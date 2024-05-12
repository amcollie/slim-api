<?php

declare(strict_types = 1);

use App\Controllers\Product;
use App\Controllers\ProductIndex;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\GetProduct;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Routing\RouteCollectorProxy;

ini_set('display_errors', '1');

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$container = $builder
    ->addDefinitions(APP_ROOT . '/config/settings.php')
    ->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');

$app->add(new AddJsonResponseHeader());

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/products', ProductIndex::class);
    
    $group->post('/products', [Product::class, 'store']);

    $group->group('', function (RouteCollectorProxy $group) {
        
        $group->get('/products/{id:[0-9]+}', Product::class . ':show');

        $group->patch('/products/{id:[0-9]+}', [Product::class,'update']);

        $group->delete('/products/{id:[0-9]+}', [Product::class, 'delete']);

    })->add(GetProduct::class);
});

$app->run();    