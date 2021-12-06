<?php

namespace Controllers;

use Obsidian\Application;
use Obsidian\Core\Template;

class Home {

    public static function view() {
        $session = Application::session();
        Template::view('index.html');
    }

}