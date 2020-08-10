<?php

require __DIR__ . '/../core/Core.php';

$config = include_once '../config.php';

(new core\base\Application($config))->run();
