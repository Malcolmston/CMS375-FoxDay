<?php

class User extends Connect
{
    private $id;
    private $name;
    private $year;
    private $email;
    private $events;
    private $isDeleted;

    public function __construct($name, $year, $email)
    {
        parent::__construct();

        $this->name = $name;
        $this->year = $year;
        $this->email = $email;
    }

    public function getUser($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $u = new User($data['name'], $data['year'], $data['email']);
            $u->id = $data['id'];
            return $u;
        }
        return null;
    }

    public function hasUser()
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email AND deletedAt IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function isUserDeleted()
    {
        $sql = "SELECT deletedAt FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $deletedAt = $stmt->fetchColumn();
        return $deletedAt !== null;
    }

    public function deleteUser()
    {
        $sql = "UPDATE users SET deletedAt = CURRENT_TIMESTAMP WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
    }
}
