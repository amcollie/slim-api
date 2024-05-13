<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Renderer\TemplateRenderer;
use App\Repositories\UserRepository;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

class Register
{
    public function __construct(private TemplateRenderer $renderer, private Validator $validator, private UserRepository $repository) 
    {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', ['lengthMin', 6]],
            'password_confirmation' => ['required', ['equals', 'password']]
        ]);

        $this->validator->rule(function ($field, $value, $params, $fields) {
            return $this->repository->find('email', $value) === false;
        }, 'email')->message('{field} already taken');
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->template($response, 'register.latte');
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);
        if (!$this->validator->validate()) {
            return $this->renderer->template($response, 'register.latte', [
                'data' => $data,
                'errors' => $this->validator->errors()
            ]);
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $api_key = bin2hex(random_bytes(16));

        $encryption_key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);
        $data['api_key'] = Crypto::encrypt($api_key, $encryption_key);
        $data['api_key_hash'] = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);

        $this->repository->store($data);

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/register/success');
    }

    public function success(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->template($response, 'register-success.latte');
    }
}