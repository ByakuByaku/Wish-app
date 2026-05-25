<?php

require_once __DIR__ . '/../core/Database.php';

class Role {

    const ADMIN = 0;
    const USER = 1;

    public static function isAdmin($user)
    {
        return $user['role'] == self::ADMIN;
    }

}