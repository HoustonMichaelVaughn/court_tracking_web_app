<?php
require_once __DIR__ . '/../controllers/Database.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../models/Auth.php';

class Defendant
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function create($data) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            INSERT INTO defendant (Name, Date_of_Birth, Address, Ethnicity, Phone_Number, Email)
            VALUES (:name, :dob, :address, :ethnicity, :phone, :email)
        ");

        $stmt->execute([
            ':name'      => $data['name'],
            ':dob'       => $data['dob'],
            ':address'   => $data['address'] ?? '',
            ':ethnicity' => $data['ethnicity'] ?? '',
            ':phone'     => $data['phone'] ?? '',
            ':email'     => $data['email'] ?? ''
        ]);

        $defendantID = $db->lastInsertId();

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $user = Auth::getCurrentUser();
            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        $logMessage = sprintf(
            "User %s (ID: %s) added new defendant: Name='%s', DOB='%s', Ethnicity='%s', Phone='%s', Address='%s', Email='%s' (Defendant ID: %s)",
            $username,
            $userId ?? 'N/A',
            $data['name'],
            $data['dob'],
            $data['ethnicity'] ?? '',
            $data['phone'] ?? '',
            $data['address'] ?? '',
            $data['email'] ?? '',
            $defendantID
        );

        LogModel::log_action($userId, $logMessage);

        return $defendantID;
    }

    public function all(): array {
        $stmt = $this->db->query("SELECT defendant_ID, Name FROM defendant ORDER BY Name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search_fielded(string $field, string $term): array {
        $base_sql = "
            SELECT DISTINCT
                d.defendant_ID,
                d.Name AS Defendant_Name,
                d.Email AS Defendant_Email,
                cr.case_ID,
                ch.Description AS Charge_Description,
                ch.Status AS Charge_Status,
                l.Name AS Lawyer_Name,
                ce.Description AS Event_Description,
                ce.Location AS Event_Location,
                ce.Date AS Event_Date
            FROM defendant d
            LEFT JOIN caserecord cr ON cr.defendant_ID = d.defendant_ID
            LEFT JOIN charge ch ON ch.case_ID = cr.case_ID
            LEFT JOIN case_lawyer cl ON cl.case_ID = cr.case_ID
            LEFT JOIN lawyer l ON l.lawyer_ID = cl.lawyer_ID
            LEFT JOIN court_event ce ON ce.case_ID = cr.case_ID
        ";

        $field_map = [
            'name' => 'd.Name',
            'email' => 'd.Email',
            'charge' => 'ch.Description',
            'status' => 'ch.Status',
            'lawyer' => 'l.Name',
            'event' => 'ce.Description',
        ];
        if (!isset($field_map[$field])) return [];
        $sql = $base_sql . " WHERE " . $field_map[$field] . " LIKE :term";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':term' => '%' . $term . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllDefendantsWithDetails() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT defendant_ID, Name AS defendant_name, Date_Of_Birth AS defendant_DOB FROM Defendant");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDefendantByDefendantID($defendantID) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Defendant WHERE defendant_ID = :defendantID");
        $stmt->execute([':defendantID' => $defendantID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($defendantID, $data) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM defendant WHERE defendant_ID = :defendant_ID");
        $stmt->execute([':defendant_ID' => $defendantID]);
        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("
            UPDATE defendant SET
                Name = :Name,
                Date_of_Birth = :Date_of_Birth,
                Address = :Address,
                Ethnicity = :Ethnicity,
                Phone_Number = :Phone_Number,
                Email = :Email
            WHERE defendant_ID = :defendant_ID
        ");

        $stmt->execute([
            ':Name' => $data['name'],
            ':Date_of_Birth' => $data['dob'],
            ':Address' => $data['address'] ?? '',
            ':Ethnicity' => $data['ethnicity'] ?? '',
            ':Phone_Number' => $data['phone'] ?? '',
            ':Email' => $data['email'] ?? '',
            ':defendant_ID' => $defendantID,
        ]);

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $user = Auth::getCurrentUser();
            $username = $user['username'] ?? $username;
        }

        $fields = [
            'Name' => ['key' => 'name', 'label' => 'Name'],
            'Date_of_Birth' => ['key' => 'dob', 'label' => 'Date of Birth'],
            'Address' => ['key' => 'address', 'label' => 'Address'],
            'Ethnicity' => ['key' => 'ethnicity', 'label' => 'Ethnicity'],
            'Phone_Number' => ['key' => 'phone', 'label' => 'Phone Number'],
            'Email' => ['key' => 'email', 'label' => 'Email'],
        ];

        $changes = [];

        foreach ($fields as $dbField => $info) {
            $oldValue = $oldData[$dbField] ?? '';
            $newValue = $data[$info['key']] ?? '';
            if ($oldValue != $newValue) {
                $changes[] = "{$info['label']} changed from '{$oldValue}' to '{$newValue}'";
            }
        }

        $changeSummary = !empty($changes) ? implode("; ", $changes) : "No changes were made.";

        $logMessage = "User {$username} (ID: {$userId}) updated defendant (ID: {$defendantID}). \n{$changeSummary}";
        LogModel::log_action($userId, $logMessage);
    }

    public static function delete($defendantID) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT Name, Date_of_Birth, Ethnicity, Phone_Number, Address, Email FROM defendant WHERE defendant_ID = :defendant_ID");
        $stmt->execute([':defendant_ID' => $defendantID]);
        $defendant = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("DELETE FROM defendant WHERE defendant_ID = :defendant_ID");
        $stmt->execute([':defendant_ID' => $defendantID]);

        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $user = Auth::getCurrentUser();
            $username = $user['username'] ?? $username;
        }

        $logMessage = sprintf(
            "User %s (ID: %s) deleted defendant: Name='%s', DOB='%s', Ethnicity='%s', Phone='%s', Address='%s', Email='%s' (Defendant ID: %s)",
            $username,
            $userId ?? 'N/A',
            $defendant['Name'] ?? 'N/A',
            $defendant['Date_of_Birth'] ?? 'N/A',
            $defendant['Ethnicity'] ?? 'N/A',
            $defendant['Phone_Number'] ?? 'N/A',
            $defendant['Address'] ?? 'N/A',
            $defendant['Email'] ?? 'N/A',
            $defendantID
        );

        LogModel::log_action($userId, $logMessage);
    }
}
