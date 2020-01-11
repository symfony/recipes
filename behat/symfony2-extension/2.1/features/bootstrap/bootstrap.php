<?php

use Symfony\Component\Dotenv\Dotenv;

putenv('APP_ENV='.$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'test');

if (file_exists(dirname(__DIR__, 2).'/config/bootstrap.php')) {
    require dirname(__DIR__, 2).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__, 2).'/.env');
}
