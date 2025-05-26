<?php
require_once __DIR__ . '/../includes/Database.php';

class Charge
{
    public static function create($case_ID, $data)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            INSERT INTO charge (case_ID, Description, Status)
            VALUES (:case_ID, :description, :status)
        ");

        $stmt->execute([
            ':case_ID'     => $case_ID,
            ':description' => $data['description'] ?? '',
            ':status'      => $data['status'] ?? ''
        ]);
    }

    public static function getChargesByCaseID($caseID)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM charge WHERE case_ID = :caseID");
        $stmt->execute([':caseID' => $caseID]);

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
    
        $stmt = $db->prepare("DELETE FROM charge WHERE charge_ID = :charge_ID");
        $stmt->execute([':charge_ID' => $chargeID]);
    }
    
    public static function update($chargeID, $data) {
        $db = Database::getInstance()->getConnection();
    
        $stmt = $db->prepare("
            UPDATE charge
            SET Description = :description, Status = :status
            WHERE charge_ID = :charge_ID
        ");
    
        $stmt->execute([
            ':description' => $data['description'],
            ':status'      => $data['status'],
            ':charge_ID'   => $chargeID
        ]);
    }    
    
    
}

