<?php

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/User.php'; 

class LogModel
{
    public static function log_action($userID, $message)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Get username
            $user = User::findById($db, $userID);
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
}
