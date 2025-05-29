<?php


ob_start();  // Prevent premature output
require_once __DIR__ . '/../models/Auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function login_page($app) {
    ($app->render)("standard", "authentication/login");
}

function login_user() {
    try {
        $username = filter_input(INPUT_POST, 'username', FILTER_DEFAULT);
        $username = trim($username);
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid CSRF token");
        }
        if (!$username || !preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            throw new Exception("Invalid username format.");
        }

        if (!$password || strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters.");
        }

        Auth::login($username, $password);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);  // prevent session fixation
        }

        header("Location: " . BASE_URL . "/");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: " . BASE_URL . "/login");
        exit;
    }
}

function logout_user() {
    Auth::logout();
    header("Location: " . BASE_URL . "/login");
    exit;
}

function register_page($app) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    ($app->render)("standard", "authentication/register");
}

function register_user() {
    try {
        $staffType = $_POST['staff_type'] ?? null;
        $role = ($staffType === 'admin') ? 'admin' : 'user';

        $username = filter_input(INPUT_POST, 'username', FILTER_DEFAULT);
        $username = trim($username);
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
        $confirm = filter_input(INPUT_POST, 'confirm', FILTER_UNSAFE_RAW);

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid CSRF token");
        }
        if (!$username || !preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            throw new Exception("Invalid username format.");
        }

        if (!$password || strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters.");
        }

        if ($password !== $confirm) {
            throw new Exception("Passwords do not match.");
        }

        Auth::register($username, $password, $confirm, $role);

        $_SESSION['success'] = "Staff registered successfully.";
        header("Location: " . BASE_URL . "/");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: " . BASE_URL . "/register");
        exit;
    }
}

// managing accounts:

function manage_accounts($app) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, username, role, staff_type FROM users");
    $accounts = $stmt->fetchAll();

    ($app->render)("standard", "authentication/manage_accounts", ['accounts' => $accounts]);
}

function delete_user($id) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    // prevents deleting current admin account
    if ($_SESSION['user_id'] == $id) {
        $_SESSION['message'] = "You cannot delete your own account.";
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = "Account deleted successfully.";
    header("Location: " . BASE_URL . "/accounts/manage");
    exit;
}

function edit_user_page($app, $id) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, username, role, staff_type FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;
    }

    ($app->render)("standard", "authentication/edit_account", ['user' => $user]);
}

function update_user($id) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    $username = $_POST['username'];
    $role = $_POST['role'];
    $staffType = $_POST['staff_type'];

    $allowedRoles = ['admin', 'user'];
    if (!in_array($role, $allowedRoles)) {
        $_SESSION['error'] = "Invalid role selected.";
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET username = ?, role = ?, staff_type = ? WHERE id = ?");
    $stmt->execute([$username, $role, $staffType, $id]);

    $_SESSION['message'] = "User updated successfully.";
    header("Location: " . BASE_URL . "/accounts/manage");
    exit;
}
