<?php

namespace Controllers;

use Obsidian\Core\Auth;
use Obsidian\Core\Template;

class Home {

    public static function view() {
        var_dump(Auth::isLoggedIn());
        // Template::view('index.html');
    }

}