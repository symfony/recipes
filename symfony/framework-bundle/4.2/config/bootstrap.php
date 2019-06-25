<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Requirements\SymfonyRequirements;

require dirname(__DIR__).'/vendor/autoload.php';

// Verify Symfony requirements
if (class_exists(SymfonyRequirements::class)) {
    $requirements = new SymfonyRequirements(dirname(__DIR__), Kernel::VERSION);
    $argv = [];
    echo "<pre>REMOVE THIS MESSAGE: composer remove symfony/requirements-checker\n\n";
    require dirname(__DIR__).'/vendor/symfony/requirements-checker/bin/requirements-checker.php';
    die(1);
}

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (is_array($env = @include dirname(__DIR__).'/.env.local.php')) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
} else {
    // load all the .env files
    (new Dotenv(false))->loadEnv(dirname(__DIR__).'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
