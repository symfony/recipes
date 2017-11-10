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

    public function getCacheDir()
    {
        return $this->getVarDir().'/cache/'.$this->environment;
    }

    public function getLogDir()
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

    protected function getConfigDir()
    {
        return $this->getProjectDir().'/config';
    }

    protected function getVarDir()
    {
        return $this->getProjectDir().'/var';
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.autowiring.strict_mode', true);
        $configDir = $this->getConfigDir();
        $loader->load($configDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($configDir.'/packages/'.$this->environment)) {
            $loader->load($configDir.'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($configDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($configDir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $configDir = $this->getConfigDir();
        if (is_dir($configDir.'/routes/')) {
            $routes->import($configDir.'/routes/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        if (is_dir($configDir.'/routes/'.$this->environment)) {
            $routes->import($configDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($configDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');
    }
}
