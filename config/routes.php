<?php

namespace Icybee\Modules\Modules;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Routing\RouteDefinition;

use Icybee\Routing\RouteMaker as Make;

return Make::admin('modules', Routing\ModulesAdminController::class, [

	Make::OPTION_ONLY => [ 'active', 'inactive', 'install' ],
	Make::OPTION_ACTIONS => [

		'active' => [ '/{name}/active', Request::METHOD_ANY ],
		'inactive' => [ '/{name}/inactive', Request::METHOD_ANY ],
		'install' => [ '/{name}/<[^/]+>/install', Request::METHOD_ANY ]

	]

]) + [

	'admin:modules:index' => [

		RouteDefinition::PATTERN => '/admin/modules',
		RouteDefinition::LOCATION => '/admin/modules/active'

	],

	'redirect:admin/features' => [

		RouteDefinition::PATTERN => '/admin/features',
		RouteDefinition::LOCATION => '/admin/modules/active'

	]

];
