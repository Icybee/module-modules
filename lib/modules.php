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

use ICanBoogie\Core;
use ICanBoogie\Module\Descriptor;

/**
 * Accessor class for the modules of the framework.
 */
class Modules extends \ICanBoogie\Module\ModuleCollection
{
	/**
	 * Disables selected modules.
	 *
	 * Modules are disabled against a list of enabled modules. The enabled modules list is made
	 * from the `enabled_modules` persistent variable and the value of the {@link T_REQUIRED}
	 * tag, which forces some modules to always be enabled.
	 */
	protected function lazy_get_index()
	{
		$index = parent::lazy_get_index();
		$enableds = \ICanBoogie\app()->vars['enabled_modules'];

		if ($enableds && is_array($enableds))
		{
			$enableds = array_flip($enableds);

			foreach ($this->descriptors as $module_id => &$descriptor)
			{
				$descriptor[Descriptor::DISABLED] = !($descriptor[Descriptor::REQUIRED] || isset($enableds[$module_id]));
			}
		}

		return $index;
	}
}
