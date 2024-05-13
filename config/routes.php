<?php

declare(strict_types = 1);

use App\Controllers\Home;
use App\Controllers\Login;
use App\Controllers\Product;
use App\Controllers\ProductIndex;
use App\Controllers\Profile;
use App\Controllers\Register;
use App\Middleware\ActiveSession;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\GetProduct;
use App\Middleware\RequireApiKey;
use App\Middleware\RequireLogin;
use Slim\Routing\RouteCollectorProxy;

$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/', Home::class)->setName('home');

    $group->get('/register', [Register::class, 'create'])->setName('register.create');
    $group->post('/register', [Register::class, 'store'])->setName('register.store');
    $group->get('/register/success', [Register::class, 'success'])->setName('register.success');

    $group->get('/login', [Login::class, 'create'])->setName('login.create');
    $group->post('/login', [Login::class, 'store'])->setName('login.store');

    $group->get('/logout', [Login::class, 'destroy'])->setName('login.destroy');

    $group->get('/profile', [Profile::class, 'show'])
        ->add(RequireLogin::class)
        ->setName('profile.show');
})->add(ActiveSession::class);

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/products', ProductIndex::class);
    
    $group->post('/products', [Product::class, 'store']);

    $group->group('', function (RouteCollectorProxy $group) {
        
        $group->get('/products/{id:[0-9]+}', Product::class . ':show');

        $group->patch('/products/{id:[0-9]+}', [Product::class,'update']);

        $group->delete('/products/{id:[0-9]+}', [Product::class, 'delete']);

    })->add(GetProduct::class);
})
    ->add(AddJsonResponseHeader::class)
    ->add(RequireApiKey::class);
