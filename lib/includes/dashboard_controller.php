<?php
require_once __DIR__ . '/../models/CourtEvent.php';

function getUpcomingCourtEvents() {
    return CourtEvent::getUpcomingEvents();
}

// Example announcements (replace with model later)
$announcements = [
    "Welcome to the Court Tracking Dashboard!",
    "Don't forget to review your assigned cases."
];

$events = getUpcomingCourtEvents();

($app->render)('standard', 'dashboard/dashboard_view', [
    'events' => $events,
    'announcements' => $announcements
]);
