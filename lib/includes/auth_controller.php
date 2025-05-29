<?php
require_once __DIR__ . '/../models/Auth.php';  // loads the Auth class
session_start();

// login system: 

function login_page($app) {
    ($app->render)("standard", "authentication/login");
}

function login_user() {
    try {
        Auth::login($_POST['username'], $_POST['password']);
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

// registration system:

function register_page($app) {
    if (!Auth::isAuthenticated() || !Auth::isAdmin()) {
        header("Location: " . BASE_URL . "/");
        exit;
    }

    ($app->render)("standard", "authentication/register");
}

function register_user() {
    try {
        $staffType = $_POST['staff_type'];
        $role = ($staffType === 'admin') ? 'admin' : 'user';

        Auth::register($_POST['username'], $_POST['password'], $_POST['confirm'], $role);

        // Don't log out the current user — stay logged in and redirect to staff management
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