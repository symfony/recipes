<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(\dirname(__DIR__).'/config/{packages}/*.yaml');
        $container->import(\dirname(__DIR__).'/config/{packages}/'.$this->environment.'/*.yaml');

        if (\is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import(\dirname(__DIR__).'/config/services.yaml');
            $container->import(\dirname(__DIR__).'/config/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import(\dirname(__DIR__).'/config/{services}.php');
            $container->import(\dirname(__DIR__).'/config/{services}_'.$this->environment.'.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(\dirname(__DIR__).'/config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import(\dirname(__DIR__).'/config/{routes}/*.yaml');

        if (\is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import(\dirname(__DIR__).'/config/routes.yaml');
        } else {
            $routes->import(\dirname(__DIR__).'/config/{routes}.php');
        }
    }
}
