<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class UserRepository
{

    public function __construct(private Database $database) {}

    public function store(array $data): void
    {
        $sql = 'INSERT INTO users (name, email, password_hash, api_key, api_key_hash) 
            VALUES (:name, :email, :password_hash, :api_key, :api_key_hash)';

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $data['password_hash'], PDO::PARAM_STR);
        $stmt->bindValue(':api_key', $data['api_key'], PDO::PARAM_STR);
        $stmt->bindValue(':api_key_hash', $data['api_key_hash'], PDO::PARAM_STR);

        $stmt->execute();
    }

    public function find(string $column, mixed $value): array|bool
    {
        $sql = "SELECT * FROM users WHERE $column = :value";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        return $stmt->fetch();
    }
}