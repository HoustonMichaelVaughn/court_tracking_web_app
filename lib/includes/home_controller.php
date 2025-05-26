<?php
// controllers/home_controller.php

require_once __DIR__ . '/../models/CaseRecord.php';

function getRecentLogs($db, $limit = 3) {
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

function getHomePageData() {
    $db = Database::getInstance()->getConnection();
    $stats = CaseRecord::getStatistics($db);
    $logs = getRecentLogs($db);
    return ['stats' => $stats, 'logs' => $logs];
}
