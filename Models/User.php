<?php

namespace Models;

use Obsidian\Application;
use Obsidian\Core\Database;
use \Obsidian\Core\Model;

class User extends Model {

    protected $properties = [
        'id' => 'required,integer',
        'name' => 'string',
        'updated_at' => '',
        'email' => '',
        'username' => 'string,required'
    ];

    public function fetchByID(int $userId) {
        return $this->fetch('SELECT * FROM users WHERE id = ?', $userId);
    }

    public function fetchByEmail(string $userEmail) {
        return $this->fetch('SELECT * FROM users WHERE email = ?', $userEmail);
    }

    public function fetchByUsername(string $username) {
        return $this->fetch('SELECT * FROM users where username = ?', $username);
    }

    public function save(string $table) {
        // var_dump(get_class_vars(__CLASS__));
        // var_dump($this);
        var_dump(get_object_vars($this));
    }

}