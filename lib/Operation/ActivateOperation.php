<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules\Operation;

use ICanBoogie\Errors;
use ICanBoogie\Operation;

use Icybee\Binding\Core\PrototypedBindings;
use Icybee\Modules\Modules\Module;

/**
 * Activates the specified modules.
 */
class ActivateOperation extends Operation
{
	use PrototypedBindings;

	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER

		] + parent::get_controls();
	}

	protected function validate(Errors $errors)
	{
		$app = $this->app;
		$install_errors = new Errors;

		foreach ((array) $this->key as $key => $dummy)
		{
			try
			{
				$app->modules[$key] = true;
				$module = $app->modules[$key];
				$install_errors->clear();
				$rc = $module->is_installed($install_errors);

				if (!$rc || count($install_errors))
				{
					$module->install($errors);

					\ICanBoogie\log_success('The module %title was installed.', [ 'title' => $module->title ]);
				}

				$enabled[$key] = true;
			}
			catch (\Exception $e)
			{
				$app->modules[$key] = false;
				$errors[] = $e->getMessage();
			}
		}

		return !count($errors);
	}

	protected function process()
	{
		$app = $this->app;

		$enabled = array_keys($app->modules->enabled_modules_descriptors);
		$enabled = array_flip($enabled);

		foreach ((array) $this->key as $key => $dummy)
		{
			$enabled[$key] = true;
		}

		$app->vars['enabled_modules'] = array_keys($enabled);

		$this->response->location = $app->url_for('admin:modules:index');

		return true;
	}
}
