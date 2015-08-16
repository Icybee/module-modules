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

use Brickrouge\Alert;
use Brickrouge\Button;

use Icybee\Element\ActionbarToolbar;
use Icybee\Modules\Editor\Collection;
use Icybee\Modules\Modules\Element\ListView as Columns;

class InactiveBlock extends ManageBlock
{
	public function __construct(Module $module, array $attributes=[])
	{
		parent::__construct($module, $attributes + [

			self::ENTRIES => $this->app->modules->disabled_modules_descriptors,
			self::COLUMNS => [

				'key' =>        Columns\KeyColumn::class,
				'title' =>      Columns\TitleColumn::class,
				'dependency' => Columns\DependencyColumn::class

			]
		]);
	}

	protected function render_inner_html()
	{
		if (!$this->entries)
		{
			return new Alert("All modules are active.", [

				Alert::CONTEXT => Alert::CONTEXT_INFO,
				Alert::UNDISMISSABLE => true,

				'class' => 'alert-block'

			]);
		}

		return parent::render_inner_html();
	}

	protected function attach_buttons()
	{
		if (!$this->entries)
		{
			return;
		}

		$this->app->events->attach(function(ActionbarToolbar\CollectEvent $event, ActionbarToolbar $target) {

			$event->buttons[] = new Button('Enable selected modules', [

				'class' => 'btn-primary btn-danger',
				'type' => 'submit',
				'data-target' => '.form-primary'

			]);

		});
	}
}