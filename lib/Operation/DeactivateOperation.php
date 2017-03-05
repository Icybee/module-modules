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

use ICanBoogie\Binding\PrototypedBindings;
use ICanBoogie\ErrorCollection;
use ICanBoogie\Operation;

use Icybee\Modules\Modules\Module;

/**
 * Deactivates the specified modules.
 */
class DeactivateOperation extends Operation
{
	use PrototypedBindings;

	/**
	 * @inheritdoc
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER

		] + parent::get_controls();
	}

	/**
	 * Only modules which are not used by other modules can be disabled.
	 *
	 * @inheritdoc
	 */
	protected function validate(ErrorCollection $errors)
	{
		$app = $this->app;

		if ($this->key)
		{
			foreach (array_keys($this->key) as $module_id)
			{
				$n = $app->modules->usage($module_id);

				if ($n)
				{
					$errors->add_generic("The module %title cannot be disabled, :count modules are using it.", [

						'title' => $this->format($module_id, [], [ 'scope' => 'module_title' ]),
						':count' => $n

					]);
				}
			}
		}

		return $errors;
	}

	/**
	 * @inheritdoc
	 */
	protected function process()
	{
		$app = $this->app;
		$modules = $app->modules;

		$enabled = array_keys($modules->enabled_modules_descriptors);
		$enabled = array_combine($enabled, $enabled);

		if ($this->key)
		{
			foreach (array_keys($this->key) as $key)
			{
				unset($enabled[$key]);
				unset($modules[$key]);
			}
		}

		$app->vars['enabled_modules'] = array_values($enabled);

		$this->response->location = $app->url_for('admin:modules:index');

		return true;
	}
}
