<?php
ob_start();  // Prevent premature output
require_once __DIR__ . '/../models/Auth.php';
session_start();

function login_page($app) {
    ($app->render)("standard", "authentication/login");
}

function login_user() {
    try {
        $username = filter_input(INPUT_POST, 'username', FILTER_DEFAULT);
        $username = trim($username);
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

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
