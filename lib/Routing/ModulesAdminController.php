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

use ICanBoogie\Binding\PrototypedBindings;
use ICanBoogie\ErrorCollection;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Module;

use Icybee\Routing\AdminController;

class ModulesAdminController extends AdminController
{
	use PrototypedBindings;
	use Module\ApplicationBindings;

	/**
	 * Clears module cache before doing anything.
	 *
	 * @inheritdoc
	 */
	protected function action(Request $request)
	{
		try
		{
			$this->app->caches['core.modules']->clear();
		}
		catch (\Exception $e)
		{
			#
			# Not important
			#
		}

		return parent::action($request);
	}

	/**
	 * @param $module_id
	 *
	 * @return \ICanBoogie\HTTP\RedirectResponse
	 *
	 * @throws \Exception
	 */
	protected function action_install($module_id)
	{
		$module = $this->modules[$module_id];
		$errors = new ErrorCollection;

		if (!$module->install($errors)) {
			throw new \Exception("Unable to install $module_id");
		}

		return $this->redirect($this->request->referer);
	}

	protected function action_index()
	{
		$this->view->content = $this->module->getBlock('manage');
		$this->view['block_name'] = 'manage';
	}
}
