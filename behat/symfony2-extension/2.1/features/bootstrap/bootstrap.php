<?php

use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if (!getenv('APP_ENV')) {
    (new Dotenv())->load(__DIR__.'/../../.env');
}
