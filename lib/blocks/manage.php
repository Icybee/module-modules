<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules;

use ICanBoogie\I18n;
use ICanBoogie\I18n\FormattedString;
use ICanBoogie\Operation;
use ICanBoogie\Route;

use Brickrouge\A;
use Brickrouge\Button;
use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\ListView;

use Icybee\Element\ActionbarToolbar;

class ManageBlock extends ListView
{
	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(\Icybee\ASSETS . 'css/manage.css');
		$document->css->add(DIR . 'public/admin.css');
	}

	protected $module;

	public function __construct(Module $module, array $attributes=array())
	{
		global $core;

		$this->module = $module;

		if (!$core->user->has_permission(Module::PERMISSION_ADMINISTER, $module))
		{
			throw new HTTPException("You don't have permission to administer modules.", array(), 403);
		}

		parent::__construct
		(
			$attributes + array
			(
				self::ENTRIES => array_values($core->modules->enabled_modules_descriptors),
				self::COLUMNS => array
				(
					'key' =>        __CLASS__ . '\KeyColumn',
					'title' =>      __CLASS__ . '\TitleColumn',
					'version' =>    __CLASS__ . '\VersionColumn',
					'dependency' => __CLASS__ . '\DependencyColumn',
					'install' =>    __CLASS__ . '\InstallColumn',
					'configure' =>  __CLASS__ . '\ConfigureColumn'
				),

				'class' => 'form-primary'
			)
		);

		$this->attach_buttons();
	}

	/**
	 * The title and the category of the entries (descriptors) are translated and stored under
	 * `__i18n_title` and `__i18n_category` respectively. The entries (descriptors) are ordered
	 * according to their translated title.
	 */
	protected function get_entries()
	{
		$entries = parent::get_entries();

		foreach ($entries as &$descriptor)
		{
			$descriptor['__i18n_title'] = self::resolve_module_title($descriptor[Module::T_ID]);
			$descriptor['__i18n_category'] = self::translate_module_category($descriptor);
		}

		unset($descriptor);

		usort($entries, function($a, $b) {

			return \ICanBoogie\unaccent_compare_ci($a['__i18n_title'], $b['__i18n_title']);

		});

		return $entries;
	}

	protected function render_rows(array $rows)
	{
		$rendered_rows = parent::render_rows($rows);
		$entries = $this->entries;
		$grouped = array();

		foreach ($rendered_rows as $i => $row)
		{
			$descriptor = $entries[$i];
			$grouped[$descriptor['__i18n_category']][$i] = $row;
		}

		uksort($grouped, 'ICanBoogie\unaccent_compare_ci');

		$span = count($rendered_rows) - 2;
		$rendered_rows = array();

		foreach ($grouped as $group_title => $rows)
		{
			$rendered_rows[] = <<<EOT
<tr class="listview-divider">
	<td>&nbsp;</td>
	<td>$group_title</td>
	<td colspan="$span">&nbsp;</td>
</tr>
EOT;
			foreach ($rows as $row)
			{
				$rendered_rows[] = $row;
			}
		}

		return $rendered_rows;
	}

	protected function decorate($html)
	{
		$operation_destination_name = \ICanBoogie\Operation::DESTINATION;
		$operation_destination_value = $this->module->id;
		$operation_name = \ICanBoogie\Operation::NAME;
		// TODO-20130702: Fix the following hack:
		$operation_value = ($this instanceof InactivesBlock) ? Module::OPERATION_ACTIVATE : Module::OPERATION_DEACTIVATE;

		return <<<EOT
<form action="" method="POST" class="form-primary">
	<input type="hidden" name="{$operation_destination_name}" value="$operation_destination_value" />
	<input type="hidden" name="{$operation_name}" value="$operation_value" />

	$html
</form>
EOT;
	}

	protected function attach_buttons()
	{
		global $core;

		$core->events->attach(function(ActionbarToolbar\CollectEvent $event, ActionbarToolbar $target) {

			$event->buttons[] = new Button
			(
				'Disable selected modules', array
				(
					'class' => 'btn-primary btn-danger',
					'type' => 'submit',
					'data-target' => '.form-primary'
				)
			);

		});
	}

	static public function resolve_module_title($module_id)
	{
		global $core;

		return I18n\t
		(
			'module_title.' . strtr($module_id, '.', '_'), array(), array
			(
				'default' => isset($core->modules->descriptors[$module_id]) ? $core->modules->descriptors[$module_id][Module::T_TITLE] : $module_id
			)
		);
	}

	static public function translate_module_category(array $descriptor)
	{
		$category = $descriptor[Module::T_CATEGORY];

		if (!$category)
		{
			list($category) = explode('.', $descriptor[Module::T_ID]);
		}

		return I18n\t($category, array(), array('scope' => 'module_category', 'default' => ucfirst($category)));
	}
}

namespace Icybee\Modules\Modules\ManageBlock;

use ICanBoogie\I18n;
use ICanBoogie\Module;
use ICanBoogie\Operation;

use Brickrouge\A;
use Brickrouge\Element;
use Brickrouge\ListViewColumn;

use Icybee\Modules\Modules\ManageBlock;

/**
 * Representation of the `key` column.
 */
class KeyColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=array())
	{
		parent::__construct
		(
			$listview, $id, $options + array
			(
				'title' => null
			)
		);
	}

	public function render_cell($descriptor)
	{
		global $core;

		$module_id = $descriptor[Module::T_ID];
		$disabled = $descriptor[Module::T_REQUIRED];

		if ($core->modules->usage($module_id))
		{
			$disabled = true;
		}

		return new Element
		(
			Element::TYPE_CHECKBOX, array
			(
				'name' => Operation::KEY . '[' . $module_id . ']',
				'disabled' => $disabled
			)
		);
	}
}

/**
 * Representation of the `title` column.
 */
class TitleColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=array())
	{
		parent::__construct
		(
			$listview, $id, $options + array
			(
				'title' => 'Module'
			)
		);
	}

	public function render_cell($descriptor)
	{
		$module_id = $descriptor[Module::T_ID];
		$title = $descriptor['__i18n_title'];

		$html = \ICanBoogie\Routes::get()->find('/admin/' . $module_id) ? '<a href="' . \ICanBoogie\Routing\contextualize('/admin/' . $module_id) . '">' . $title . '</a>' : $title;

		$description = I18n\t
		(
			'module_description.' . strtr($module_id, '.', '_'), array(), array
			(
				'default' => I18n\t($descriptor[Module::T_DESCRIPTION]) ?: '<em class="light">' . I18n\t('No description') . '</em>'
			)
		);

		if ($description)
		{
			$html .= '<div class="small">' . $description . '</div>';
		}

		return $html;
	}
}

/**
 * Representation of the `version` column.
 */
class VersionColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=array())
	{
		parent::__construct
		(
			$listview, $id, $options + array
			(
				'title' => 'Version'
			)
		);
	}

	public function render_cell($descriptor)
	{
		$version = $descriptor[Module::T_VERSION];

		if (!$version)
		{
			return;
		}

		return '<span class="small lighter">' . $version . '</span>';
	}
}

/**
 * Representation of the `dependency` column.
 */
class DependencyColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=array())
	{
		parent::__construct
		(
			$listview, $id, $options + array
			(
				'title' => 'Dependency'
			)
		);
	}

	public function render_cell($descriptor)
	{
		global $core;

		$html = '';
		$extends = $descriptor[Module::T_EXTENDS];
		$module_id = $descriptor[Module::T_ID];

		if ($extends)
		{
			$label = ManageBlock::resolve_module_title($extends);
			$class = isset($core->modules[$extends]) ? 'success' : 'warning';

			$html .= '<div class="extends">Extends: ';
			$html .= '<span class="label label-' . $class . '">' . $label . '</span>';
			$html .= '</div>';
		}

		$requires = $descriptor[Module::T_REQUIRES];

		if ($requires)
		{
			$html .= '<div class="requires">Requires: ';

			foreach ($requires as $require_id => $version)
			{
				$label = ManageBlock::resolve_module_title($require_id);
				$label_class = isset($core->modules[$require_id]) ? 'success' : 'warning';

				$html .= <<<EOT
<span class="label label-{$label_class}" title="Version $version">$label</span>
EOT;

				$html .= ' ';
			}

			$html .= '</div>';
		}

		$usage = $core->modules->usage($module_id);

		if ($usage)
		{
			$html .= '<div class="usage light">' . I18n\t('Used by :count modules', array(':count' => $usage)) . '</div>';
		}

		return $html;
	}
}

/**
 * Representation of the `install` column.
 */
class InstallColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=array())
	{
		parent::__construct
		(
			$listview, $id, $options + array
			(
				'title' => 'Installed'
			)
		);
	}

	public function render_cell($descriptor)
	{
		global $core;

		$module_id = $descriptor[Module::T_ID];

		try
		{
			$module = $core->modules[$module_id];
		}
		catch (\Exception $e)
		{
			return '<div class="alert alert-error">' . $e->getMessage() . '</div>';
		}

		$html = '';
		$is_installed = false;

		# EXTENDS

		$errors = new \ICanBoogie\Errors;
		$extends_errors = new \ICanBoogie\Errors;
		$n_errors = count($errors);

		while ($descriptor[Module::T_EXTENDS])
		{
			$extends = $descriptor[Module::T_EXTENDS];

			if (empty($core->modules->descriptors[$extends]))
			{
				$errors[$module_id] = I18n\t('Requires the %module module which is missing.', array('%module' => $extends));

				break;
			}
			else if (!isset($core->modules[$extends]))
			{
				$errors[$module_id] = I18n\t('Requires the %module module which is disabled.', array('%module' => $extends));

				break;
			}
			else
			{
				$extends_errors->clear();
				$extends_module = $core->modules[$extends];
				$extends_is_installed = $extends_module->is_installed($extends_errors);

				if (count($extends_errors))
				{
					$extends_is_installed = false;
				}

				if (!$extends_is_installed)
				{
					$errors[$module_id] = I18n\t('Requires the %module module which is disabled.', array('%module' => $extends));

					break;
				}
			}

			$descriptor = $core->modules->descriptors[$extends];
		}

		if ($n_errors != count($errors))
		{
			$html .= '<div class="alert alert-error">' . implode('<br />', (array) $errors[$module_id]) . '</div>';
		}
		else
		{
			try
			{
				$n_errors = count($errors);
				$is_installed = $module->is_installed($errors);

				if (count($errors) != $n_errors)
				{
					$is_installed = false;
				}
			}
			catch (\Exception $e)
			{
				$errors[$module->id] = I18n\t
				(
					'Exception with module %module: :message', array
					(
						'%module' => (string) $module,
						':message' => $e->getMessage()
					)
				);
			}

			if ($is_installed)
			{
				$html .= I18n\t('Installed');
			}
			else if ($is_installed === false)
			{
				$btn = '<a class="btn btn-danger" href="'
				. \ICanBoogie\Routing\contextualize("/admin/{$this->module}/{$module}/install")
				. '">' . I18n\t('Install module') . '</a>';

				$title = new FormattedString('The module %title is not properly installed', array('title' => $module->title));

				\ICanBoogie\log_error("$title.");

				if (isset($errors[$module_id]))
				{
					$error = $errors[$module_id];

					if (is_array($error))
					{
						$error = implode('</p><p>', $error);
					}

					$html .= <<<EOT
<div class="alert alert-error alert-block undissmissable">
	<h4 class="alert-heading">$title</h4>
	<div class="content">
		<p>$error</p>
	</div>

	<div class="alert-actions">$btn</div>
</div>
EOT;
				}
				else
				{
					$html .= $btn;
				}
			}
			else // null
			{
				$html .= '<em class="not-applicable light">Not applicable</em>';
			}
		}

		return $html;
	}
}

/**
 * Representation of the `configure` column.
 */
class ConfigureColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=array())
	{
		parent::__construct
		(
			$listview, $id, $options + array
			(
				'title' => null
			)
		);
	}

	public function render_cell($descriptor)
	{
		global $core;

		$module_id = $descriptor[Module::T_ID];

		if (empty($core->routes["admin:$module_id/config"]))
		{
			return;
		}

		$route = $core->routes["admin:$module_id/config"];

		return new A('Configure', \ICanBoogie\Routing\contextualize($route->pattern));
	}
}