<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Logs.php';

class CourtEvent
{
    public static function create($case_id, $data)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            INSERT INTO court_event (case_ID, Location, Description, Date)
            VALUES (:case_id, :location, :description, :date)
        ");

        $stmt->execute([
            ':case_id'     => $case_id,
            ':location'    => $data['location'] ?? '',
            ':description' => $data['description'] ?? '',
            ':date'        => $data['date'] ?? null
        ]);

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId) {
            $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        $logMessage = sprintf(
            "User %s (ID: %s) added new court event for case ID %s.\nLocation: %s\nDescription: %s\nDate: %s",
            $username,
            $userId ?? 'N/A',
            $case_id,
            $data['location'] ?? '',
            $data['description'] ?? '',
            $data['date'] ?? ''
        );

        LogModel::log_action($userId, $logMessage);
    }

    public static function getEventsByCaseID($id)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM court_event WHERE case_ID = :caseID");
        $stmt->execute([':caseID' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEventByEventID($eventID)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM court_event WHERE Event_ID = :eventID");
        $stmt->execute([':eventID' => $eventID]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getUpcomingEvents()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT * FROM court_event
            WHERE Date >= CURDATE()
            ORDER BY Date ASC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($eventID) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM court_event WHERE Event_ID = :eventID");
        $stmt->execute([':eventID' => $eventID]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("DELETE FROM court_event WHERE Event_ID = :eventID");
        $stmt->execute([':eventID' => $eventID]);

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId) {
            $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        $logMessage = sprintf(
            "User %s (ID: %s) deleted court event (ID: %s).\nLocation: %s\nDescription: %s\nDate: %s",
            $username,
            $userId ?? 'N/A',
            $eventID,
            $event['Location'] ?? 'N/A',
            $event['Description'] ?? 'N/A',
            $event['Date'] ?? 'N/A'
        );

        LogModel::log_action($userId, $logMessage);
    }

    public static function update($eventID, $data) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM court_event WHERE Event_ID = :eventID");
        $stmt->execute([':eventID' => $eventID]);
        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("
            UPDATE court_event
            SET Location = :location, Description = :description, Date = :date
            WHERE Event_ID = :eventID
        ");

        $stmt->execute([
            ':location' => $data['location'],
            ':description' => $data['description'],
            ':date'      => $data['date'],
            ':eventID'   => $eventID
        ]);

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId) {
            $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        $changes = [];
        foreach (['Location' => 'location', 'Description' => 'description', 'Date' => 'date'] as $dbField => $key) {
            $old = $oldData[$dbField] ?? '';
            $new = $data[$key] ?? '';
            if ($old !== $new) {
                $changes[] = "$dbField changed from '$old' to '$new'";
            }
        }

        $summary = !empty($changes) ? implode("; ", $changes) : "No changes detected.";

        $logMessage = sprintf(
            "User %s (ID: %s) updated court event (ID: %s).\n%s",
            $username,
            $userId ?? 'N/A',
            $eventID,
            $summary
        );

        LogModel::log_action($userId, $logMessage);
    }
}
