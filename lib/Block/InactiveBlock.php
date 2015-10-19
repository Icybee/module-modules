<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules\Block;

use ICanBoogie\Operation;

use Brickrouge\Alert;
use Brickrouge\Button;

use Icybee\Element\ActionBarToolbar;
use Icybee\Modules\Editor\Collection;
use Icybee\Modules\Modules\Module;
use Icybee\Modules\Modules\Element\ListView as Columns;

class InactiveBlock extends ManageBlock
{
	public function __construct(Module $module, array $attributes = [])
	{
		parent::__construct($module, $attributes + [

			self::RECORDS => $this->app->modules->disabled_modules_descriptors,
			self::COLUMNS => [

				'key' =>        Columns\KeyColumn::class,
				'title' =>      Columns\TitleColumn::class,
				'dependency' => Columns\DependencyColumn::class

			]
		]);
	}

	/**
	 * Renders a notice when there is no record to render.
	 *
	 * @return Alert
	 */
	protected function render_no_records()
	{
		return new Alert("All modules are active.", [

			Alert::UNDISMISSABLE => true,
			Alert::CONTEXT => Alert::CONTEXT_INFO,

			'class' => 'alert alert-block listview-alert'

		]);
	}

	protected function attach_buttons()
	{
		if (!$this->records)
		{
			return;
		}

		$this->app->events->attach(function(ActionBarToolbar\CollectEvent $event, ActionBarToolbar $target) {

			$event->buttons[] = new Button('Enable selected modules', [

				'class' => 'btn-primary btn-danger',
				'type' => 'submit',
				'data-target' => '.form-primary'

			]);

		});
	}
}
