<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

/*
 * Environment variables can also be specified in phpunit.xml.dist.
 * Those variables will override any defined in .env.
 */

if (!isset($_SERVER['APP_ENV']) || 'prod' !== $_SERVER['APP_ENV']) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__.'/../.env');
}

$debug = $_SERVER['APP_DEBUG'] ?? true;

if ($debug) {
    umask(0000);
}
