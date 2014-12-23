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

/**
 * Activates the specified modules.
 */
class ActivateOperation extends \ICanBoogie\Operation
{
	protected function get_controls()
	{
		return array
		(
			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER
		)

		+ parent::get_controls();
	}

	protected function validate(\ICanBoogie\Errors $errors)
	{
		$app = $this->app;
		$install_errors = new \ICanBoogie\Errors;

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

					\ICanBoogie\log_success('The module %title was installed.', array('title' => $module->title));
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

		$this->response->location = \ICanBoogie\Routing\contextualize('/admin/' . (string) $this->module);

		return true;
	}
}
