#!/usr/bin/env php
<?php

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', 'UTC');
}

if (is_file(dirname(__DIR__).'/vendor/phpunit/phpunit/phpunit')) {
    require dirname(__DIR__).'/vendor/phpunit/phpunit/phpunit';
} else {
    if (!is_file(dirname(__DIR__).'/vendor/symfony/phpunit-bridge/bin/simple-phpunit.php')) {
        echo "Unable to find the `simple-phpunit.php` script in `vendor/symfony/phpunit-bridge/bin/`.\n";
        exit(1);
    }

    require dirname(__DIR__).'/vendor/symfony/phpunit-bridge/bin/simple-phpunit.php';
}
