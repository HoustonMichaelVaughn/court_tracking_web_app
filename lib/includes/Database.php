<?php

class Database
{
    private static $instance = null;
    private $pdo;
    private $host = 'localhost';
    private $dbname = 'court_tracking_db';
    private $username = 'root';
    private $password = 'defb29ad6c520227685b5b14feacf23f';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            die("Database Failure: " . $e->getMessage());
        }
    }
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}