<?php

namespace Icybee\Modules\Modules;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Application::class . '::lazy_get_modules' => $hooks . 'get_modules'

];
