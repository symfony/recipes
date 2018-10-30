<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$envFromEnv = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null;
$env = $envFromEnv ?? 'dev';
$prod = 'prod' === $env;

if (!$prod && class_exists(Dotenv::class)) {
    (new Dotenv())->loadForEnv($env, __DIR__.'/../.env');
} elseif (null === $envFromEnv) {
    throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
}

return [$env, (bool) ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? !$prod)];
