<?php

require_once __DIR__ . '/../controllers/Database.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Defendant.php';

class CaseRecord
{
    public static function create($defendant_id)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("INSERT INTO caserecord (defendant_ID) VALUES (:defendant_id)");
        $stmt->execute([':defendant_id' => $defendant_id]);

        $caseID = $db->lastInsertId();

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $user = Auth::getCurrentUser();
            $username = $user['username'] ?? $username;
        }

        $defendant = Defendant::getDefendantByDefendantID($defendant_id);
        $defName = $defendant['Name'] ?? 'Unknown';

        $logMessage = sprintf(
            "%s (UserID: %s) created case #%s for defendant: %s",
            $username,
            $userId ?? 'N/A',
            $caseID,
            $defName
        );

        LogModel::log_action($userId, $logMessage);

        return $caseID;
    }

    public static function deleteCaseByID($id)
    {
        $db = Database::getInstance()->getConnection();

        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("Invalid Case ID.");
        }

        $stmt = $db->prepare("
            SELECT cr.case_ID, d.name AS defendant_name, l.name AS lawyer_name, ch.description AS charge_type, ce.description AS event_description, ce.date AS event_date
            FROM caserecord cr
            LEFT JOIN defendant d ON cr.defendant_ID = d.defendant_ID
            LEFT JOIN case_lawyer cl ON cr.case_ID = cl.case_ID
            LEFT JOIN lawyer l ON cl.lawyer_ID = l.lawyer_ID
            LEFT JOIN charge ch ON cr.case_ID = ch.case_ID
            LEFT JOIN court_event ce ON cr.case_ID = ce.case_ID
            WHERE cr.case_ID = :caseID
        ");
        $stmt->execute([':caseID' => $id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $defendant = $details[0]['defendant_name'] ?? 'Unknown';
        $charges = array_unique(array_column($details, 'charge_type'));
        $lawyers = array_unique(array_column($details, 'lawyer_name'));

        $events = [];
        foreach ($details as $row) {
            if (!empty($row['event_description']) && !empty($row['event_date'])) {
                $events[] = $row['event_description'] . " on " . $row['event_date'];
            }
        }

        $stmt = $db->prepare("DELETE FROM caserecord WHERE case_ID = :caseID");
        $stmt->execute([':caseID' => $id]);

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $user = Auth::getCurrentUser();
            $username = $user['username'] ?? $username;
        }

        $logMessage = sprintf(
            "%s (UserID: %s) deleted case #%s.\nDefendant: %s\nCharges: %s\nLawyers: %s\nEvents: %s",
            $username,
            $userId ?? 'N/A',
            $id,
            $defendant,
            implode(', ', array_filter($charges)),
            implode(', ', array_filter($lawyers)),
            implode('; ', array_filter($events))
        );

        LogModel::log_action($userId, $logMessage);
    }

    public static function getAllCasesWithDetails($caseID = null)
    {
        $db = Database::getInstance()->getConnection();

        if ($caseID === null) {
            $stmt = $db->prepare("
                SELECT cr.case_ID, d.name AS defendant_name, l.name AS lawyer_name
                FROM caserecord cr
                LEFT JOIN defendant d ON cr.defendant_ID = d.defendant_ID
                LEFT JOIN case_lawyer cl ON cr.case_ID = cl.case_ID
                LEFT JOIN lawyer l ON cl.lawyer_ID = l.lawyer_ID
                ORDER BY cr.case_ID DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $db->prepare("
                SELECT cr.case_ID, d.name AS defendant_name, l.name AS lawyer_name,
                       ch.description AS charge_type, ce.description AS event_description, ce.date AS event_date
                FROM caserecord cr
                LEFT JOIN defendant d ON cr.defendant_ID = d.defendant_ID
                LEFT JOIN case_lawyer cl ON cr.case_ID = cl.case_ID
                LEFT JOIN lawyer l ON cl.lawyer_ID = l.lawyer_ID
                LEFT JOIN charge ch ON cr.case_ID = ch.case_ID
                LEFT JOIN court_event ce ON cr.case_ID = ce.case_ID
                WHERE cr.case_ID = :caseID
                ORDER BY cr.case_ID DESC
            ");
            $stmt->execute([':caseID' => $caseID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function linkLawyer($id, $lawyerID)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO case_lawyer (case_ID, lawyer_ID) VALUES (?, ?)");
        $stmt->execute([$id, $lawyerID]);
    }

    public static function getStatistics() {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM caserecord");
        $stmt->execute();
        $total = $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(DISTINCT case_ID) FROM charge WHERE LOWER(status) = 'pending'");
        $stmt->execute();
        $pending = $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(DISTINCT case_ID) FROM charge WHERE LOWER(status) = 'resolved'");
        $stmt->execute();
        $resolved = $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(DISTINCT case_ID) FROM charge WHERE LOWER(status) = 'dismissed'");
        $stmt->execute();
        $dismissed = $stmt->fetchColumn();

        return [
            'total' => $total,
            'pending' => $pending,
            'resolved' => $resolved,
            'dismissed' => $dismissed
        ];
    }
}
