<?php

declare(strict_types=1);

use App\Database;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->load();

return [
    Database::class => function() {
        return new Database(host: $_ENV['DB_HOST'], username: $_ENV['DB_USER'], password: $_ENV['DB_PASS'], database: $_ENV['DB_NAME']);
    },
    'latte' => [
        'template' => dirname(__DIR__) . '/views',
        'template_temp' => dirname(__DIR__) . '/tmp/latte',
        'template_auto_refresh' => true
    ]
];