<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Model {

    protected $properties = [];

    protected $table = '';

    public function __construct(?array $data = [])
    {
        if (is_array($data) && !empty($data)) {
            $this->load($data);
        }
    }

    protected function fetch($query, ...$params) {
        // Check database connection
        if (Application::database()) {
            $result = Application::database()->query($query, ...$params)->getFirst();
            if ($result) {
                $this->load($result);
                return true;
            }
            return  false;
        }
    }

    protected function select(array $tables, array $queries = [], string $columns = null) {
        if (Application::database()) {
            $command = 'SELECT';
            $tables = implode(',', $tables);
            $columns = (!is_null($columns))? $columns : implode(',', array_keys($this->properties));
            if (!empty($queries) && count($queries) > 0) {
                $queryString = 'WHERE ' . array_key_first($queries) . ' = ?';
                $params[] = $queries[array_key_first($queries)];
                $queryKeys = array_keys($queries);
                for ($i=1; $i < count($queryKeys); $i++) { 
                    $queryString .= ' AND ' . $queryKeys[$i] . ' = ?';
                    $params[] = $queries[$queryKeys[$i]];
                }
            }
            $finishedQuery = "$command $columns FROM $tables $queryString";
            return $this->fetch($finishedQuery, ...$params);
        }
    }

    protected function update() {
        if (Application::database()) {
            $command = 'UPDATE'; // SQL COMMAND
            $primary = $this->findPrimary();
            $properties = array_keys($this->properties); // GET A LIST OF COLUMNS
            if ($primary) {
                // Make sure we're not setting primary column
                unset($properties[array_search($primary, $properties)]);
            }
            $columns = implode(' = ?,', $properties) . ' = ?'; // PREPARE LIST AS STRING
            foreach ($properties as $property) {
                $values[] = $this->$property; // Array of property values
            }
            array_push($values, $this->id);
            $query = "$command {$this->table} SET $columns WHERE `id` = ?";
            $result = Application::database()->query($query, ...$values);
            if ($result) {
                return $result;
            }
            Error::show('Failed to update: ' . $query);
            return false;
        }
    }

    protected function insert() {
        if (Application::database()) {
            $command = 'INSERT INTO';
            $primary = $this->findPrimary();
            $properties = array_keys($this->properties);
            if ($primary) {
                unset($properties[array_search($primary, $properties)]);
            }
            $columns = implode(',', $properties);
            $vals = '';
            foreach ($properties as $property) {
                $values[] = $this->$property;
                $vals .= '?, ';
            }
            $vals = trim($vals, ', ');
            $query = "$command {$this->table} ($columns) VALUES ($vals)";
            $result = Application::database()->query($query, ...$values);
            if ($result) {
                return $result;
            }
            Error::show('Failed to insert: ' . $query . PHP_EOL . Application::database()->getLast());
            return false;
        }
    }

    protected function delete() {
        if (Application::database()) {
            $command = 'DELETE FROM';
            $primary = $this->findPrimary();

            $query = "$command {$this->table} WHERE {$primary} = ?";
            $values[] = $this->$primary;
            $result = Application::database()->query($query, ...$values);
            if ($result) {
                return $result;
            }
            Error::show('Failed to delete: ' . $query . PHP_EOL . Application::database()->getLast());
            return false;
        }
    }

    protected function load(array $data) {
        foreach ($data as $property => $value) {
            if (array_key_exists($property, $this->properties) && $this->validateProperty($property, $value)) {
                $this->$property = $value;
            }
        }
    }

    public function show() {
        $vars = get_object_vars($this);
        unset($vars['properties']);
        return $vars;
    }

    // protected function save(string $table) {

    // }

    public function __set($property, $value) {
        if (array_key_exists($property, $this->properties) && $this->validateProperty($property, $value)) {
            $this->$property = $value;
            return true;
        }
        return false;
    }

    public function __get(string $property) {
        if (isset($this->$property)) {
            return $this->$property;
        }
        return false;
    }

    /**
     * Validate property value against defined setting, e.g. email => string
     *
     * @param string $property
     * @param [type] $value
     * @return boolean
     */
    protected function validateProperty(string $property, $value): bool {
        $options = explode(',', $this->properties[$property]);
        foreach ($options as $option) {
            $method = 'validate'. ucfirst($option);
            if (method_exists($this,$method)) {
                $result = $this->{$method}($value);
                // Exception
                if (!$result) {
                    $type = gettype($value);
                    \Obsidian\Core\Error::show((Logger::error("$property failed validation for $method with value $value ($type)")));
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Find property containing the primary value e.g. id
     *
     * @param string $primary value to search for. 'primary' by default.
     * @return void
     */
    private function findPrimary($primary = 'primary') {
        $keys = [];
        foreach ($this->properties as $key => $value) {
            if (strpos($value, $primary) !== false) {
                $keys[] = $key;
            }
        }
        switch (count($keys)) {
            case 0:
                return false;
                break;
            case 1:
                return $keys[0];
                break;
            default:
                Error::show('More than 1 primary property found in ' . get_called_class() .  "\n" . implode(',',$keys));
                break;
        }
    }

    private function validateInteger($value) {
        return is_int($value) || is_double($value) || is_numeric($value);
    }

    private function validateString($value) {
        return is_string($value);
    }

    private function validateRequired($value) {
        return isset($value) || empty($value) || is_null($value);
    }

    private function validateJSON($value) {
        return is_array(json_decode($value,1));
    }

    private function validatePrimary($value) {
        return true;
    }

}