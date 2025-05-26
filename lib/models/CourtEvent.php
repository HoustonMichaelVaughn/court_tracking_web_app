<?php
require_once __DIR__ . '/../includes/Database.php';

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
    }

    public static function getEventsByCaseID($caseID)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT * FROM court_event WHERE case_ID = :caseID");
        $stmt->execute([':caseID' => $caseID]);

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
    
        $stmt = $db->prepare("DELETE FROM court_event WHERE Event_ID = :eventID");
        $stmt->execute([':eventID' => $eventID]);
    }
    
    public static function update($eventID, $data) {
        $db = Database::getInstance()->getConnection();
    
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
    }    

}