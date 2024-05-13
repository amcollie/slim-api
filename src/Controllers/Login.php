<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Renderer\TemplateRenderer;
use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Login
{
    public function __construct(private TemplateRenderer $renderer, private UserRepository $repository) { }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->template($response, 'login.latte');
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        $user = $this->repository->find('email', $data['email']);
        if ($user && password_verify($data['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];

            return $response->withStatus(302)->withHeader('Location', '/');
        }

        return $this->renderer->template($response, 'login.latte', [
            'data' => $data,
            'error' => 'Invalid credentials'
        ]);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        session_destroy();

        return $response->withStatus(302)->withHeader('Location', '/');
    }
}