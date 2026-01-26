<?php

require_once 'Connect.php';

class Event extends Connect
{
    private $id;
    private $title;
    private $date;
    private $description;

    public function __construct($title = null, $date = null, $description = null)
    {
        parent::__construct();
        $this->initEvents();
        
        $this->title = $title;
        $this->date = $date;
        $this->description = $description;
    }

    public static function getEvent($id)
    {
        $connect = new Connect();
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $connect->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $e = new Event($data['title'], $data['date'], $data['description']);
            $e->id = $data['id'];

            return $e;
        }

        return null;
    }

    public static function initEvent($id)
    {
        $event = self::getEvent($id);
        return $event ?: new Event();
    }

    public function hasEvent($title = null)
    {
        if ($title === null) {
            $title = $this->title;
        }
        if ($title === null || $title === '') {
            return false;
        }
        $sql = "SELECT COUNT(*) FROM events WHERE title = :title";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public static function getEvents()
    {
        $connect = new Connect();
        $sql = "SELECT * FROM events";
        $stmt = $connect->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($rows as $row) {
            $event = new Event($row['title'], $row['date'], $row['description']);
            $event->id = $row['id'];
            $events[] = $event;
        }

        return $events;
    }

    public function updateEvent($id, $title, $date, $description)
    {
        $sql = "UPDATE events SET title = :title, date = :date, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
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

    private function hasAnyEvents()
    {
        $sql = "SELECT COUNT(*) FROM events";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    private function initEvents()
    {
        if ($this->hasAnyEvents()) {
            return;
        }
        $events = [
            ['title' => 'Event 1', 'date' => '2023-10-01', 'description' => 'Description for Event 1'],
            ['title' => 'Event 2', 'date' => '2023-10-02', 'description' => 'Description for Event 2'],
            ['title' => 'Event 3', 'date' => '2023-10-03', 'description' => 'Description for Event 3'],
            ['title' => 'Event 4', 'date' => '2023-10-04', 'description' => 'Description for Event 4'],
        ];

        foreach($events as $event) {
            $this->addEvent($event['title'], $event['date'], $event['description']);
        }

    }

}
