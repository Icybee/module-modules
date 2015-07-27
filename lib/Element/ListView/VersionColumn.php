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

use Brickrouge\ListViewColumn;

use Icybee\Modules\Modules\ManageBlock;

/**
 * Representation of the `version` column.
 */
class VersionColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options=[])
	{
		parent::__construct($listview, $id, $options + [

				'title' => 'Version'

			]);
	}

	public function render_cell($descriptor)
	{
		$version = 'N/A';

		if (!$version)
		{
			return null;
		}

		return '<span class="small lighter">' . $version . '</span>';
	}
}
