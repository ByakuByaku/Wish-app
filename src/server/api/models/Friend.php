<?php

require_once __DIR__ . '/../core/Database.php';

class Friend
{
    public static function areFriends(int $userId, int $otherId): bool
    {
        if ($userId === $otherId) {
            return true;
        }

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT 1 FROM friends
            WHERE (user_id = ? AND friend_id = ?)
               OR (user_id = ? AND friend_id = ?)
            LIMIT 1
        ");

        $stmt->execute([$userId, $otherId, $otherId, $userId]);

        return (bool) $stmt->fetchColumn();
    }

    public static function getFriends($userId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT u.id, u.name, u.email, u.age
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
            OR (user_id = ? AND friend_id = ?)
        ");

        return $stmt->execute([$userId, $friendId, $friendId, $userId]);
    }

    public static function createInvite($userId)
{
    $db = Database::connect();
    $delete = $db->prepare("DELETE FROM friend_invites WHERE user_id = ?");
    $delete->execute([$userId]);
    $code = substr(bin2hex(random_bytes(4)), 0, 8);
    $stmt = $db->prepare("
        INSERT INTO friend_invites (code, user_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$code, $userId]);
    return $code;
}

public static function useInvite($code, $userId)
{
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT user_id FROM friend_invites
        WHERE code = ?
    ");
    $stmt->execute([$code]);
    $invite = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$invite) return false;
    $ownerId = (int) $invite['user_id'];
    if ($ownerId === $userId) return false;
    $stmt2 = $db->prepare("
        INSERT OR IGNORE INTO friends (user_id, friend_id) VALUES (?, ?)
    ");
    $stmt2->execute([$userId, $ownerId]);
    $stmt2->execute([$ownerId, $userId]);
    $stmt3 = $db->prepare("DELETE FROM friend_invites WHERE code = ?");
    $stmt3->execute([$code]);
    return true;
}
}