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
 * Representation of the `dependency` column.
 */
class DependencyColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		parent::__construct($listview, $id, $options + [

			'title' => 'Dependency'

		]);
	}

	public function render_cell($descriptor)
	{
		$html = '';
		$extends = $descriptor[Descriptor::INHERITS];
		$module_id = $descriptor[Descriptor::ID];
		$app = \ICanBoogie\app();

		if ($extends)
		{
			$label = ManageBlock::resolve_module_title($extends);
			$class = isset($app->modules[$extends]) ? 'success' : 'warning';

			$html .= '<div class="extends">Extends: ';
			$html .= '<span class="label label-' . $class . '">' . $label . '</span>';
			$html .= '</div>';
		}

		$requires = $descriptor[Descriptor::REQUIRES];

		if ($requires)
		{
			$html .= '<div class="requires">Requires: ';

			foreach ($requires as $require_id => $version)
			{
				$label = ManageBlock::resolve_module_title($require_id);
				$label_class = isset($app->modules[$require_id]) ? 'success' : 'warning';

				$html .= <<<EOT
<span class="label label-{$label_class}" title="Version $version">$label</span>
EOT;

				$html .= ' ';
			}

			$html .= '</div>';
		}

		$usage = $app->modules->usage($module_id);

		if ($usage)
		{
			$html .= '<div class="usage light">' . I18n\t('Used by :count modules', [ ':count' => $usage ]) . '</div>';
		}

		return $html;
	}
}
