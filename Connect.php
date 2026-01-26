<?php

class Connect
{
    protected $db;
    public $error;
    public function __construct()
    {
        try {
            $host = $this->getEnvValue('DB_HOST', 'localhost');
            $dbName = $this->getEnvValue('DB_NAME', 'foxday');
            $user = $this->getEnvValue('DB_USER', 'admin');
            $pass = $this->getEnvValue('DB_PASS', 'admin');
            $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
            $this->db = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $this->createEventTable()->createUserTable()->createUserReq()->createUserReq();
        } catch (PDOException $e) {
            $this->error = 'Database connection failed: ' . $e->getMessage();
        }
    }

    private function getEnvValue($key, $default)
    {
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return $_SERVER[$key];
        }
        $value = getenv($key);
        return ($value !== false && $value !== '') ? $value : $default;
    }

    private function createEventTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS events (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          title VARCHAR(30) NOT NULL UNIQUE,
          date DATE NOT NULL,
          description TEXT NOT NULL  
        );
        ";

        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            $this->error = 'Failed to create events table: ' . $e->getMessage();
        }

        return $this;
    }

    private function createUserTable()  {
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(30) NOT NULL,
          year enum('1','2','3','4') NOT NULL default '1',  
          email VARCHAR(50) NOT NULL UNIQUE,
          createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
          updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          deletedAt DATETIME DEFAULT NULL 
        );
        ";

        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            $this->error = 'Failed to create users table: ' . $e->getMessage();
        }

        return $this;
    }

    private function createUserReq()  {
        $sql = "
        CREATE TABLE IF NOT EXISTS user_requests (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(6) UNSIGNED NOT NULL,
            event_id INT(6) UNSIGNED NOT NULL,
            
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (event_id) REFERENCES events(id)
        )";

        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            $this->error = 'Failed to create user_requests table: ' . $e->getMessage();
        }

        return $this;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getError()
    {
        return $this->error;
    }


    public function __destruct()
    {
        $this->db = null;
    }
}
