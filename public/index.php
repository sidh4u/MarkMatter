<?php

declare(strict_types=1);

define('ROOT', dirname(__DIR__));
require ROOT . '/vendor/autoload.php';

use App\Config;
use App\Router;

Config::load(ROOT);
Router::dispatch();
