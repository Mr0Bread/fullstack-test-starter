<?php

class Database {
    private static $pdo;

    public static function connect() {
        if (self::$pdo === null) {
            $host = getenv('DB_HOST');
            $db = getenv('DB_DATABASE');
            $user = getenv('DB_USERNAME');
            $pass = getenv('DB_PASSWORD');
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$pdo;
    }

    public static function getAllCategories() {
        $stmt = self::connect()->query("SELECT * FROM categories");
        return $stmt->fetchAll();
    }

    public static function getAllProducts() {
        $stmt = self::connect()->query("SELECT * FROM products");
        return $stmt->fetchAll();
    }

    public static function getProductById($id) {
        $stmt = self::connect()->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
