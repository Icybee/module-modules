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

use Brickrouge\A;
use ICanBoogie\I18n;
use ICanBoogie\Module\Descriptor;

use Brickrouge\ListViewColumn;

use ICanBoogie\Routing\RouteNotDefined;
use Icybee\Modules\Modules\Block\ManageBlock;

/**
 * Representation of the `title` column.
 *
 * @property-read \ICanBoogie\Core|\Icybee\Binding\CoreBindings $app
 */
class TitleColumn extends ListViewColumn
{
	private $app;

	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		parent::__construct($listview, $id, $options + [

			'title' => 'Module'

		]);

		$this->app = $listview->app;
	}

	public function render_cell($descriptor)
	{
		$module_id = $descriptor[Descriptor::ID];
		$title = $descriptor['__i18n_title'];

		try
		{
			$title = new A($title, $this->app->url_for("admin:{$module_id}:index"));
		}
		catch (RouteNotDefined $e)
		{
			#
			# If the route is not defined we just display the title and not a link
			#
		}

		$html = $title;

		$description = $this->t('module_description.' . strtr($module_id, '.', '_'),  [], [

			'default' => $this->t($descriptor[Descriptor::DESCRIPTION]) ?: '<em class="light">' . $this->t('No description') . '</em>'

		]);

		if ($description)
		{
			$html .= '<div class="small">' . $description . '</div>';
		}

		return $html;
	}
}
