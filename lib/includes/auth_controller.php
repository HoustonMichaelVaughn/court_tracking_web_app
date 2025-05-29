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