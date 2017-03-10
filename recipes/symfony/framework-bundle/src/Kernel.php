<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return dirname(dirname(__DIR__)).'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return dirname(dirname(__DIR__)).'/var/logs';
    }

    public function registerBundles()
    {
        $contents = require dirname($this->getRootDir()).'/etc/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->getEnvironment()])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $confDir = dirname($this->getRootDir()).'/etc';
        $loader->import($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->getEnvironment())) {
            $loader->import($confDir.'/packages/'.$this->getEnvironment().'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->import($confDir.'/container'.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = dirname($this->getRootDir()).'/etc';
        if (is_dir($confDir.'/routing/')) {
            $routes->import($confDir.'/routing/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        if (is_dir($confDir.'/routing/'.$this->getEnvironment())) {
            $routes->import($confDir.'/routing/'.$this->getEnvironment().'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($confDir.'/routing'.self::CONFIG_EXTS, '/', 'glob');
    }
}
