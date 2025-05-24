<?php
session_start();

require_once __DIR__ . '/../models/CourtEvent.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/logs.php';

// internal routing within controller for CRUD operations
switch ($action) {
    case 'add':
        save_event($app);
        break;
    case 'edit':
        save_event($app, $eventID);
        break;
    case 'delete':
        delete_event($app, $eventID);
        break;
    default:
        ($app->render)('standard', '404');
        exit;
}

// combined function for adding and editing for DRY
function save_event($app, $eventID = null) {
    try {
        $caseID = $_GET['caseID'] ?? null;
        if (!$caseID) {
            throw new Exception("CaseID required.");
        }
    
        $isEdit = isset($eventID);
        $event = $isEdit ? CourtEvent::getEventByEventID($eventID) : null;
    
        if ($isEdit && !$event) { // only relevant for edit operations
            throw new Exception("Event not found.");
        }
    
        // get user data if POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $location = $_POST['location'] ?? '';
            $description = $_POST['description'] ?? '';
            $date = $_POST['date'] ?? '';
            
            // server-side checking, client-side already implemented
            if (empty($location) || empty($description) || empty($date)) {
                throw new Exception("All fields must be filled out.");
            }
    
            $data = [
                'location' => $location,
                'description' => $description,
                'date' => $date
            ];
            
            // database operations
            if ($isEdit) {
                $oldData = $event;
                
                CourtEvent::update($eventID, $data);

                $changes = [];
                foreach (['location', 'description', 'date'] as $field) {
                    $oldValue = $oldData[ucfirst($field)] ?? '';
                    $newValue = $data[$field] ?? '';
                    if ($oldValue != $newValue) {
                        $changes[] = ucfirst($field) . " changed from '$oldValue' to '$newValue'";
                    }
                }

                $changeSummary = implode("; ", $changes);
                if (empty($changeSummary)) {
                    $changeSummary = "No changes were made.";
                }

                LogModel::log_action($_SESSION['user_id'], "Updated event ID $eventID for case ID $caseID. $changeSummary");
                $successMessage = "Event updated successfully.";
            } else {
                CourtEvent::create($caseID, $data);

                $details = "Location: '{$data['location']}'; Description: '{$data['description']}'; Date: '{$data['date']}'";
                LogModel::log_action($_SESSION['user_id'], "Created new event for case ID $caseID. $details");

                $successMessage = "Event added successfully.";
            }
    
            redirect_with_success("/case/edit/" . $caseID, $successMessage);
        }
        
        // for GET request, display standard form
        ($app->render)('standard', 'forms/event_form', [
            'caseID' => $caseID,
            'event' => $event,
            'isEdit' => $isEdit,
        ]);      

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}


function delete_event($app, $eventID) {
    try {
        $caseID = $_GET['caseID'] ?? null;
        if (!$caseID) {
            throw new Exception("CaseID required.");
        }

        $event = CourtEvent::getEventByEventID($eventID);
        if (!$event) {
            throw new Exception("Event not found.");
        }
        // database operation
        CourtEvent::delete($eventID);

        $details = "Deleted event ID $eventID from case ID $caseID. ";
        $details .= "Details - Location: '{$event['Location']}', Description: '{$event['Description']}', Date: '{$event['Date']}'.";

        LogModel::log_action($_SESSION['user_id'], $details);

        redirect_with_success("/case/edit/" . $caseID, "Event deleted successfully.");
        
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

