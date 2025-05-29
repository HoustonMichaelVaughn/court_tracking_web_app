<?php

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/Auth.php';

class LogModel
{
    public static function log_action($userID, $message)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Get username
            $user = Auth::getCurrentUser();
            $username = $user ? $user['username'] : "User unknown (ID: $userID)";

            // Final message
            $message = "$username - $message";

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
        } catch (PDOException $e) {
            error_log("Log error: " . $e->getMessage()); // You can also write this to a file
        }
    }

    public static function getPaginatedLogs($limit, $offset)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT u.username, l.action, l.created_at
            FROM logs l
            LEFT JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countLogs()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM logs");
        return $stmt->fetchColumn();
    }
}
