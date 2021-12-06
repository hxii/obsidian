<?php

namespace Obsidian;

use Obsidian\Core\Configuration;
use Obsidian\Core\Logger;
use Obsidian\Core\Router;

class Application {

    protected static $database, $router, $logger;

    public function __construct(array $configuration)
    {
        Configuration::read($configuration);
        Logger::enable($configuration['logging']['level'], $configuration['logging']['file']);
        self::$database = new \Obsidian\Core\Database();
    }

    public static function database() {
        return self::$database;
    }

    public function execute() {
        Router::execute();
    }

}