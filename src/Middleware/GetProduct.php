<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Repositories\ProductRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class GetProduct
{
    public function __construct(private ProductRepository $repository) {}

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $product = $this->repository->getById(intval($id));
        if (! $product) {
            throw new HttpNotFoundException($request, 'Product not found');
        }

        $request = $request->withAttribute('product', $product);

        return $handler->handle($request);
    }
}