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

/**
 * Deactivates the specified modules.
 */
class DeactivateOperation extends \ICanBoogie\Operation
{
	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER

		] + parent::get_controls();
	}

	/**
	 * Only modules which are not used by other modules can be disabled.
	 */
	protected function validate(\ICanboogie\Errors $errors)
	{
		$app = $this->app;

		if ($this->key)
		{
			foreach (array_keys($this->key) as $module_id)
			{
				$n = $app->modules->usage($module_id);

				if ($n)
				{
					$errors[] = $errors->format('The module %title cannot be disabled, :count modules are using it.', [

						'title' => I18n\t($module_id, [], [ 'scope' => 'module_title' ]),
						':count' => $n

					]);
				}
			}
		}

		return $errors;
	}

	protected function process()
	{
		$app = $this->app;

		$enabled = array_keys($app->modules->enabled_modules_descriptors);
		$enabled = array_combine($enabled, $enabled);

		if ($this->key)
		{
			foreach (array_keys($this->key) as $key)
			{
				unset($enabled[$key]);
				unset($app->modules[$key]);
			}
		}

		$app->vars['enabled_modules'] = array_values($enabled);

		$this->response->location = \ICanBoogie\Routing\contextualize('/admin/' . $this->module->id);

		return true;
	}
}
