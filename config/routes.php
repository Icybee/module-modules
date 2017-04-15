<?php

namespace Icybee\Modules\Modules;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Routing\RouteDefinition;

use Icybee\Routing\RouteMaker as Make;

return Make::admin('modules', Routing\ModulesAdminController::class, [

	Make::OPTION_ONLY => [ 'index', 'install' ],
	Make::OPTION_ACTIONS => [

		'install' => [ '/{name}/:module_id/install', Request::METHOD_ANY ]

	]

]) + [

	'redirect:admin/features' => [

		RouteDefinition::PATTERN => '/admin/features',
		RouteDefinition::LOCATION => '/admin/modules/active'

	]

];
