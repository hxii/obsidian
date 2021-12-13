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
            $result = Application::database()->query($query, ...$params);
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
            echo '<pre>'.var_export($this->findPrimary(),1).'</pre>';
            $properties = array_keys($this->properties); // GET A LIST OF COLUMNS
            $properties = implode(' = ?,', $properties) . ' = ?'; // PREPARE LIST AS STRING
            foreach ($this->properties as $property => $value) {
                // if (strpos('primary', $value)) continue;
                $values[] = $this->$property;
            }
            array_push($values, $this->id);
            $query = "$command {$this->table} SET $properties WHERE `id` = ?";
            echo '<pre>'.var_export($query,1).'</pre>';
            echo '<pre>'.var_export($values,1).'</pre>';
            // $result = Application::database()->query($query, ...$values);
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

    private function findPrimary() {
        $keys = [];
        foreach ($this->properties as $key => $value) {
            if (strpos($value, 'primary')) {
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
        return is_int($value);
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