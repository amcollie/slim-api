<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Renderer\TemplateRenderer;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Profile
{
    public function __construct(private TemplateRenderer $renderer) { }

    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $encryption_key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);
        $api_key = Crypto::decrypt($user['api_key'], $encryption_key);
        return $this->renderer->template($response, 'profile.latte', ['api_key' => $api_key]);
    }

}