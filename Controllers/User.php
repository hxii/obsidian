<?php

namespace Controllers;
class User {
    protected $user;

    public function __construct($handle)
    {
        $this->user = new \Models\User();
        if (!$this->user->fetchByUsername($handle)) {
            \Obsidian\Core\Error::show("User $handle doesn't exist!");
        }
    }

    public static function viewList() {
        return 1;
    }
}