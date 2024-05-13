<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Renderer\TemplateRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Home
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->template($response, 'index.latte');
    }
}