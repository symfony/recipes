<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;

$envFromEnv = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null;
$env = $envFromEnv ?? 'dev';
$prod = 'prod' === $env;

if (!$prod && class_exists(Dotenv::class)) {
    (new Dotenv())->loadForEnv($env, __DIR__.'/../.env');
    $env = $_SERVER['APP_ENV'] ?? $env;
} elseif (null === $envFromEnv) {
    throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
}

$debug = (bool) ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? !$prod);
if ($debug) {
    umask(0000);

    if (class_exists(Debug::class)) {
        Debug::enable();
    }
}

return new Kernel($env, $debug);
