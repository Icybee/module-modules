<?php

namespace Icybee\Modules\Modules;

$hooks = __NAMESPACE__ . '\Hooks::';

return [

	'prototypes' => [

		'ICanBoogie\Core::lazy_get_modules' => $hooks . 'get_modules'

	]

];