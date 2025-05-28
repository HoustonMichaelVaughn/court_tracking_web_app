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
        $_SESSION['staff_type'] = $user['staff_type'];
        return true;
    }

    public static function logout() {
        session_destroy();
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    private static function get_db() {
        return new PDO("mysql:host=localhost;dbname=court_tracking_system", "root", "");
    }

    public static function register($username, $password, $confirm) {
        if ($password !== $confirm) {
            throw new Exception("Passwords do not match.");
        }

        $db = self::get_db();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            throw new Exception("Username already exists.");
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $staffType = $_POST['staff_type'];
        $role = ($staffType === 'admin') ? 'admin' : 'user';

        $stmt = $db->prepare("INSERT INTO users (username, password, role, staff_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hash, $role, $staffType]);
    }

    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

