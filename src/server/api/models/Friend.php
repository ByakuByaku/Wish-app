<?php

require_once __DIR__ . '/../core/Database.php';

class Friend
{
    public static function getFriends($userId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT u.*
            FROM friends f
            JOIN users u ON u.id = f.friend_id
            WHERE f.user_id = ?
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addFriend($userId, $friendId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO friends (user_id, friend_id)
            VALUES (?, ?)
        ");

        return $stmt->execute([$userId, $friendId]);
    }

    public static function removeFriend($userId, $friendId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            DELETE FROM friends
            WHERE user_id = ? AND friend_id = ?
        ");

        return $stmt->execute([$userId, $friendId]);
    }
}