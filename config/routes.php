<?php

return array
(
	'!admin:manage' => array
	(

	),

	'admin:modules/inactives' => array
	(
		'pattern' => '/admin/modules/inactives',
		'controller' => 'Icybee\BlockController',
		'title' => 'Inactives',
		'block' => 'inactives'
	),

	'admin:modules/install' => array
	(
		'pattern' => '/admin/modules/<[^/]+>/install',
		'controller' => 'Icybee\BlockController',
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