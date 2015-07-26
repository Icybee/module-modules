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

use ICanBoogie\HTTP\Request;

/**
 * The controller flushes the `core.modules` when it is invoked so that the module list rendered
 * is always up-to-date with what is actually on disk.
 */
class ManageController extends \Icybee\Controller\BlockController
{
	protected function respond(Request $request)
	{
		$app = $this->app;

		if ($app->has_property('cache'))
		{
			$app->cache['core.modules']->clear();
		}

		return parent::respond($request);
	}
}
