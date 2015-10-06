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

use ICanBoogie\Errors;
use ICanBoogie\HTTP\Request;

use Icybee\Binding\PrototypedBindings;
use Icybee\Routing\AdminController;

class ModulesAdminController extends AdminController
{
	use PrototypedBindings;
	use \ICanBoogie\Module\CoreBindings;

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
	 * @param $module_id
	 *
	 * @return \ICanBoogie\HTTP\RedirectResponse
	 * @throws \Exception
	 */
	protected function action_install($module_id)
	{
		$module = $this->modules[$module_id];
		$errors = new Errors;

		if (!$module->install($errors)) {
			throw new \Exception("Unable to install $module_id");
		}

		return $this->redirect($this->request->referer);
	}

	protected function action_inactive()
	{
		$this->view->content = $this->module->getBlock('inactive');
		$this->view['block_name'] = 'inactive';
	}
}
