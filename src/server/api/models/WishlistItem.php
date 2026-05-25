<?php

require_once __DIR__ . '/../core/Database.php';

class WishlistItem{
    public static function getByWishlistId($wishlistId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * FROM wishlist_items
            WHERE wishlist_id = ?
        ");

        $stmt->execute([$wishlistId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getById($id)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * FROM wishlist_items
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function createItem($data)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO wishlist_items (wishlist_id, name, description, price)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['wishlist_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['price'] ?? null
        ]);

        return $db->lastInsertId();
    }
    
    public static function updateItem($id, $data)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE wishlist_items
            SET name = ?, description = ?, price = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['name'] ?? null,
            $data['description'] ?? '',
            $data['price'] ?? null,
            $id
        ]);
    }
    
    public static function reserve($itemId, $userId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE wishlist_items
            SET reserved_by = ?
            WHERE id = ?
        ");

        return $stmt->execute([$userId, $itemId]);
    }
    
    public static function unreserve($itemId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE wishlist_items
            SET reserved_by = NULL
            WHERE id = ?
        ");

        return $stmt->execute([$itemId]);
    }
    
    public static function delete($id)
    {
        $db = Database::connect();

        $stmt = $db->prepare("DELETE FROM wishlist_items WHERE id = ?");
        return $stmt->execute([$id]);
    }
}