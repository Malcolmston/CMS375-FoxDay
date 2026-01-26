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

    /**
     * Constructor method to initialize the object with name, year, and email.
     *
     * @param string $name The name of the entity.
     * @param int $year The year associated with the entity.
     * @param string $email The email address of the entity.
     *
     * @return void
     */
    public function __construct($name, $year, $email)
    {
        parent::__construct();

        $this->name = $name;
        $this->year = $year;
        $this->email = $email;
        $this->events = [];
    }

    /**
     * Retrieves a user from the database by their unique identifier.
     *
     * @param int $id The unique identifier of the user to retrieve.
     * @return User|null Returns a User object if a user with the specified ID exists, or null if no user is found.
     */
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

    /**
     * Checks if a user exists in the database based on the email.
     *
     * @param bool $p If true, the check includes all users regardless of deletion status.
     *                If false, only non-deleted users are considered.
     * @return bool Returns true if the user exists, false otherwise.
     */
    public function hasUser($p = false)
    {

        if ($p) {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        } else {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email AND deletedAt IS NULL";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    /**
     * Checks if the user associated with the provided email has been marked as deleted.
     *
     * @return bool Returns true if the user exists and has a non-null deletedAt timestamp, otherwise false.
     */
    public function isUserDeleted()
    {
        $sql = "SELECT deletedAt FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $deletedAt = $stmt->fetchColumn();
        return $deletedAt !== null && $this->hasUser(true);
    }

    /**
     * Marks a user as deleted by setting the `deletedAt` column to the current timestamp.
     *
     * @return void
     */
    public function deleteUser()
    {
        $sql = "UPDATE users SET deletedAt = CURRENT_TIMESTAMP WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
    }

    /**
     * Retrieves events from the database and maps them into Event objects.
     *
     * Queries the `events` table, joins it with the `user_requests` table based on the user ID,
     * and constructs an array of Event objects using the fetched data.
     *
     * @return array An associative array of Event objects indexed by their IDs.
     */
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

    /**
     * Retrieves an event by its ID.
     *
     * @param mixed $id The identifier of the event to retrieve.
     * @return mixed|null The event associated with the provided ID, or null if not found.
     */
    public function getEvent($id)
    {
        if (!$this->events) {
            $this->getEvents();
        }
        return $this->events[$id] ?? null;
    }

    /**
     * Adds a user event association to the database.
     *
     * @param int $user_id The ID of the user.
     * @param int $event_id The ID of the event.
     *
     * @return int|false Returns the ID of the newly created record on success, or false on failure.
     */
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

    /**
     * Adds an event to the user_requests table for the current user.
     *
     * @param mixed $event Can either be an instance of the Event class or a numeric event ID.
     * @return bool Returns true if the event was added successfully, otherwise false.
     */
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

    /**
     * Creates a new user in the database if the user does not already exist and has not been previously deleted.
     *
     * @return bool Returns true if the user creation is successful, false otherwise. Sets an error message if the user already exists,
     *              was previously deleted, or if a database error occurs during the user creation process.
     */
    public function createUser()
    {
        try {

            if ($this->hasUser()) {
                $this->error = 'User already exists';
                return false;
            }
            if ($this->isUserDeleted()) {
                $this->error = 'User previously deleted';
                return false;
            }

            $sql = "INSERT INTO users (name, year, email) VALUES (:name, :year, :email)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':year', $this->year);
            $stmt->bindParam(':email', $this->email);
            $ok = $stmt->execute();
            if ($ok) {
                $this->id = (int) $this->db->lastInsertId();
            }
            return $ok;
        } catch (PDOException $e) {
            $this->error = 'Failed to create user: ' . $e->getMessage();
            return false;
        }
    }

}
