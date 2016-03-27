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

class Module extends \Icybee\Module
{
	const OPERATION_ACTIVATE = 'activate';
	const OPERATION_DEACTIVATE = 'deactivate';

	protected function block_install($module_id)
	{
		$app = $this->app;

		if (!$app->user->has_permission(self::PERMISSION_ADMINISTER, $this))
		{
			return '<div class="alert alert-danger">' . $app->translate('You don\'t have enought privileges to install packages.') . '</div>';
		}

		if (empty($app->modules[$module_id]))
		{
			return '<div class="alert alert-danger">' . $app->translate('The module %module_id does not exists.', [ '%module_id' => $module_id ]) . '</div>';
		}

		$errors = new \ICanBoogie\ErrorCollection;
		$module = $app->modules[$module_id];

		$is_installed = $module->is_installed($errors);

		if ($is_installed && !count($errors))
		{
			return '<div class="alert alert-danger">' . $app->translate('The module %module is already installed', [ '%module' => $module_id ]) . '</div>';
		}

		$errors->clear();
		$is_installed = $module->install($errors);

		if (!$is_installed || count($errors))
		{
			return '<div class="alert alert-danger">' . $app->translate('Unable to install the module %module', [ '%module' => $module_id ]) . '</div>';
		}

		return '<div class="alert alert-success">' . $app->translate('The module %module has been installed. <a href="' . $app->site->path . '/admin/' . $this . '">Retourner à la liste.</a>', [ '%module' => $module_id ]) . '</div>';
	}
}
