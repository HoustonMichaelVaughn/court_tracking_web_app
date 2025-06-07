<?php


ob_start();
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
            session_regenerate_id(true);
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

function manage_accounts($app) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    $accounts = Auth::getAllUsers();
    ($app->render)("standard", "authentication/manage_accounts", ['accounts' => $accounts]);
}

function delete_user($id) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    if ($_SESSION['user_id'] == $id) {
        $_SESSION['message'] = "You cannot delete your own account.";
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;
    }

    Auth::deleteUser($id);
    $_SESSION['message'] = "Account deleted successfully.";
    header("Location: " . BASE_URL . "/accounts/manage");
    exit;
}

function edit_user_page($app, $id) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    $user = Auth::getUserById($id);

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;
    }

    ($app->render)("standard", "authentication/edit_account", ['user' => $user]);
}

function update_user($id) {
    try {
        if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
            header("Location: " . BASE_URL . "/");
            exit;
        }

        $username = $_POST['username'];
        $role = $_POST['role'];

        $allowedRoles = ['admin', 'user'];
        if (!in_array($role, $allowedRoles)) {
            $_SESSION['error'] = "Invalid role selected.";
            header("Location: " . BASE_URL . "/accounts/manage");
            exit;
        }

        Auth::updateUser($id, $username, $role);

        $_SESSION['message'] = "User updated successfully.";
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;

    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        header("Location: " . BASE_URL . "/accounts/manage");
        exit;
    }
}