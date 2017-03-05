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

use ICanBoogie\Application;

class Hooks
{
	/**
	 * Override the method to provide our own accessor.
	 *
	 * @param Application $app
	 *
	 * @return ModuleCollection
	 */
	static public function get_modules(Application $app)
	{
		$config = $app->config;

		return new ModuleCollection($config['module-path'], $config['cache modules'] ? $app->vars : null);
	}
}
