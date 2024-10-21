<?php

use App\Kernel;

if (!is_dir(dirname(__DIR__).'/vendor')) {
    throw new LogicException('Dependencies are missing. Try running "composer install".');
}

if (!is_file(dirname(__DIR__).'/vendor/autoload_runtime.php')) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

if (false === include dirname(__DIR__).'/vendor/autoload_runtime.php') {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
