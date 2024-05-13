<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequireLogin
{
    public function __construct(private ResponseFactory $factory, private UserRepository $repository) {}

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (isset($_SESSION['user_id'])) {
            $user = $this->repository->find('id', $_SESSION['user_id']);
            if ($user) {
                $request = $request->withAttribute('user', $user);
                return $handler->handle($request);
            }
        }

        $response = $this->factory->createResponse();

        return $response->withStatus(302)->withHeader('Location', '/login');
    }
}