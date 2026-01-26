<?php

class Connect
{
    protected $db;
    public $error;

    /**
     * Initializes a new instance of the class and establishes a database connection.
     *
     * Attempts to retrieve database connection parameters from environment variables or defaults.
     * Initializes the PDO database connection with the specified settings.
     * Creates necessary database tables upon successful connection.
     *
     * @return void
     */
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

    /**
     * Retrieves the value of an environment variable. Searches in the following order:
     * - $_ENV array
     * - $_SERVER array
     * - getenv function
     * If the variable is not found or is empty, the default value is returned.
     *
     * @param string $key The name of the environment variable to look up.
     * @param mixed $default The default value to return if the environment variable is not set or is empty.
     * @return mixed The value of the environment variable, or the default value if not found.
     */
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

    /**
     * Creates the "events" table in the database if it does not already exist.
     * The table includes fields for event ID, title, date, and description.
     *
     * @return $this Returns the current instance for method chaining.
     */
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

    /**
     * Creates the users table in the database if it does not already exist.
     *
     * The users table includes the following columns:
     * - id: Primary key, auto-incremented.
     * - name: User's name, a non-null string with a maximum length of 30 characters.
     * - year: Enum type representing the user's year, defaulting to '1'.
     * - email: User's email, a non-null unique string with a maximum length of 50 characters.
     * - createdAt: Timestamp of when the record was created, defaults to the current timestamp.
     * - updatedAt: Timestamp of the last update, defaults to the current timestamp and updates on modification.
     * - deletedAt: Nullable timestamp for soft deletion.
     *
     * @return $this Returns the current instance for method chaining.
     */
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

    /**
     * Creates the `user_requests` table in the database if it does not already exist.
     *
     * The `user_requests` table contains the following columns:
     * - `id`: Primary key, auto-incrementing.
     * - `user_id`: Foreign key referencing the `id` column in the `users` table.
     * - `event_id`: Foreign key referencing the `id` column in the `events` table.
     *
     * This method executes the SQL query to create the table and handles any exceptions
     * that might occur during the execution.
     *
     * @return $this Returns the current instance for method chaining.
     */
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

    /**
     * Retrieves the database connection instance.
     *
     * @return PDO|null Returns the PDO instance representing the database connection, or null if not initialized.
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Retrieves the error message.
     *
     * @return string|null The error message, or null if no error exists.
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * Automatically releases database resources when the object is destroyed.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->db = null;
    }
}
