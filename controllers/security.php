<?php

function enforce_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
        $_SERVER['REQUEST_METHOD'] === 'PUT' || 
        $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            throw new Exception("CSRF token invalid or missing.");
        }
    }
}

function require_protected_access($app, $callback) {
    require_once __DIR__ . '/../models/Auth.php';

    if (!Auth::isAuthenticated()) {
        header("Location: " . BASE_URL . "/login");
        exit;
    }

    enforce_csrf();
    $callback($app);
}