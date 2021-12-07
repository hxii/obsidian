<?php

namespace Controllers;

use \Obsidian\Core\Auth;
use \Obsidian\Core\Router;
use \Obsidian\Core\Session;
use \Obsidian\Core\Error;

class Login {

    public static function process() {
        if (!Auth::isLoggedIn()) {
            $user = new \Models\User();
            if($user->fetchByUsername(Router::currentRoute()->post['username'])) {
                $log = Session::login($user->username, Router::currentRoute()->post['password'], $user->password);
            } else {
                Error::show('User does not exist');
            }
        }
    }

    public static function logout() {
        if (Auth::isLoggedIn()) {
            Session::logout();
            Router::redirect('/', 200);
        }
    }


}