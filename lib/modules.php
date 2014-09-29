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

use ICanBoogie\Module;

/**
 * Accessor class for the modules of the framework.
 */
class Modules extends \ICanBoogie\Module\Modules
{
	/**
	 * Disables selected modules.
	 *
	 * Modules are disabled againts a list of enabled modules. The enabled modules list is made
	 * from the `enabled_modules` persistant variable and the value of the {@link T_REQUIRED}
	 * tag, which forces some modules to always be enabled.
	 */
	protected function lazy_get_index()
	{
		global $core;

		$index = parent::lazy_get_index();
		$enableds = $core->vars['enabled_modules'];

		if ($enableds && is_array($enableds))
		{
			$enableds = array_flip($enableds);

			foreach ($this->descriptors as $module_id => &$descriptor)
			{
				$descriptor[Module::T_DISABLED] = !($descriptor[Module::T_REQUIRED] || isset($enableds[$module_id]));
			}
		}

		return $index;
	}
}