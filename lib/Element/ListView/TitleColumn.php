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

use ICanBoogie\I18n;
use ICanBoogie\Module\Descriptor;

use Brickrouge\ListViewColumn;

use Icybee\Modules\Modules\ManageBlock;

/**
 * Representation of the `title` column.
 */
class TitleColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		parent::__construct($listview, $id, $options + [

			'title' => 'Module'

		]);
	}

	public function render_cell($descriptor)
	{
		$module_id = $descriptor[Descriptor::ID];
		$title = $descriptor['__i18n_title'];

		$html = \ICanBoogie\app()->routes->find('/admin/' . $module_id) ? '<a href="' . \ICanBoogie\Routing\contextualize('/admin/' . $module_id) . '">' . $title . '</a>' : $title;

		$description = I18n\t('module_description.' . strtr($module_id, '.', '_'),  [], [

			'default' => I18n\t($descriptor[Descriptor::DESCRIPTION]) ?: '<em class="light">' . I18n\t('No description') . '</em>'

		]);

		if ($description)
		{
			$html .= '<div class="small">' . $description . '</div>';
		}

		return $html;
	}
}
