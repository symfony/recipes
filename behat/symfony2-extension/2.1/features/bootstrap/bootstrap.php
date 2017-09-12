<?php

use Symfony\Component\Dotenv\Dotenv;

// The check is to ensure we don't use .env in production
if (file_exists(__DIR__.'/../../.env')) {
    (new Dotenv())->load(__DIR__.'/../../.env');
}
