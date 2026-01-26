<?php

class Event extends Connect
{
    private $id;
    private $title;
    private $date;
    private $description;

    public function __construct($title, $date, $description = null)
    {
        parent::__construct();
        $this->initEvents();
        
        $this->title = $title;
        $this->date = $date;
        $this->description = $description;
    }

    public function getEvent($id)
    {
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $e =  new Event($data['title'], $data['date'], $data['description']);
            $e->id = $data['id'];

            return $e;
        }

        return null;
    }
    public function hasEvent($title)
    {
        $sql = "SELECT COUNT(*) FROM events WHERE title = :title";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function getEvents()
    {
        $sql = "SELECT * FROM events";
        $stmt = $this->db->prepare($sql);
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

    private function initEvents()
    {
        $events = [
            ['title' => 'Event 1', 'date' => DATE('2023-10-01'), 'description' => 'Description for Event 1'],
            ['title' => 'Event 2', 'date' => DATE('2023-10-02'), 'description' => 'Description for Event 2'],
            ['title' => 'Event 3', 'date' => DATE('2023-10-03'), 'description' => 'Description for Event 3'],
            ['title' => 'Event 4', 'date' => DATE('2023-10-04'), 'description' => 'Description for Event 4'],
        ];

        foreach($events as $event) {
            $this->addEvent($event['title'], $event['date'], $event['description']);
        }

    }

}
