<?php
//  Context/This is here because going from C, to Django, to PHP makes me forget how words work.
// ($app->render)('standard', 'home') is the weird way function calls happen in php sometimes.
// This is done to avoid confusion with methods. In this case ->render is being called but is defined in compile_app()
// Same occurs with ->set_message //

define('BASE_URL', '/court_tracking_web_app/public');

require_once '../lib/includes/mouse.php';
 
path('/', function($app) {
    require_once __DIR__ . '/../lib/includes/home_controller.php';
});

path('/cases', function($app) {
    ($app->render)('standard', 'manage_entities/manage_cases');
});

path('/defendants', function($app) {
    ($app->render)('standard', 'manage_entities/manage_defendants');
});

path('/lawyers', function($app) {
    ($app->render)('standard', 'manage_entities/manage_lawyers');
});

path('/logs', function($app){
    require_once __DIR__ . '/../lib/includes/logs_controller.php';
});

// DATABASE REQUESTS
path('/defendant/{action}', function($app, $action) {
    require_once __DIR__ . '/../lib/includes/defendant_controller.php';
});

path('/defendant/{action}/{id}', function($app, $action, $defendantID) {
    require_once __DIR__ . '/../lib/includes/defendant_controller.php';
});

path('/charge/{action}', function($app, $action) {
    require_once __DIR__ . '/../lib/includes/charge_controller.php';
});

path('/charge/{action}/{id}', function($app, $action, $chargeID) {
    require_once __DIR__ . '/../lib/includes/charge_controller.php';
});

path('/lawyer/{action}', function($app, $action) {
    require_once __DIR__ . '/../lib/includes/lawyer_controller.php';
});

path('/lawyer/{action}/{id}', function($app, $action, $lawyerID) {
    require_once __DIR__ . '/../lib/includes/lawyer_controller.php';
});

path('/event/{action}', function($app, $action) {
    require_once __DIR__ . '/../lib/includes/event_controller.php';
});

path('/event/{action}/{id}', function($app, $action, $eventID) {
    require_once __DIR__ . '/../lib/includes/event_controller.php';
});

path('/case/{action}', function($app, $action) {
    require_once __DIR__ . '/../lib/includes/case_controller.php';
});

path('/case/{action}/{caseID}', function($app, $action, $caseID) {
    require_once __DIR__ . '/../lib/includes/case_controller.php';
});

require_once __DIR__ . '/../lib/includes/auth_controller.php'; // for authentication

// display login page
path('/login', function($app) {
    login_page($app);
});

// handle login form submission
path('/login/submit', function($app) {
    login_user();
});

// handles logout
path('/logout', function($app) {
    logout_user();
});

resolve();
