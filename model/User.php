<?php

class User
{
    private $table = 'users';

    public static function find($username)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = ?');
        $stmt->execute(array($username));

        return $stmt->fetch();
    }
}
