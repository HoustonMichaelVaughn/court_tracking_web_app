<?php

require_once __DIR__ . '/../includes/Database.php';

class LogModel
{
    public static function log_action($userID, $message)
    {
        $db = Database::getInstance()->getConnection();
        $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

        $stmt = $db->prepare("
            INSERT INTO logs (user_id, action, created_at)
            VALUES (:user_id, :action, NOW())
        ");
        
        $stmt = $db->prepare("
    INSERT INTO logs (user_id, action, created_at)
    VALUES (:user_id, :action, NOW())
");

if ($userID === null) {
    $stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
} else {
    $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT);
}
$stmt->bindValue(':action', $message, PDO::PARAM_STR);

$stmt->execute();
    }
}
