<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->load(__DIR__.'/../.env', __DIR__.'/../.env.test');
