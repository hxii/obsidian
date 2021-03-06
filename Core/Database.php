<?php

namespace Obsidian\Core;

use Obsidian\Core\Configuration;
use Obsidian\Core\Logger;

class Database {

    protected $connection;
    private $results;

    public function __construct()
    {
        $configuration = Configuration::get('database');
        if (!$configuration) {
            die(Logger::error('Database configuration missing!'));
        }
        $link = mysqli_connect($configuration['host'], $configuration['user'], $configuration['password']);
        $db = mysqli_select_db($link, $configuration['database']);
        if (!$link || !$db) {
            if (class_exists('Logger')) Logger::error('Could not connect to database:' .  mysqli_connect_error() );
            die('Could not connect to database:' .  mysqli_connect_error() );
        }
        $link->set_charset("utf8mb4");
        $this->connection = $link;
        Logger::debug("Database connected {$configuration['user']}@{$configuration['host']}:{$configuration['database']}");
        return true;
    }

    public function __destruct()
    {
        if ($this->connection) $this->connection->close();
    }

    public function error() {
        return $this->connection->error ?? null;
    }

    public function getLast() {
        if ($this->connection) {
            return mysqli_insert_id($this->connection);
        }
    }

    public function query(string $query, ...$vars) {
        if ($this->connection) {
            $stmt = mysqli_prepare($this->connection, $query);
            if (!$stmt || is_bool($stmt)) {
                die(Logger::error('Query failed! QUERY: ' . $query . PHP_EOL . mysqli_error($this->connection)));
            }
            $types = '';
            foreach ($vars as $var) {
                $type = gettype($var);
                $types .= ('integer' === $type)? 'i' : 's';
            }
            mysqli_stmt_bind_param($stmt, $types, ...$vars);
            mysqli_stmt_execute($stmt);
            $rows = $stmt->get_result();
            if (0 !== mysqli_stmt_errno($stmt)) {
                $error = "SQL Error: ({$stmt->errno}) {$stmt->error}";
                Logger::error($error);
                return false;
            }
            if (is_bool($rows)) {
                return true;
            }
            $this->results = [];
            while ($row = $rows->fetch_array(MYSQLI_ASSOC)) {
                $this->results[] = $row;
            }
            return $this;
        }
    }

    public function getList() {
        return $this->results;
    }

    public function getFirst() {
        return (is_array($this->results) && isset($this->results[0]))? $this->results[0] : false;
    }

    private function esc(&$data) {
        if (!$this->connection) {
            if(class_exists('Logger')) Logger::error('Database connection not available');
            die('Database connection not available');
        }
        if (is_string($data)) {
            $data = mysqli_real_escape_string($this->connection, $data);
        }
        return $data;
    }

}