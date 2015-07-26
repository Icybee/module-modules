<?php

namespace Icybee\Modules\Modules;

use ICanBoogie\Module\Descriptor;

return array
(
	Descriptor::CATEGORY => 'features',
	Descriptor::DESCRIPTION => "Manages modules",
	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSION => false,
	Descriptor::REQUIRED => true,
	Descriptor::TITLE => "Modules"
);
