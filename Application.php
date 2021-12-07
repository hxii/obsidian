<?php

namespace Obsidian;

use Obsidian\Core\Configuration;
use Obsidian\Core\Logger;
use Obsidian\Core\Router;
use Obsidian\Core\Session;

class Application {

    protected static $database, $router, $logger, $session;

    public function __construct(array $configuration)
    {
        Configuration::read($configuration);
        Logger::enable(Configuration::get('logging','level'), Configuration::get('logging','file'));
        self::$database = new \Obsidian\Core\Database();
        Session::start();
    }

    public static function database() {
        return self::$database;
    }

    public static function session() {
        return self::$session;
    }

    public function execute() {
        Router::execute();
    }

}