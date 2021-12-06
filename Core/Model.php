<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Model {

    protected $properties = [];

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

    protected function save(string $table) {

    }

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

    private function validateInteger($value) {
        return is_int($value);
    }

    private function validateString($value) {
        return is_string($value);
    }

    private function validateRequired($value) {
        return isset($value) || empty($value) || is_null($value);
    }

}