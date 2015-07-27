<?php

namespace Icybee\Modules\Modules;

use ICanBoogie\HTTP\Request;
use Icybee\Routing\RouteMaker as Make;

return Make::admin('modules', Routing\ModulesAdminController::class, [

	'only' => [ 'index', 'inactive', 'install' ],
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

//var_dump($routes);

return [

	'!admin:manage' => [

		'controller' => ManageController::class

	],

	'admin:modules/inactives' => [

		'pattern' => '/admin/modules/inactives',
		'controller' => ManageController::class,
		'title' => 'Inactives',
		'block' => 'inactives'

	],

	'admin:modules/install' => [

		'pattern' => '/admin/modules/<[^/]+>/install',
		'controller' => ManageController::class,
		'title' => 'Install',
		'block' => 'install',
		'visibility' => 'auto'

	],

	'redirect:admin/features' => [

		'pattern' => '/admin/features',
		'location' => '/admin/modules'

	]

];
