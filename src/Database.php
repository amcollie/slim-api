<?php

declare(strict_types = 1);

namespace App;

use PDO;

class Database
{
    public function __construct(private string $host, private string $username, private string $password, private string $database) {}

    public function  getConnection(): PDO
    {
        $dsn = "mysql:host=$this->host;dbname=$this->database;charset=utf8mb4";

        return new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}