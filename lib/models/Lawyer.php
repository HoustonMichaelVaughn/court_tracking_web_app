<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../models/Auth.php';

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

        $lawyerID = $db->lastInsertId();

        // Logging
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
            "User %s (ID: %s) added new lawyer: Name='%s', \nEmail='%s', \nPhone='%s', \nFirm='%s' (Lawyer ID: %s)",
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
    {
        $stmt = $this->db->query("SELECT lawyer_ID, Name FROM lawyer ORDER BY Name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllLawyersWithDetails()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT lawyer_ID, Name AS lawyer_name
            FROM Lawyer
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLawyerByLawyerID($lawyerID)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM Lawyer WHERE lawyer_ID = :lawyerID");
        $stmt->execute([':lawyerID' => $lawyerID]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($lawyerID, $data)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM lawyer WHERE lawyer_ID = :lawyer_ID");
        $stmt->execute([':lawyer_ID' => $lawyerID]);
        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

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

        // Logging
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
        $logMessage = "User {$username} (ID: {$userId}) updated lawyer (ID: {$lawyerID}). \n{$changeSummary}";

        LogModel::log_action($userId, $logMessage);
    }

    public static function delete($lawyerID)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT Name, Email, Phone_Number, Firm FROM lawyer WHERE lawyer_ID = :lawyer_ID");
        $stmt->execute([':lawyer_ID' => $lawyerID]);
        $lawyer = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("DELETE FROM lawyer WHERE lawyer_ID = :lawyer_ID");
        $stmt->execute([':lawyer_ID' => $lawyerID]);

        // Logging
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
            "User %s (ID: %s) deleted lawyer: Name='%s', \nEmail='%s', \nPhone='%s', \nFirm='%s' (Lawyer ID: %s)",
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
