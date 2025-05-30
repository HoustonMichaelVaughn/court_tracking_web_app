<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/CaseRecord.php';
require_once __DIR__ . '/../models/CourtEvent.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Logs.php';

if (!Auth::isAuthenticated()) {
    header("Location: " . BASE_URL . "/login");
    exit;
}

$announcements = [
    "Welcome to the Court Tracking Dashboard!",
    "Don't forget to review your assigned cases."
];

$events = getUpcomingCourtEvents();

$db = Database::getInstance()->getConnection();
$stats = CaseRecord::getStatistics($db);

$logs = LogModel::getRecentLogs($db);

($app->render)(
    'standard', 
    'home', 
    [
        'stats' => $stats,
        'logs' => $logs
    ]
);

function getHomePageData()
{
    $db = Database::getInstance()->getConnection();
    $stats = CaseRecord::getStatistics($db);
    $logs = getRecentLogs($db);
    return ['stats' => $stats, 'logs' => $logs];
}

function getUpcomingCourtEvents() {
    return CourtEvent::getUpcomingEvents();
}
