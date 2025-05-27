<?php
require_once __DIR__ . '/../models/Auth.php';  // loads the Auth class
session_start();

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
