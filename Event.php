<?php
require_once 'Connect.php';

$EVENTS = "events.json";

class Event extends Connect
{
    private $id;
    private $title;
    private $date;
    private $description;

    /**
     * Reads and parses events data from a file.
     *
     * This method retrieves event data from a JSON file, decodes the data into an array,
     * and ensures that the date format for each event is standardized.
     *
     * @return array An array of events with standardized date format. Returns an empty array if no valid data is found.
     * @global string $EVENTS The global variable containing the path to the events file.
     *
     */
    private static function getEventsFromFile()
    {
        global $EVENTS;
        $events = json_decode(file_get_contents($EVENTS), true);
        if (!$events) {
            return [];
        }

        foreach($events as &$event) {
            $event['date'] = date('Y-m-d', strtotime($event['date']));
        }

        return $events;
    }

    /**
     * Retrieves the title.
     *
     * @return string|null The title if set, or null if not.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Retrieves the date property.
     *
     * @return mixed The value of the date property.
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Retrieves the description associated with the current instance.
     *
     * @return string|null The description, or null if no description is set.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Constructor method for the class.
     *
     * @param string|null $title The title of the entity, default is null.
     * @param string|null $date The date associated with the entity, default is null.
     * @param string|null $description A description of the entity, default is null.
     *
     * @return void
     */
    public function __construct($title = null, $date = null, $description = null)
    {
        parent::__construct();
        $this->initEvents();
        
        $this->title = $title;
        $this->date = $date;
        $this->description = $description;
    }

    /**
     * Retrieves an event from the database based on the given event ID.
     *
     * @param int $id The unique identifier of the event to be retrieved.
     * @return Event|null Returns an Event object if found, or null if no event is found or the database connection fails.
     */
    public static function getEvent($id)
    {
        $connect = new Connect();
        $db = $connect->getDb();
        if (!$db) {
            return null;
        }
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $db->prepare($sql);
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

    /**
     * Initializes an event based on the given ID.
     *
     * @param mixed $id The identifier of the event to initialize.
     * @return Event Returns the event associated with the given ID, or a new Event instance if no event is found.
     */
    public static function initEvent($id)
    {
        $event = self::getEvent($id);
        return $event ?: new Event();
    }

    /**
     * Checks if an event with the given title exists in the database.
     *
     * @param string|null $title The title of the event to check. If null, the method will use the object's title property.
     * @return bool True if the event exists, false otherwise.
     */
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

    /**
     * Retrieves all events from the database.
     *
     * @return array An array of Event objects, or an empty array if the database connection fails or no events are available.
     */
    public static function getEvents()
    {
        $connect = new Connect();
        $db = $connect->getDb();
        if (!$db) {
            return [];
        }
        $sql = "SELECT * FROM events";
        $stmt = $db->prepare($sql);
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

    /**
     * Updates an existing event in the database with new values for title, date, and description.
     *
     * @param int $id The ID of the event to be updated.
     * @param string $title The new title for the event.
     * @param string $date The new date for the event in YYYY-MM-DD format.
     * @param string $description The new description for the event.
     * @return bool Returns true on successful update, false otherwise.
     */
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

    /**
     * Retrieves the ID of the current instance.
     *
     * @return mixed The ID of the instance.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the ID value.
     *
     * @param mixed $id The ID to set.
     * @return self Returns the current instance.
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Adds a new event to the database.
     *
     * @param string $title The title of the event.
     * @param string $date The date of the event in YYYY-MM-DD format.
     * @param string $description The description of the event.
     * @return int|false The ID of the newly inserted event on success, or false on failure.
     */
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

    /**
     * Checks if there are any events in the database.
     *
     * @return bool True if there are events, false otherwise.
     */
    private function hasAnyEvents()
    {
        $sql = "SELECT COUNT(*) FROM events";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Initializes predefined events if none have been added yet.
     *
     * @return void
     */
    private function initEvents()
    {
        if ($this->hasAnyEvents()) {
            return;
        }
        $events = self::getEventsFromFile();

        foreach($events as $event) {
            $this->addEvent($event['title'], $event['date'], $event['description']);
        }

    }

}
