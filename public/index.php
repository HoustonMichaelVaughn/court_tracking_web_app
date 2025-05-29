<?php

define('BASE_URL', '/court_tracking_web_app/public');

require_once '../lib/includes/mouse.php';
require_once '../lib/includes/auth_controller.php'; // for login/logout/register logic
require_once '../lib/includes/security.php';         // for CSRF and auth checks


// Public: Homepage
path('/', function($app) {
    require_once __DIR__ . '/../lib/includes/home_controller.php';
});

// Protected views
path('/cases', function($app) {
    require_protected_access($app, function($app) {
        ($app->render)('standard', 'manage_entities/manage_cases');
    });
});

path('/defendants', function($app) {
    require_protected_access($app, function($app) {
        ($app->render)('standard', 'manage_entities/manage_defendants');
    });
});

path('/lawyers', function($app) {
    require_protected_access($app, function($app) {
        ($app->render)('standard', 'manage_entities/manage_lawyers');
    });
});

path('/logs', function($app) {
    require_protected_access($app, function($app) {
        require_once __DIR__ . '/../lib/includes/logs_controller.php';
    });
});

// Protected controllers
foreach (['defendant', 'charge', 'lawyer', 'event', 'case'] as $entity) {
    path("/$entity/{action}", function($app, $action) use ($entity) {
        require_protected_access($app, function($app) use ($entity, $action) {
            require_once __DIR__ . "/../lib/includes/{$entity}_controller.php";
        });
    });

    path("/$entity/{action}/{id}", function($app, $action, $id) use ($entity) {
        require_protected_access($app, function($app) use ($entity, $action, $id) {
            require_once __DIR__ . "/../lib/includes/{$entity}_controller.php";
        });
    });
}

// Public: login/register
path('/login', function($app) {
    login_page($app);
});

path('/login/submit', function($app) {
    login_user();
});

path('/logout', function($app) {
    logout_user();
});

path('/register', function($app) {
    register_page($app);
});

path('/register/submit', function($app) {
    register_user();
});

// manage accounts:

path('/accounts/manage', function($app) {
    require_once __DIR__ . '/../lib/includes/auth_controller.php';
    manage_accounts($app);
});

path('/accounts/delete/{id}', function($app, $id) {
    require_once __DIR__ . '/../lib/includes/auth_controller.php';
    delete_user($id);
});

path('/accounts/edit/{id}', function($app, $id) {
    require_once __DIR__ . '/../lib/includes/auth_controller.php';
    edit_user_page($app, $id);
});

path('/accounts/edit/{id}/submit', function($app, $id) {
    require_once __DIR__ . '/../lib/includes/auth_controller.php';
    update_user($id);
});

resolve();
