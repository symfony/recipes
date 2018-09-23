<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

if (class_exists(Dotenv::class)) {
    (new Dotenv())->loadForEnv($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev', __DIR__.'/../.env');
} elseif (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
}
