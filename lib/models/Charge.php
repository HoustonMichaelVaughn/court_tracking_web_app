<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Logs.php';

class Charge
{
    public static function create($case_ID, $data)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("INSERT INTO charge (case_ID, Description, Status) VALUES (:case_ID, :description, :status)");
        $stmt->execute([
            ':case_ID'     => $case_ID,
            ':description' => $data['description'] ?? '',
            ':status'      => $data['status'] ?? ''
        ]);

        // Logging
        $userId = $_SESSION['user_id'] ?? null;
        $logMessage = sprintf(
            "User ID %s added charge to case %s: Description='%s', Status='%s'",
            $userId ?? 'Unknown',
            $case_ID,
            $data['description'] ?? '',
            $data['status'] ?? ''
        );
        LogModel::log_action($userId, $logMessage);
    }

    public static function getChargesByCaseID($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM charge WHERE case_ID = :caseID");
        $stmt->execute([':caseID' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getChargeByChargeID($chargeID)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM charge WHERE charge_ID = :chargeID");
        $stmt->execute([':chargeID' => $chargeID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($chargeID) {
        $db = Database::getInstance()->getConnection();

        // Retrieve charge details for logging
        $charge = self::getChargeByChargeID($chargeID);

        $stmt = $db->prepare("DELETE FROM charge WHERE charge_ID = :charge_ID");
        $stmt->execute([':charge_ID' => $chargeID]);

        // Logging
        $userId = $_SESSION['user_id'] ?? null;
        $logMessage = sprintf(
            "User ID %s deleted charge ID %s: Description='%s', Status='%s'",
            $userId ?? 'Unknown',
            $chargeID,
            $charge['Description'] ?? 'Unknown',
            $charge['Status'] ?? 'Unknown'
        );
        LogModel::log_action($userId, $logMessage);
    }

    public static function update($chargeID, $data) {
        $db = Database::getInstance()->getConnection();

        $old = self::getChargeByChargeID($chargeID);

        $stmt = $db->prepare("UPDATE charge SET Description = :description, Status = :status WHERE charge_ID = :charge_ID");
        $stmt->execute([
            ':description' => $data['description'],
            ':status'      => $data['status'],
            ':charge_ID'   => $chargeID
        ]);

        // Logging
        $userId = $_SESSION['user_id'] ?? null;
        $logMessage = sprintf(
            "User ID %s updated charge ID %s: Description='%s' → '%s', Status='%s' → '%s'",
            $userId ?? 'Unknown',
            $chargeID,
            $old['Description'] ?? '',
            $data['description'],
            $old['Status'] ?? '',
            $data['status']
        );
        LogModel::log_action($userId, $logMessage);
    }
}
