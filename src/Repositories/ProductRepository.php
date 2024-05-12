<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    public function __construct(private Database $database) { }

    public function getAll(): array
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query('SELECT * FROM products');

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|bool
    {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function store(array $data): string
    {
        $sql = 'INSERT INTO products (name, description, size)
                VALUES (:name, :description, :size)';
        
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        if (empty($data['description'])) {
            $stmt->bindValue(':description', NULL, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        }
        $stmt->bindValue(':size', $data['size'], PDO::PARAM_INT);

        $stmt->execute();

        return $pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE products 
            SET name = :name, 
                description = :description, 
                size = :size
            WHERE id = :id';
        
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        if (empty($data['description'])) {
            $stmt->bindValue(':description', NULL, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        }
        $stmt->bindValue(':size', $data['size'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $sql = 'DELETE FROM products WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}