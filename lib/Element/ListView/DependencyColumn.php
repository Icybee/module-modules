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
 *
 * @property \ICanBoogie\Core|\Icybee\Binding\CoreBindings $app
 */
class DependencyColumn extends ListViewColumn
{
	public function __construct(ManageBlock $listview, $id, array $options = [])
	{
		parent::__construct($listview, $id, $options + [

			'title' => 'Dependency'

		]);
	}

	/**
	 * Render module descriptor.
	 *
	 * @param array $descriptor
	 *
	 * @return string
	 */
	public function render_cell($descriptor)
	{
		$body =

			$this->render_module_inherits($descriptor) .
			$this->render_module_requires($descriptor) .
			$this->render_module_users($descriptor);

		return <<<EOT
<dl class="module-relations">$body</dl>
EOT;

	}

	/**
	 * Renders module inherits.
	 *
	 * @param array $descriptor
	 *
	 * @return string
	 */
	protected function render_module_inherits(array $descriptor)
	{
		$inherits = $descriptor[Descriptor::INHERITS];

		if (!$inherits)
		{
			return '';
		}

		$label = implode(' ', $this->create_modules_labels([ $inherits ]));

		return <<<EOT
<div class="module-relations-row module-relations--inherits">
	<dt>Inherits</dt>
	<dd>$label</dd>
</div>
EOT;
	}

	/**
	 * Renders module requires.
	 *
	 * @param array $descriptor
	 *
	 * @return string
	 */
	protected function render_module_requires(array $descriptor)
	{
		$requires = $descriptor[Descriptor::REQUIRES];

		if (!$requires)
		{
			return '';
		}

		$labels = implode(' ', $this->create_modules_labels($requires));

		return <<<EOT
<div class="module-relations-row module-relations--requires">
	<dt>Requires</dt>
	<dd>$labels</dd>
</div>
EOT;
	}

	/**
	 * Renders module users.
	 *
	 * @param array $descriptor
	 *
	 * @return string
	 */
	protected function render_module_users(array $descriptor)
	{
		$module_id = $descriptor[Descriptor::ID];
		$descriptors = $this->app->modules->filter_descriptors_by_users($module_id);

		if (!$descriptors)
		{
			return '';
		}

		$labels = implode(' ', $this->create_modules_labels(array_keys($descriptors)));

		return <<<EOT
<div class="module-relations-row module-relations--users">
	<dt>Used by</dt>
	<dd>$labels</dd>
</div>
EOT;
	}

	/**
	 * @param array $module_id_collection
	 *
	 * @return string
	 */
	protected function create_modules_labels(array $module_id_collection)
	{
		$labels = [];
		$modules = $this->app->modules;

		foreach ($module_id_collection as $module_id)
		{
			$label = ManageBlock::resolve_module_title($module_id);
			$label_class = isset($modules[$module_id]) ? 'success' : 'warning';

			$labels[] = <<<EOT
<span class="label label-{$label_class}">$label</span>
EOT;

		}

		return $labels;
	}
}
