<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIGS_POSSIBLE_EXTENSIONS = '.{php,xml,yaml,yml}';

    private const RESOURCE_TYPE_GLOBAL = 'glob';

    public function registerBundles(): iterable
    {
        $contents = require $this->getBundlesFilePath();
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $bundlesFileResource = new FileResource($this->getBundlesFilePath());
        $container->addResource($bundlesFileResource);
        
        $container->setParameter('container.dumper.inline_class_loader', true);
        
        $configsRelativePaths = [
            '/{packages}/*',
            '/{packages}/'.$this->environment.'/**/*',
            '/{services}',
            '/{services}_'.$this->environment,
        ];
        $this->loadConfigs($loader, $configsRelativePaths);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routesRelativePaths = [
            '/{routes}/'.$this->environment.'/**/*',
            '/{routes}/*',
            '/{routes}',
        ];
        $this->importRoutes($routes, $routesRelativePaths);
    }

    private function loadConfigs(LoaderInterface $loader, array $relativePaths): void
    {
        $confDir = $this->getConfigDir();
        
        foreach ($relativePaths as $configRelativePath) {
            $loader->load(
                $confDir.$configRelativePath.self::CONFIGS_POSSIBLE_EXTENSIONS,
                self::RESOURCE_TYPE_GLOBAL
            );
        }
    }

    private function importRoutes(RouteCollectionBuilder $routes, array $relativePaths): void
    {
        $confDir = $this->getConfigDir();

        foreach ($relativePaths as $routeRelativePath) {
            $routes->import(
                $confDir.$routeRelativePath.self::CONFIGS_POSSIBLE_EXTENSIONS,
                '/',
                self::RESOURCE_TYPE_GLOBAL
            );
        }        
    }

    private function getConfigDir(): string
    {
        return $this->getProjectDir().'/config';
    }

    private function getBundlesFilePath(): string
    {
        return $this->getConfigDir().'/bundles.php';
    }
}
