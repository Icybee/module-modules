<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules\Element\ListView;

use ICanBoogie\I18n;
use ICanBoogie\Module\Descriptor;

use Brickrouge\Alert;
use Brickrouge\ListViewColumn;

use Icybee\Modules\Modules\ManageBlock;

/**
 * Representation of the `install` column.
 */
class InstallColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		parent::__construct($listview, $id, $options + [

			'title' => 'Installed'

		]);
	}

	public function render_cell($descriptor)
	{
		$app = \ICanBoogie\app();
		$module_id = $descriptor[Descriptor::ID];

		try
		{
			$module = $app->modules[$module_id];
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

		while ($descriptor[Descriptor::INHERITS])
		{
			$extends = $descriptor[Descriptor::INHERITS];

			if (empty($app->modules->descriptors[$extends]))
			{
				$errors[$module_id] = $errors->format('Requires the %module module which is missing.', [ '%module' => $extends ]);

				break;
			}
			else if (!isset($app->modules[$extends]))
			{
				$errors[$module_id] = $errors->format('Requires the %module module which is disabled.', [ '%module' => $extends ]);

				break;
			}
			else
			{
				$extends_errors->clear();
				$extends_module = $app->modules[$extends];
				$extends_is_installed = $extends_module->is_installed($extends_errors);

				if (count($extends_errors))
				{
					$extends_is_installed = false;
				}

				if (!$extends_is_installed)
				{
					$errors[$module_id] = $errors->format('Requires the %module module which is disabled.', [ '%module' => $extends ]);

					break;
				}
			}

			$descriptor = $app->modules->descriptors[$extends];
		}

		if ($n_errors != count($errors))
		{
			$html .= new Alert($errors[$module_id], [

				Alert::UNDISMISSABLE => true

			]);
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
				$errors[$module->id] = $errors->format('Exception with module %module: :message', [

					'%module' => (string) $module,
					':message' => $e->getMessage()

				]);
			}

			if ($is_installed)
			{
				$html .= $this->t('Installed');
			}
			else if ($is_installed === false)
			{
				$btn = '<a class="btn btn-danger" href="'
					. \ICanBoogie\Routing\contextualize("/admin/modules/{$module}/install")
					. '">' . $this->t('Install module') . '</a>';

				$title = $this->t('The module %title is not properly installed', [ 'title' => $module->title ]);

				\ICanBoogie\log_error("$title.");

				if (isset($errors[$module_id]))
				{
					$error = $errors[$module_id];

					if (is_array($error))
					{
						$error = '– ' . implode('</p><p>– ', $error);
					}
					else
					{
						$error = '– ' . $error;
					}

					$html .= <<<EOT
<div class="alert alert-error alert-block undismissable">
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
