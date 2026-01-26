<?php
require_once 'Connect.php';
require_once 'Event.php';
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
        $this->events = [];
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

    public function getEvents()
    {
        $sql = "SELECT * FROM events 
LEFT JOIN user_requests ON events.id = user_requests.event_id AND user_requests.user_id = :id 
";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $events = [];
        foreach ($rows as $row) {
            $event = new Event($row['title'], $row['date'], $row['description']);
            $event->setId($row['id']);
            $events[$row['id']] = $event;
        }
        $this->events = $events;
        return $this->events;
    }

    public function getEvent($id)
    {
        if (!$this->events) {
            $this->getEvents();
        }
        return $this->events[$id] ?? null;
    }

    private function addUserEvent($user_id, $event_id)
    {
        try {
            $sql = "INSERT INTO user_requests (user_id, event_id) VALUES (:user_id, :event_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->error = 'Failed to add user event: ' . $e->getMessage();
            return false;
        }

    }

    public function addEvent($event)
    {
        $sql = "INSERT INTO user_requests (user_id, event_id) VALUES (:user_id, :event_id)";
        $eventId = null;

        if ($event instanceof Event) {
            $eventId = $event->getId();
        } elseif (is_numeric($event)) {
            $eventId = (int) $event;
        }

        if (!$eventId) {
            $this->error = 'Invalid event supplied';
            return false;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $this->id);
            $stmt->bindParam(':event_id', $eventId);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->error = 'Failed to add user event: ' . $e->getMessage();
            return false;
        }
    }
}
