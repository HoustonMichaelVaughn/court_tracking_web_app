<?php

class User {
    public static function findByUsername($conn, $username) {
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
} 