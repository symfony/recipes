<?php

use Symfony\Component\Dotenv\Dotenv;

// The check is to ensure we don't use .env in production
if (!getenv('APP_ENV')) {
    (new Dotenv())->load(__DIR__.'/../../.env');
}
