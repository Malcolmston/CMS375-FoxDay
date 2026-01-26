<?php

class Connect
{
    private $db;
    public $error;
    public function __construct()
    {
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=foxday', 'admin', 'admin');
            $this->createEventTable()->createUserTable()->createUserReq()->createUserReq();
        } catch (PDOException $e) {
            $this->error = 'Database connection failed: ' . $e->getMessage();
        }
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
          created DATETIME DEFAULT CURRENT_TIMESTAMP,
          updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          deleted DATETIME DEFAULT NULL 
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

    public function addEvent($title, $date, $description)
    {
        try {
        $sql = "INSERT INTO events (title, date, description) VALUES (:title, :date, :description)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->error = 'Failed to add event: ' . $e->getMessage();
            return false;
        }
    }

    public function addUser($name, $year, $email)
    {
        try {
            $sql = "INSERT INTO users (name, year, email) VALUES (:name, :year, :email)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->error = 'Failed to add user: ' . $e->getMessage();
            return false;
        }
    }

    public function __destruct()
    {
        $this->db = null;
    }
}
