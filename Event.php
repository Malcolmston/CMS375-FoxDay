<?php

class Event extends Connect
{
    private $id;
    private $title;
    private $date;
    private $description;

    public function __construct()
    {
        parent::__construct();
        $this->initEvents();
    }

    public function getEvent($id)
    {
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hasEvent($title)
    {
        $sql = "SELECT COUNT(*) FROM events WHERE title = :title";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getEvents()
    {
        $sql = "SELECT * FROM events";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    public function exsist()
    {

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
