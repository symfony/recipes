<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new class($context['APP_ENV'], (bool) $context['APP_DEBUG']) extends Kernel {
        use MicroKernelTrait;
    };
};
