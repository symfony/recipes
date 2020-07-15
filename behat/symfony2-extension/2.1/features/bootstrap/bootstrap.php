<?php

use Symfony\Component\Dotenv\Dotenv;

putenv('APP_MODE='.$_SERVER['APP_MODE'] = $_ENV['APP_MODE'] = 'test');

if (file_exists(dirname(__DIR__, 2).'/config/bootstrap.php')) {
    require dirname(__DIR__, 2).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv('APP_MODE'))->bootEnv(dirname(__DIR__, 2).'/.env');
}
