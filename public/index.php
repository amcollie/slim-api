<?php

declare(strict_types = 1);

use DI\ContainerBuilder;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;

ini_set('display_errors', '1');

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';
$builder = new ContainerBuilder();
$container = $builder
    ->addDefinitions(APP_ROOT . '/config/settings.php')
    ->build();

$container->set(Engine::class, function (ContainerInterface $container) {
    $latte = new Engine();
    $settings = $container->get('latte');
    $latte->setLoader(new FileLoader($settings['template']));
    $latte->setTempDirectory($settings['template_temp']);
    $latte->setAutoRefresh($settings['template_auto_refresh']);

    return $latte;
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true, true, true);

require APP_ROOT . '/config/routes.php';

$app->run();    