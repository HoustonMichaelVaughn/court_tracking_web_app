<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Logs.php';

class Lawyer
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            INSERT INTO lawyer (Name, Email, Phone_Number, Firm)
            VALUES (:name, :email, :phone, :firm)
        ");

        $stmt->execute([
            ':name'  => $data['name'],
            ':email' => $data['email'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':firm'  => $data['firm'] ?? ''
        ]);

        // Log the action
        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        $logMessage = sprintf(
            "User %s (ID: %s) added new lawyer: Name='%s', \n Email='%s', \n Phone='%s', \n Firm='%s' (Lawyer ID: %s)",
            $username,
            $userId ?? 'N/A',
            $data['name'],
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['firm'] ?? '',
            $lawyerID
        );

        LogModel::log_action($userId, $logMessage);

        return $lawyerID;
    }



    public function all(): array
    // returns all entries from database for dynamic drop down menus
    {
        $stmt = $this->db->query("SELECT lawyer_ID, Name FROM lawyer ORDER BY Name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }  

    public static function getAllLawyersWithDetails()
    // returns lawyer_ID and lawyer_name for all_lawyers page
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT lawyer_ID, Name AS lawyer_name
            FROM Lawyer
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLawyerByLawyerID($lawyerID)
    // returns lawyer entity for edit functionality
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM Lawyer WHERE lawyer_ID = :lawyerID");
        $stmt->execute([':lawyerID' => $lawyerID]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($lawyerID, $data) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM lawyer WHERE lawyer_ID = :lawyer_ID");
        $stmt->execute([':lawyer_ID' => $lawyerID]);
        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update lawyer record
        $stmt = $db->prepare("
            UPDATE lawyer
            SET
                Name = :Name,
                Email = :Email,
                Phone_Number = :Phone_Number,
                Firm = :Firm
            WHERE lawyer_ID = :lawyer_ID
        ");

        $stmt->execute([
            ':Name' => $data['name'],
            ':Email' => $data['email'] ?? '',
            ':Phone_Number' => $data['phone'] ?? '',
            ':Firm' => $data['firm'] ?? '',
            ':lawyer_ID' => $lawyerID,
        ]);

        // Get user info for logging
        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        // Fields to compare: db field => [data key, readable label]
        $fields = [
            'Name' => ['name', 'Name'],
            'Email' => ['email', 'Email'],
            'Phone_Number' => ['phone', 'Phone Number'],
            'Firm' => ['firm', 'Firm'],
        ];

        $changes = [];

        foreach ($fields as $dbField => [$dataKey, $label]) {
            $oldValue = $oldData[$dbField] ?? '';
            $newValue = $data[$dataKey] ?? '';

            if ($oldValue != $newValue) {
                $changes[] = "{$label} changed from '{$oldValue}' to '{$newValue}'";
            }
        }

        $changeSummary = !empty($changes) ? implode("; ", $changes) : "No changes were made.";

        $logMessage = "User {$username} (ID: {$userId}) updated lawyer (ID: {$lawyerID}). {$changeSummary}";

        LogModel::log_action($userId, $logMessage);
    }


    public static function delete($lawyerID) {
        $db = Database::getInstance()->getConnection();

        // Fetch lawyer info before deletion (for logging)
        $stmt = $db->prepare("SELECT Name, Email, Phone_Number, Firm FROM lawyer WHERE lawyer_ID = :lawyer_ID");
        $stmt->execute([':lawyer_ID' => $lawyerID]);
        $lawyer = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the lawyer record
        $stmt = $db->prepare("DELETE FROM lawyer WHERE lawyer_ID = :lawyer_ID");
        $stmt->execute([':lawyer_ID' => $lawyerID]);

        // Log the deletion
        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId !== null) {
            $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($user && isset($user['username'])) {
                $username = $user['username'];
            }
        }

        $logMessage = sprintf(
            "User %s (ID: %s) deleted lawyer: Name='%s', \n Email='%s', \n Phone='%s', \n Firm='%s' (Lawyer ID: %s)",
            $username,
            $userId ?? 'N/A',
            $lawyer['Name'] ?? 'N/A',
            $lawyer['Email'] ?? 'N/A',
            $lawyer['Phone_Number'] ?? 'N/A',
            $lawyer['Firm'] ?? 'N/A',
            $lawyerID
        );

        LogModel::log_action($userId, $logMessage);
    }

}