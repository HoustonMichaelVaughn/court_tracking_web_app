<?php

class User {
    public static function findByUsername($conn, $username) {
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public static function findById($db, $id) {
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
} 