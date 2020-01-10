<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/packages/{,'.$this->environment.'/}*.yaml');
        $container->import('../config/services{,_'.$this->environment.'}.yaml');

        // See https://symfony.com/doc/current/best_practices.html#configuration
        $container->parameters()
        //    ->set('name', 'value')
        ;

        $services = $container->services()
            ->defaults()
                ->autowire()
                ->autoconfigure()
        ;

        // Makes classes in src/ available to be used as services
        $services
            ->load('App\\', __DIR__)
            ->exclude([
                'DependencyInjection',
                'Entity',
                'Migrations',
                'Tests',
                'Kernel.php',
            ])
        ;

        // Use $services->get() to alter an already loaded definition
        $services
        //    ->set(App\MyService::class)
        //        ->args([service(App\AnotherService::class)])
        ;

    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/routes/{'.$this->environment.'/,}*.yaml');
        $routes->import('../config/routes{_'.$this->environment.',}.yaml');

        $routes
        //    ->add('index', '/')
        //        ->controller([App\Controller\DefaultController::class, 'index'])
        ;
    }
}
