<?php

namespace Obsidian\Core;

class Session {

    protected $session;

    public function __construct()
    {
        session_name('Obsidian'.Configuration::get('session','name'));
        $this->session = session_start([
            'cookie_lifetime' => Configuration::get('session', 'time'),
            'cookie_secure'   => 1,
            'cookie_httponly' => 1,
        ]);
        if (!$this->session) {
            \Obsidian\Core\Error::show(Logger::error('Failed to start session!'));
        }
    }

    public function login(string $username, string $password, string $hash) {
        if (Auth::verifyPassword($password, $hash)) {
            $this->username = $username;
            $this->signature = Auth::generateSignature();
        } else {
            Logger::error("Failed login for $username");
        }
    }

    public function logout() {
        unset($_COOKIE['Obsidian'.Configuration::get('session','name')]);
        $_SESSION = [];
        session_destroy();
    }

    public function id() {
        return session_id();
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