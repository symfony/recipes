<?php

use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\Console\Application;
use Symfony\Component\VarDumper\Dumper\CliDumper;

$handlers = [
    'main' => [
        'type' => 'stream',
        'path' => '%kernel.logs_dir%/%kernel.environment%.log',
        'level' => 'debug',
        'channels' => ['!event'],
    ],
    // uncomment to get logging in your browser
    // you may have to allow bigger header sizes in your Web server configuration
    //'firephp' => [
    //    'type' => 'firephp',
    //    'level' => 'info',
    //],
    //'chromephp' => [
    //    'type' => 'chromephp',
    //    'level' => 'info',
    //],
];

$container->addResource(new ClassExistenceResource(Application::class));
if (class_exists(Application::class)) {
    $handlers['console'] = [
        'type' => 'console',
        'process_psr_3_messages' => false,
        'channels' => ['!event', '!doctrine', '!console'],
    ];
    if (!class_exists(CliDumper::class)) {
        $handlers['console']['formatter'] = 'monolog.formatter.line';
    }
}

$container->loadFromExtension('monolog', [
    'handlers' => $handlers,
]);
