<?php

namespace Icybee\Modules\Modules;

$hooks = Hooks::class . '::';

return [

	'prototypes' => [

		'ICanBoogie\Core::lazy_get_modules' => $hooks . 'get_modules'

	]

];
