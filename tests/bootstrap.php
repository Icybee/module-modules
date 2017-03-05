<?php

namespace ICanBoogie;

$module_dir = __DIR__ . '/../vendor/icanboogie-modules';

if (!file_exists($module_dir)) {
	mkdir($module_dir);
}

require __DIR__ . '/../vendor/autoload.php';

$app = boot();
$app->modules->install();
