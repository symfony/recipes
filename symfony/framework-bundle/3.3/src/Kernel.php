<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * Bootstrap the environment if APP_ENV has not been set yet
     * It will load the generic .env file and the .env for the APP_ENV set in the generic (if it exists)
     *
     * @param string|null $env If set it will load an additional ".env" file
     */
    public static function bootstrapEnvironment(string $env = null)
    {
        if (isset($_SERVER['APP_ENV'])) {
            // environment is set, do not load .env
            return;
        }

        $projectDir = __DIR__.'/..';
        if (!class_exists(Dotenv::class) || !file_exists($projectDir.'/.env')) {
            // Dotenv is not installed or generic env file is not present
            return;
        }

        // Load default .env file
        $dotEnv = new Dotenv();
        $dotEnv->load($projectDir.'/.env');
        $appEnv = $_SERVER['APP_ENV'] ?? 'dev';
        if ($env && file_exists($file = "$projectDir/.env.$env")) {
            // Load a specific environment requested by the user
            $dotEnv->load($file);
        } elseif ($appEnv && $appEnv !== $env && file_exists($file = "$projectDir/.env.$appEnv")) {
            // Load the environment for the app set in the generic
            $dotEnv->load($file);
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
}
