<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules\Block;

use ICanBoogie\HTTP\PermissionRequired;
use ICanBoogie\I18n;
use ICanBoogie\Module\Descriptor;
use ICanBoogie\Operation;

use Brickrouge\Button;
use Brickrouge\ListView;

use ICanBoogie\PrototypeTrait;
use Icybee\Element\ActionBarToolbar;
use Icybee\Modules\Modules\Module;
use Icybee\Modules\Modules\Element\ListView as Columns;

/**
 * @property \Icybee\Binding\CoreBindings $app
 */
class ManageBlock extends ListView
{
	static protected function add_assets(\Brickrouge\Document $document)
	{
		parent::add_assets($document);

		$document->css->add(\Icybee\ASSETS . 'css/manage.css');
		$document->css->add(\Icybee\Modules\Modules\DIR . 'public/admin.css');
	}

	protected $module;

	public function __construct(Module $module, array $attributes = [])
	{
		$app = $this->app;
		$this->module = $module;

		if (!$app->user->has_permission(Module::PERMISSION_ADMINISTER, $module))
		{
			throw new PermissionRequired("You don't have permission to administer modules.");
		}

		parent::__construct($attributes + [

			self::RECORDS => array_values($app->modules->enabled_modules_descriptors),
			self::COLUMNS => [

				'key' =>        Columns\KeyColumn::class,
				'title' =>      Columns\TitleColumn::class,
				'dependency' => Columns\DependencyColumn::class,
				'install' =>    Columns\InstallColumn::class,
				'configure' =>  Columns\ConfigureColumn::class

			],

			'class' => 'form-primary'
		]);

		$this->attach_buttons();
	}

	/**
	 * The title and the category of the entries (descriptors) are translated and stored under
	 * `__i18n_title` and `__i18n_category` respectively. The entries (descriptors) are ordered
	 * according to their translated title.
	 */
	protected function get_records()
	{
		$entries = parent::get_records();

		foreach ($entries as &$descriptor)
		{
			$descriptor['__i18n_title'] = self::resolve_module_title($descriptor[Descriptor::ID]);
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
		$entries = $this->records;
		$grouped = [];

		foreach ($rendered_rows as $i => $row)
		{
			$descriptor = $entries[$i];
			$grouped[$descriptor['__i18n_category']][$i] = $row;
		}

		uksort($grouped, 'ICanBoogie\unaccent_compare_ci');

		$span = count($this->columns) - 2;
		$rendered_rows = [];

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
		$operation_destination_name = Operation::DESTINATION;
		$operation_destination_value = $this->module->id;
		$operation_name = Operation::NAME;
		// TODO-20130702: Fix the following hack:
		$operation_value = ($this instanceof InactiveBlock) ? Module::OPERATION_ACTIVATE : Module::OPERATION_DEACTIVATE;

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
		$this->app->events->attach(function(ActionBarToolbar\CollectEvent $event, ActionBarToolbar $target) {

			$event->buttons[] = new Button('Disable selected modules', [

				'class' => 'btn-primary btn-danger',
				'type' => 'submit',
				'data-target' => '.form-primary'
			]);

		});
	}

	static public function resolve_module_title($module_id)
	{
		$app = self::app();

		return $app->translate('module_title.' . strtr($module_id, '.', '_'),  [], [

			'default' => isset($app->modules->descriptors[$module_id]) ? $app->modules->descriptors[$module_id][Descriptor::TITLE] : $module_id

		]);
	}

	static public function translate_module_category(array $descriptor)
	{
		$category = $descriptor[Descriptor::CATEGORY];

		if (!$category)
		{
			list($category) = explode('.', $descriptor[Descriptor::ID]);
		}

		return self::app()->translate($category, [], [ 'scope' => 'module_category', 'default' => ucfirst($category) ]);
	}

	/**
	 * @return \ICanBoogie\Core|\Icybee\Binding\CoreBindings
	 */
	static private function app()
	{
		return \ICanBoogie\app();
	}
}
