<?php

namespace Obsidian\Core;

class Session {

    protected static $session;

    // public function __construct()
    // {
    //     session_name('Obsidian'.Configuration::get('session','name'));
    //     $this->session = session_start([
    //         'cookie_lifetime' => Configuration::get('session', 'time'),
    //         'cookie_secure'   => 1,
    //         'cookie_httponly' => 1,
    //     ]);
    //     if (!$this->session) {
    //         \Obsidian\Core\Error::show(Logger::error('Failed to start session!'));
    //     }
    // }

    public static function start() {
        session_name('Obsidian'.Configuration::get('session','name'));
        self::$session = session_start([
            'cookie_lifetime' => Configuration::get('session', 'time'),
            'cookie_secure'   => 1,
            'cookie_httponly' => 1,
        ]);
        if (!self::$session) {
            \Obsidian\Core\Error::show(Logger::error('Failed to start session!'));
        }
    }

    public static function login(string $username, string $password, string $hash) {
        if (Auth::verifyPassword($password, $hash)) {
            self::set('username', $username);
            self::set('signature', Auth::generateSignature());
            return true;
        } else {
            Logger::error("Failed login for $username");
            return false;
        }
    }

    public static function logout() {
        unset($_COOKIE['Obsidian'.Configuration::get('session','name')]);
        $_SESSION = [];
        session_destroy();
    }

    public static function id() {
        return session_id();
    }

    public static function get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return false;
    }

    public static function set($name, $value = '')
    {
        if (empty($value)) {
            unset($_SESSION[$name]);
        } else {
            $_SESSION[$name] = $value;
        }
        return $value;
    }

    public function __set($name, $value = '')
    {
        if (empty($value)) {
            unset($_SESSION[$name]);
        } else {
            $_SESSION[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return false;
    }

}