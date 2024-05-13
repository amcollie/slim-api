<?php

declare(strict_types=1);

namespace App\Renderer;
use Latte\Engine;
use Psr\Http\Message\ResponseInterface;

class TemplateRenderer
{
    public function __construct(private Engine $engine) {}

    public function template(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $response->getBody()->write($this->engine->renderToString($template, $data));

        return $response;
    }

}