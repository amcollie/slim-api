<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductIndex
{
    public function __construct(private ProductRepository $repository) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $products = $this->repository->getAll();

        $response->getBody()->write(json_encode($products));

        return $response;
    }
}