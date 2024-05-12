<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

class Product
{
    public function __construct(private ProductRepository $repository, private Validator $validator) 
    {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'size' => [
                'required',
                'integer',
                ['min', 1]
            ],
        ]);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
    {
        $product = $request->getAttribute('product');

        $response->getBody()->write(json_encode($product));

        return $response;
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $this->validator = $this->validator->withData($body);
        if (!$this->validator->validate()) {
            $response->getBody()->write(json_encode([
                $this->validator->errors()
            ]));

            return $response->withStatus(422);
        }

        $id = $this->repository->store($body);
        $response->getBody()->write(json_encode([
            'message' => 'Product created',
            'id' => $id
        ]));

        return $response->withStatus(201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface
    {
        $body = $request->getParsedBody();

        $this->validator = $this->validator->withData($body);
        if (!$this->validator->validate()) {
            $response->getBody()->write(json_encode([
                $this->validator->errors()
            ]));

            return $response->withStatus(422);
        }

        $rows = $this->repository->update(intval($id), $body);
        $response->getBody()->write(json_encode([
            'message' => 'Product updated',
            'rows' => $rows
        ]));

        return $response->withStatus(200);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface
    {
        $rows = $this->repository->delete(intval($id));

        $response->getBody()->write(json_encode([
            'message'=> 'Product deleted',
            'rows' => $rows
        ]));

        return $response->withStatus(200);
    }
}