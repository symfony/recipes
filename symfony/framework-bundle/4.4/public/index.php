<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

if ($_SERVER['SYMFONY_ALLOW_OVERRIDE'] ?? $_ENV['SYMFONY_ALLOW_OVERRIDE'] ?? false) {
    if (isset($_COOKIE['APP_ENV'])) {
        $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = $_COOKIE['APP_ENV'];
        unset($_COOKIE['APP_ENV']);
    }
    if (isset($_COOKIE['APP_DEBUG'])) {
        $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] = (bool) $_COOKIE['APP_DEBUG'];
        unset($_COOKIE['APP_DEBUG']);
    }
    if (isset($_COOKIE['SYMFONY_HTTP_CACHE'])) {
        $_ENV['SYMFONY_HTTP_CACHE'] = $_SERVER['SYMFONY_HTTP_CACHE'] = (bool) $_COOKIE['SYMFONY_HTTP_CACHE'];
        unset($_COOKIE['SYMFONY_HTTP_CACHE']);
    }
}

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();

if ($_SERVER['SYMFONY_HTTP_CACHE'] ?? $_ENV['SYMFONY_HTTP_CACHE'] ?? false) {
    class AppCache extends HttpCache {}
    $kernel = new AppCache($kernel);
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
