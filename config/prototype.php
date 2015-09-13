<?php

namespace Icybee\Modules\Modules;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Core::class . '::lazy_get_modules' => $hooks . 'get_modules'

];
