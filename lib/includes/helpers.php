<?php
function render_error($app, $message) {
    // helper to display errors on standard error page
    ($app->render)('standard', 'error', ['message' => $message]);
    exit;
}

function redirect_with_success($path, $message) {
    // helper to redirect to $path with custom success message
    $successMessage = urlencode($message);
    header("Location: " . BASE_URL . $path . "?success=" . $successMessage);
    exit;
}

function extract_post_data(array $fields): array {
    // function used by Lawyer and Defendant to extract POST data based on $fields
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }
    return $data;
}

function cancel_case_wizard() {
    // function cancels session data
    unset($_SESSION['case']);
    header("Location: " . BASE_URL . "/cases");
    exit;
}

function require_login() {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
}

function require_role($requiredRole) {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        http_response_code(403); // Forbidden
        echo "<h1>403 Forbidden</h1><p>You do not have permission to view this page.</p>";
        exit();
    }
}