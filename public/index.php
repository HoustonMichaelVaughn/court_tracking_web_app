<?php

define('BASE_URL', '/court_tracking_web_app/public');
define('VIEWS', dirname(__DIR__) . '/views');

require_once '../controllers/mouse.php';
require_once '../controllers/auth_controller.php'; // for login/logout/register logic
require_once '../controllers/security.php';         // for CSRF and auth checks

// Public: Homepage
path('/', function($app) {
    require_once __DIR__ . '/../controllers/home_controller.php';
});

// Protected views
path('/cases', function($app) {
    require_protected_access($app, function($app) {
        ($app->render)('standard', 'entities/manage_entities/manage_cases');
    });
});

path('/defendants', function($app) {
    require_protected_access($app, function($app) {
        ($app->render)('standard', 'entities/manage_entities/manage_defendants');
    });
});

path('/lawyers', function($app) {
    require_protected_access($app, function($app) {
        ($app->render)('standard', 'entities/manage_entities/manage_lawyers');
    });
});

path('/logs', function($app) {
    require_protected_access($app, function($app) {
        require_once __DIR__ . '/../controllers/logs_controller.php';
    });
});

// Protected controllers
foreach (['defendant', 'charge', 'lawyer', 'event', 'case'] as $entity) {
    path("/$entity/{action}", function($app, $action) use ($entity) {
        require_protected_access($app, function($app) use ($entity, $action) {
            require_once __DIR__ . "/../controllers/{$entity}_controller.php";
        });
    });

    path("/$entity/{action}/{id}", function($app, $action, $id) use ($entity) {
        require_protected_access($app, function($app) use ($entity, $action, $id) {
            require_once __DIR__ . "/../controllers/{$entity}_controller.php";
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

// Manage accounts (protected)
path('/accounts/manage', function($app) {
    require_protected_access($app, function($app) {
        require_once __DIR__ . '/../controllers/auth_controller.php';
        manage_accounts($app);
    });
});

path('/accounts/delete/{userID}', function($app, $userID) {
    require_protected_access($app, function($app) use ($userID) {
        require_once __DIR__ . '/../controllers/auth_controller.php';
        delete_user($userID);
    });
});

path('/accounts/edit/{userID}', function($app, $userID) {
    require_protected_access($app, function($app) use ($userID) {
        require_once __DIR__ . '/../controllers/auth_controller.php';
        edit_user_page($app, $userID);
    });
});

path('/accounts/edit/{userID}/submit', function($app, $userID) {
    require_protected_access($app, function($app) use ($userID) {
        require_once __DIR__ . '/../controllers/auth_controller.php';
        update_user($userID);
    });
});

resolve();
