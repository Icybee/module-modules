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

use ICanBoogie\Module\Descriptor;

/**
 * Accessor class for the modules of the framework.
 */
class ModuleCollection extends \ICanBoogie\Module\ModuleCollection
{
	/**
	 * Disables selected modules.
	 *
	 * Modules are disabled against a list of enabled modules. The enabled modules list is made
	 * from the `enabled_modules` persistent variable and the value of the
	 * {@link Descriptor::REQUIRED} tag, which forces some modules to always be enabled.
	 */
	protected function lazy_get_index()
	{
		$index = parent::lazy_get_index();
		$enabled = \ICanBoogie\app()->vars['enabled_modules'];

		if ($enabled && is_array($enabled))
		{
			$enabled = array_flip($enabled);

			foreach ($this->descriptors as $module_id => &$descriptor)
			{
				$descriptor[Descriptor::DISABLED] = !($descriptor[Descriptor::REQUIRED] || isset($enabled[$module_id]));
			}
		}

		return $index;
	}
}
