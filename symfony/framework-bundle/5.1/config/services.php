<?php

use App\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator as di;

// This file is the entry point to configure your own services.
// Files in the packages/ subdirectory configure your dependencies.

return static function (di\ContainerConfigurator $container, Kernel $kernel): void {

    // Parameters are configuration that don't need to change depending on the machine where the app is deployed.
    // see https://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
    $container->parameters()
    //    ->set(...)
    ;

    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    // Makes classes in src/ available to be used as services
    $src = dirname(__DIR__).'/src';
    $services
        ->load('App\\', $src)
        ->exclude([
            $src.'/DependencyInjection',
            $src.'/Entity',
            $src.'/Migrations',
            $src.'/Tests',
            $src.'/Kernel.php',
        ])
    ;

    // Controllers are imported separately to make sure services can be injected
    // as action arguments even if you don't extend any base controller class
    $services
        ->load('App\\Controller\\', $src.'/Controller')
        ->tag('controller.service_arguments')
    ;

    // Add more service definitions when explicit configuration is needed.
    // Please note that last definitions *replace* previous ones when using $services->set().
    // It is possible to alter a previously declared definition by using $services->get() instead.
    $services
    //    ->set(App\MyService::class)
    //        ->args([di\ref(App\AnotherService::class)])
    ;

};
