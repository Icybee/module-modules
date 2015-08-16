<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules\Routing;

use ICanBoogie\HTTP\Request;

use Icybee\Binding\ObjectBindings;
use Icybee\Routing\AdminController;

class ModulesAdminController extends AdminController
{
	use ObjectBindings;

	/**
	 * Clears module cache before doing anything.
	 *
	 * @inheritdoc
	 */
	protected function action(Request $request)
	{
		$app = $this->app;

		if ($app->has_property('cache'))
		{
			$app->cache['core.modules']->clear();
		}

		return parent::action($request);
	}

	/**
	 * @inheritdoc
	 */
	protected function is_action_method($action)
	{
		if (in_array($action, [ 'inactive' ]))
		{
			return true;
		}

		return parent::is_action_method($action);
	}

	protected function inactive()
	{
		$this->view->content = $this->module->getBlock('inactive');
		$this->view['block_name'] = 'inactive';
	}
}
