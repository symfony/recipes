<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return $this->getVarDir().'/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getVarDir().'/log';
    }

    public function registerBundles()
    {
        $contents = require dirname(__DIR__).'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function getConfigDir(): string
    {
        return dirname(__DIR__).'/config';
    }

    protected function getVarDir(): string
    {
        return dirname(__DIR__).'/var';
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.autowiring.strict_mode', true);
        $loader->load($this->getConfigDir().'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($this->getConfigDir().'/packages/'.$this->environment)) {
            $loader->load($this->getConfigDir().'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($this->getConfigDir().'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($this->getConfigDir().'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        if (is_dir($this->getConfigDir().'/routes/')) {
            $routes->import($this->getConfigDir().'/routes/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        if (is_dir($this->getConfigDir().'/routes/'.$this->environment)) {
            $routes->import($this->getConfigDir().'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($this->getConfigDir().'/routes'.self::CONFIG_EXTS, '/', 'glob');
    }
}
