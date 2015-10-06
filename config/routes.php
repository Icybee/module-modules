<?php

namespace Icybee\Modules\Modules;

use ICanBoogie\HTTP\Request;
use Icybee\Routing\RouteMaker as Make;

return Make::admin('modules', Routing\ModulesAdminController::class, [

	'only' => [ Make::ACTION_INDEX, 'inactive', 'install' ],
	'actions' => [

		'inactive' => [ '/{name}/inactive', Request::METHOD_ANY ],
		'install' => [ '/{name}/<[^/]+>/install', Request::METHOD_ANY ]

	]

]) + [

	'redirect:admin/features' => [

		'pattern' => '/admin/features',
		'location' => '/admin/modules'

	]

];
