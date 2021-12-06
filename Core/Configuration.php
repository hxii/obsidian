<?php

namespace Obsidian\Core;

class Configuration {

    protected static $configuration = [];

    public function __construct()
    {
        // self::read()
    }

    public static function read(array $configuration) {
        foreach ($configuration as $property => $value) {
            self::$configuration[$property] = $value;
        }
    }

    // public static function get(string $property) {
    //     if (isset(self::$configuration[$property])) {
    //         return self::$configuration[$property];
    //     } else {
    //         Logger::error("$property not found in configuration");
    //         return false;
    //     }
    // }

    public static function get(...$properties) {
        $configuration = self::$configuration;
        foreach($properties as $property) {
            if (array_key_exists($property, $configuration)) {
                $configuration = $configuration[$property];
            } else {
                return false;
            }
        }
        return $configuration;
    }

    public static function set(string $property, $value) {

    }

}