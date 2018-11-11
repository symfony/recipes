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

    public static function bootstrapCli(array &$argv)
    {
        // consume --env and --no-debug from the command line

        // when using symfony/console v4.2 or higher, this should
        // be replaced by a call to Application::bootstrapEnv()

        for ($i = 0; $i < \count($argv) && '--' !== $v = $argv[$i]; ++$i) {
            if ('--no-debug' === $v) {
                putenv('APP_DEBUG='.$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = '0');
                $argvUnset[$i] = true;
                break;
            }
        }

        for ($i = 0; $i < \count($argv) && '--' !== $v = $argv[$i]; ++$i) {
            if (!$v || '-' !== $v[0] || !preg_match('/^-(?:-env(?:=|$)|e=?)(.*)$/D', $v, $v)) {
                continue;
            }
            if (!empty($v[1]) || !empty($argv[1 + $i])) {
                putenv('APP_ENV='.$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = empty($v[1]) ? $argv[1 + $i] : $v[1]);
                $argvUnset[$i] = $argvUnset[$i + empty($v[1])] = true;
            }
            break;
        }

        if (!empty($argvUnset)) {
            $argv = array_values(array_diff_key($argv, $argvUnset));
        }
    }

    public static function bootstrapEnv($env = null)
    {
        if (null !== $env) {
            putenv('APP_ENV='.$_SERVER['APP_ENV'] = $env);
        }

        if ('prod' !== $_SERVER['APP_ENV'] = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : (isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : null)) {
            if (!class_exists(Dotenv::class)) {
                throw new \RuntimeException('The "APP_ENV" environment variable is not defined. You need to set it or run "composer require symfony/dotenv" to load it from a ".env" file.');
            }

            // when using symfony/dotenv v4.2 or higher, this call and the related methods
            // below should be replaced by a call to the new Dotenv::loadEnv() method
            self::loadEnv(new Dotenv(), \dirname(__DIR__).'/.env');
        }

        $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'dev';
        $_SERVER['APP_DEBUG'] = isset($_SERVER['APP_DEBUG']) ? $_SERVER['APP_DEBUG'] : (isset($_ENV['APP_DEBUG']) ? $_ENV['APP_DEBUG'] : 'prod' !== $_SERVER['APP_ENV']);
        $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
    }

    private static function loadEnv(Dotenv $dotenv, $path)
    {
        if (file_exists($path) || !file_exists($p = "$path.dist")) {
            $dotenv->load($path);
        } else {
            $dotenv->load($p);
        }

        if (null === $env = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : (isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : null)) {
            $dotenv->populate(array('APP_ENV' => $env = 'dev'));
        }

        if ('test' !== $env && file_exists($p = "$path.local")) {
            $dotenv->load($p);
            $env = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : (isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : $env);
        }

        if (file_exists($p = "$path.$env")) {
            $dotenv->load($p);
        }

        if (file_exists($p = "$path.$env.local")) {
            $dotenv->load($p);
        }
    }
}
