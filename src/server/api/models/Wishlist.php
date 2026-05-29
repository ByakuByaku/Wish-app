<?php

require_once __DIR__ . '/../core/Database.php';

class Wishlist {
    public static function getWishlist($id) {
        $db = Database::connect();

        $sql = "SELECT * FROM wishlists WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByUserID($userId) {
        $db = Database::connect();

        $sql = "
            SELECT w.*, COUNT(wi.id) AS items_count
            FROM wishlists w
            LEFT JOIN wishlist_items wi ON wi.wishlist_id = w.id
            WHERE w.user_id = :user_id
            GROUP BY w.id
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllPublic($id) {
        $db = Database::connect();

        $sql = "SELECT * FROM wishlists WHERE is_public = 1 AND user_id != :user_id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['user_id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function createWishlist($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO wishlists (user_id, name, description, is_public)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['description'] ?? null,
            $data['is_public'] ?? 0
        ]);

        return $db->lastInsertId();
    }

    public static function updateWishlist($id, $data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE wishlists
            SET name = ?, description = ?, is_public = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['is_public'] ?? 0,
            $id
        ]);
    }
    
    public static function deleteWishlist($id) {
        $db = Database::connect();

        $sql = "DELETE FROM wishlists WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

}