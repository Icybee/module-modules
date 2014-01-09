<?php

namespace Icybee\Modules\Modules;

return array
(
	'!admin:manage' => array
	(
		'controller' => __NAMESPACE__ . '\ManageController'
	),

	'admin:modules/inactives' => array
	(
		'pattern' => '/admin/modules/inactives',
		'controller' => __NAMESPACE__ . '\ManageController',
		'title' => 'Inactives',
		'block' => 'inactives'
	),

	'admin:modules/install' => array
	(
		'pattern' => '/admin/modules/<[^/]+>/install',
		'controller' => __NAMESPACE__ . '\ManageController',
		'title' => 'Install',
		'block' => 'install',
		'visibility' => 'auto'
	),

	'redirect:admin/features' => array
	(
		'pattern' => '/admin/features',
		'location' => '/admin/modules'
	)
);