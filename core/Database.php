<?php
class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $maxRetries = 5;
        $retryDelay = 2; // seconds

        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
                return;
            } catch (PDOException $e) {
                if ($i === $maxRetries - 1) {
                    throw $e;
                }
                sleep($retryDelay);
            }
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
