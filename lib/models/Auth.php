<?php
require_once __DIR__ . '/../includes/Database.php';

class Auth {
    public static function login($username, $password) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) throw new Exception("User not found");

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        return true;
    }

    public static function logout() {
        session_destroy();
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}
