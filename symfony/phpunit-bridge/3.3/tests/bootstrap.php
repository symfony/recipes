<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;

/*
 * Environment variables can also be specified in phpunit.xml.dist.
 * Those variables will override any defined in .env.
 */

Kernel::bootstrapEnvironment($_ENV['APP_ENV'] ?? null);

$debug = $_SERVER['APP_DEBUG'] ?? true;

if ($debug) {
    umask(0000);
}
