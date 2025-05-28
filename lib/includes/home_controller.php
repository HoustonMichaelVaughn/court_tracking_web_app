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

// Example announcements (replace with model later)
$announcements = [
    "Welcome to the Court Tracking Dashboard!",
    "Don't forget to review your assigned cases."
];

$events = getUpcomingCourtEvents();

// ($app->render)('standard', 'dashboard/dashboard_view', [
//     'events' => $events,
//     'announcements' => $announcements
// ]);

$db = Database::getInstance()->getConnection();
$stats = CaseRecord::getStatistics($db);

$logs = getRecentLogs($db);

($app->render)(
    'standard', 
    'home', 
    [
        'stats' => $stats,
        'logs' => $logs
    ]
);


function getRecentLogs($db, $limit = 3)
{
    $stmt = $db->prepare("
        SELECT logs.action, logs.created_at, users.username
        FROM logs
        JOIN users ON logs.user_id = users.id
        ORDER BY logs.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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