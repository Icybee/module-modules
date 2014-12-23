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

use ICanBoogie\Operation;

use Brickrouge\Button;
use Brickrouge\Element;

use Icybee\Element\ActionbarToolbar;

class InactivesBlock extends ManageBlock
{
	public function __construct(Module $module, array $attributes=array())
	{
		parent::__construct
		(
			$module, $attributes + array
			(
				self::ENTRIES => $this->app->modules->disabled_modules_descriptors,
				self::COLUMNS => array
				(
					'key' =>        __NAMESPACE__ . '\ManageBlock\KeyColumn',
					'title' =>      __NAMESPACE__ . '\ManageBlock\TitleColumn',
					'version' =>    __NAMESPACE__ . '\ManageBlock\VersionColumn',
					'dependency' => __NAMESPACE__ . '\ManageBlock\DependencyColumn'
				)
			)
		);
	}

	protected function attach_buttons()
	{
		$this->app->events->attach(function(ActionbarToolbar\CollectEvent $event, ActionbarToolbar $target) {

			$event->buttons[] = new Button
			(
				'Enable selected modules', array
				(
					'class' => 'btn-primary btn-danger',
					'type' => 'submit',
					'data-target' => '.form-primary'
				)
			);

		});
	}
}
