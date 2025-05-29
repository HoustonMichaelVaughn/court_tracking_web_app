<?php
require_once __DIR__ . '/../includes/Database.php';


class Auth {
    public static function login($username, $password) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception("User not found");
        }

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

    public static function register($username, $password, $confirm) {
        if ($password !== $confirm) {
            throw new Exception("Passwords do not match.");
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            throw new Exception("Username already exists.");
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $staffType = $_POST['staff_type'];
        $role = ($staffType === 'admin') ? 'admin' : 'user';

        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
    }

    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function getCurrentUser() {
    if (!self::isAuthenticated()) {
        return null;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();  // returns user as associative array
    }

    public static function getAllUsers(): array {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, username, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function deleteUser(int $id): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public static function getUserById(int $id): ?array {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    
    public static function updateUser(int $id, string $username, string $role): bool {
        $allowedRoles = ['admin', 'user'];
        if (!in_array($role, $allowedRoles, true)) {
            throw new InvalidArgumentException("Invalid role: $role");
        }
    
        $db = Database::getInstance()->getConnection();
    
        // Duplicate check to prevent unique constraint violation
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $id]);
        if ($checkStmt->fetch()) {
            throw new Exception("Username already exists.");
        }
    
        $stmt = $db->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        return $stmt->execute([$username, $role, $id]);
    }
    
}
