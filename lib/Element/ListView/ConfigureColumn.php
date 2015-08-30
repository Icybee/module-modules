<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Modules\Element\ListView;

use ICanBoogie\Module\Descriptor;
use ICanBoogie\Routing\RouteNotDefined;

use Brickrouge\A;
use Brickrouge\ListViewColumn;

use Icybee\Modules\Modules\Block\ManageBlock;

/**
 * Representation of the `configure` column.
 */
class ConfigureColumn extends ListViewColumn
{
	private $routes;

	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		$this->routes = \ICanBoogie\app()->routes;

		parent::__construct($listview, $id, $options + [

			'title' => null

		]);
	}

	public function render_cell($descriptor)
	{
		$module_id = $descriptor[Descriptor::ID];

		try
		{
			$route = $this->routes["admin:$module_id:config"];

			return new A('Configure', $route->url);
		}
		catch (RouteNotDefined $e)
		{
			return null;
		}
	}
}
