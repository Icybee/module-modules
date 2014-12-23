<?php

namespace Icybee\Modules\Modules;

return [

	'!admin:manage' => [

		'controller' => __NAMESPACE__ . '\ManageController'

	],

	'admin:modules/inactives' => [

		'pattern' => '/admin/modules/inactives',
		'controller' => __NAMESPACE__ . '\ManageController',
		'title' => 'Inactives',
		'block' => 'inactives'

	],

	'admin:modules/install' => [

		'pattern' => '/admin/modules/<[^/]+>/install',
		'controller' => __NAMESPACE__ . '\ManageController',
		'title' => 'Install',
		'block' => 'install',
		'visibility' => 'auto'

	],

	'redirect:admin/features' => [

		'pattern' => '/admin/features',
		'location' => '/admin/modules'

	]

];
