<?php

require_once __DIR__ . '/../core/Database.php';

class User{
    public static function getAll() {
        $db = Database::connect();

        $sql = "SELECT * FROM users";
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getById($id) {
        $db = Database::connect();

        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getByEmail($email){
        $db = Database::connect();

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function register($data){
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO users (name, email, age, password_hash, role)
            VALUES (?, ?, ?, ?, 2)
        ");

        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['age'] ?? null,
            $data['password_hash'] ?? null
        ]);
    }
    
    public static function updatePassword($id, $password) {
        $db = Database::connect();
        
        $stmt = $db->prepare("
            UPDATE users 
            SET password_hash = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }
    
    public static function delete($id) {
        $db = Database::connect();
        
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public static function login($email, $password) {
        $db = Database::connect();
        
        $user = self::getByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'] ?? '')) {
            return $user;
        }
        
        return null;
    }
}
