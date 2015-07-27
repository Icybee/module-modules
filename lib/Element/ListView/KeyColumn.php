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
use ICanBoogie\Operation;

use Brickrouge\ListViewColumn;

use Icybee\Modules\Modules\ManageBlock;
use Icybee\WrappedCheckbox;

/**
 * Representation of the `key` column.
 */
class KeyColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		parent::__construct($listview, $id, $options + [

			'title' => null

		]);
	}

	public function render_cell($descriptor)
	{
		$module_id = $descriptor[Descriptor::ID];
		$disabled = $descriptor[Descriptor::REQUIRED];

		if (\ICanBoogie\app()->modules->usage($module_id))
		{
			$disabled = true;
		}

		return new WrappedCheckbox([

			'name' => Operation::KEY . '[' . $module_id . ']',
			'disabled' => $disabled

		]);
	}
}
