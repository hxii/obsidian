<?php

namespace Obsidian\Core;

class JSONSettings {

    protected $defaults = [];

    public function __construct($data = null)
    {
        $this->load($data);
    }

    protected function load($data) {
        if (is_string($data)) {
            $data = json_decode($data, 1);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Error::show(Logger::error('Failed to decode settings string: ' . json_last_error_msg()));
            }
        }
        $settings = array_merge($this->defaults, $data);
        unset($this->defaults);
        foreach ($settings as $setting => $value) {
            $this->$setting = $value;
        }
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

    public function __get(string $property) {
        if (isset($this->$property)) {
            return $this->$property;
        }
        return false;
    }

    public function __toString()
    {
        var_dump(get_object_vars($this));
    }

}