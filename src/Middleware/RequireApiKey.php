<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequireApiKey
{
    public function __construct(private ResponseFactory $factory, private UserRepository $repository) {}

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // $params = $request->getQueryParams();

        // if (!array_key_exists('api_key', $params)) {
        if (!$request->hasHeader('X-API-KEY')) {
            $response = $this->factory->createResponse();

            $response->getBody()->write(json_encode(["error" => "Missing API key"]));

            return $response->withStatus(400);
        }

        $api_key = $request->getHeaderLine('X-API-KEY');
        $api_key_hash = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);
        $user = $this->repository->find('api_key_hash', $api_key_hash);


        // if ($params['api_key'] !== $_ENV['API_KEY']) {
        if (!$user) {
            $response = $this->factory->createResponse();

            $response->getBody()->write(json_encode(["error" => "Invalid API key"]));

            return $response->withStatus(401);
        }

        $response = $handler->handle($request);

        return $response->withHeader("Content-Type", "application/json");
    }
}