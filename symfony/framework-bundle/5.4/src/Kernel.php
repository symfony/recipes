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
        $projectDir = $this->getProjectDir();

        $container->import($projectDir.'/config/{packages}/*.yaml');
        $container->import($projectDir.'/config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file($projectDir.'/config/services.yaml')) {
            $container->import($projectDir.'/config/services.yaml');
            $container->import($projectDir.'/config/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import($projectDir.'/config/{services}.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $projectDir = $this->getProjectDir();

        $routes->import($projectDir.'/config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($projectDir.'/config/{routes}/*.yaml');

        if (is_file($projectDir.'/config/routes.yaml')) {
            $routes->import($projectDir.'/config/routes.yaml');
        } else {
            $routes->import($projectDir.'/config/{routes}.php');
        }
    }
}
