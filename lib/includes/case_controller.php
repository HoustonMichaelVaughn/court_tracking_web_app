<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Auth.php';

if (!Auth::isAuthenticated()) {
    header("Location: " . BASE_URL . "/login");
    exit;
}

// all models required to add or edit a case
require_once __DIR__ . '/../models/Defendant.php';
require_once __DIR__ . '/../models/Lawyer.php';
require_once __DIR__ . '/../models/CaseRecord.php';
require_once __DIR__ . '/../models/Charge.php';
require_once __DIR__ . '/../models/CourtEvent.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Logs.php';

switch ($action) {
    // internal routing within the controller
    case 'defendant':
        handle_defendant_step($app);
        break;
    case 'charges':
        handle_charge_step($app);
        break;
    case 'lawyer':
        handle_lawyer_step($app);
        break;
    case 'events':
        handle_event_step($app);
        break;
    case 'confirm':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_confirm_step($app); // user has confirmed changes
        } else {
            show_case_review($app); // to show the user changes for confirmation
        }
        break;
    case 'success':
        ($app->render)('standard', 'case_wizard/case_confirm');
        break;
    case 'manage':
        show_manage_cases($app);
        break;
    case 'edit':
        edit_case($app, $id);
        break;
    case 'cancel':
        cancel_case_wizard();
        break;
    case 'delete':
        delete_case($app, $id);
        break;
    default:
        ($app->render)('standard', '404');
        exit;
}

function handle_defendant_step($app) {
    try {
        // Get action from POST
        $action = $_POST['action'] ?? null;
        $defendantID = null; // Initialize the defendant ID variable

        // Handle 'add_new' action
        if ($action === 'add_new') {
            if (empty($_POST['name']) || empty($_POST['dob'])) {
                throw new Exception("Defendant's name and DOB are required.");
            }
            // Create new defendant and get the defendant ID
            $defendantID = Defendant::create($_POST);
            $_SESSION['case']['defendant_ID'] = $defendantID; // Store in session

            redirect_with_success("/case/defendant", "Defendant added successfully.");
        }

        // Handle 'select_existing' action
        if ($action === 'select_existing' && !empty($_POST['defendant_ID'])) {
            $defendantID = $_POST['defendant_ID'];
            $_SESSION['case']['defendant_ID'] = $defendantID; // Store in session

            // Redirect to charges step
            header("Location: " . BASE_URL . "/case/charges");
            exit;
        }

        // If action is not set or not recognized, render form with available defendants
        $defendants = (new Defendant())->all();
        ($app->render)('standard', 'case_wizard/defendant_form', [
            'defendants' => $defendants,
        ]);
        return;

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function handle_charge_step($app) {
    try {
        // Initialize charges session array if it doesn't exist
        if (!isset($_SESSION['case']['charges'])) {
            $_SESSION['case']['charges'] = [];
        }

        // If it's a GET request, just render the charge form
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Render charge form with current charges
            $charges = $_SESSION['case']['charges'];
            ($app->render)('standard', 'case_wizard/charge_form', [
                'charges' => $charges
            ]);
            return;
        }

        // If it's a POST request, handle the charge form submission
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? '';

        // Handle new charge input
        if ($description !== '') {
            $_SESSION['case']['charges'][] = [
                'description' => $description,
                'status' => $status
            ];
        }

        // If the user hasn't added any charge and tries to proceed, throw an exception
        if (!isset($_POST['add_more']) && count($_SESSION['case']['charges']) === 0) {
            throw new Exception("You must add at least one charge before continuing.");
        }

        // Redirect based on button clicked (add more or proceed)
        if (isset($_POST['add_more'])) {
            header("Location: " . BASE_URL . "/case/charges");
        } else {
            header("Location: " . BASE_URL . "/case/lawyer");
        }
        exit;

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function handle_lawyer_step($app) {
    try {
        // Get action from POST
        $action = $_POST['action'] ?? null;
        $lawyerID = null; // Initialize the lawyer ID variable

        // Handle 'add_new' action
        if ($action === 'add_new') {
            if (empty($_POST['name'])) { //server-side checking. client-side already performed.
                throw new Exception("Lawyer's name is required");
            }
            // Create new defendant and get the defendant ID
            $lawyerID = Lawyer::create($_POST);
            $_SESSION['case']['lawyer_ID'] = $lawyerID; // Store in session
            
            redirect_with_success("/case/lawyer", "Lawyer added successfully.");
        }

        // Handle 'select_existing' action
        if ($action === 'select_existing' && !empty($_POST['lawyer_ID'])) {
            $lawyerID = $_POST['lawyer_ID'];
            $_SESSION['case']['lawyer_ID'] = $lawyerID; // Store in session

            // Redirect to events step
            header("Location: " . BASE_URL . "/case/events");
            exit;
        }

        // If action is not set or not recognized, render form with available defendants
        $lawyers = (new Lawyer())->all();
        ($app->render)('standard', 'case_wizard/lawyer_form', [
            'lawyers' => $lawyers
        ]);
        return;

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function handle_event_step($app) {
    try {
        // Initialize events session array if it doesn't exist
        if (!isset($_SESSION['case']['events'])) {
            $_SESSION['case']['events'] = [];
        }

        // If it's a GET request, just render the events form
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Render events form with current events
            $events = $_SESSION['case']['events'];
            ($app->render)('standard', 'case_wizard/event_form', [
                'events' => $events
            ]);
            return;
        }

        // If it's a POST request, handle the event form submission
        $description = trim($_POST['description'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $location = trim($_POST['location'] ?? '');

        // Validate input
        if ($description !== '' && $date !== '' && $location !== '') {
            $_SESSION['case']['events'][] = [
                'description' => $description,
                'date' => $date,
                'location' => $location
            ];
        } elseif ($description !== '' || $date !== '' || $location !== '') {
            // Handle incomplete event data
            throw new Exception("To add an event, you must complete description, date, and location.");
        }

        // Redirect based on button clicked
        if (isset($_POST['add_more'])) {
            header("Location: " . BASE_URL . "/case/events");
            exit;
        } else {
            // Proceed to confirmation step if no "add more"
            header("Location: " . BASE_URL . "/case/confirm");
            exit;
        }

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function show_case_review($app) {
    try {
        $case = $_SESSION['case'] ?? [];
        $event = $_SESSION['event'] ?? [];
    
        $db = Database::getInstance()->getConnection();
    
        // Fetch defendant
        $defendant = Defendant::getDefendantByDefendantID($case['defendant_ID']);
        
        // Fetch lawyer
        $lawyer = Lawyer::getLawyerByLawyerID($case['lawyer_ID']);
    
        // send data for user confirmation
        ($app->render)('standard', 'case_wizard/confirm_view', [
            'case' => $case,
            'event' => $event,
            'defendant' => $defendant,
            'lawyer' => $lawyer
        ]);
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function handle_confirm_step($app) {
    try {
        $data = $_SESSION['case'] ?? [];

        // Check required fields server-side (client-side already performed)
        if (empty($data['defendant_ID']) || empty($data['charges']) || empty($data['lawyer_ID'])) {
            throw new Exception("Missing required data in session. Please complete all steps.");
        }

        $db = Database::getInstance()->getConnection();

        $db->beginTransaction();

        // 1. Create Case Record
        $id = CaseRecord::create($data['defendant_ID']);
        
        // 2. Add All Charges
        insert_case_charges($db, $id, $data['charges']);
        
        // 3. Link Lawyer
        CaseRecord::linkLawyer($id, $data['lawyer_ID']);
        
        // 4. Add Court Events (optional)
        insert_case_events($db, $id, $data['events']);

        $db->commit();

        $userId = $_SESSION['user_id'] ?? null;
        $user = null;
        $username = 'Unknown User';

        if ($userId !== null) {
            $user = Auth::getCurrentUser(); 
            if ($user) {
                $username = $user['username'];
            }
        }

        $defendant = Defendant::getDefendantByDefendantID($data['defendant_ID']);
        $lawyer = Lawyer::getLawyerByLawyerID($data['lawyer_ID']);

        $charges = array_map(fn($charge) => $charge['description'], $data['charges']);
        $chargeText = implode(', ', $charges);

        // Get the first event (for date & location if available)
        $firstEvent = $data['events'][0] ?? ['date' => 'N/A', 'location' => 'N/A'];
        $eventDate = $firstEvent['date'] ?? 'N/A';
        $eventLocation = $firstEvent['location'] ?? 'N/A';

        $logMessage = sprintf(
            "%s (UserID: %s) added a new case (CaseID: %s). \n Defendant: %s, \n Charges: %s, \n Courtroom: %s, \n Date: %s, \n Location: %s, \n Lawyer: %s",
            $username,
            $userId ?? 'Unknown',
            $caseID,
            $defendant['Name'] ?? 'Unknown',
            $chargeText,
            $firstEvent['description'] ?? 'N/A',
            $eventDate,
            $eventLocation,
            $lawyer['Name'] ?? 'Unknown'
        );

        // Log the action
        LogModel::log_action($_SESSION['user_id'] ?? null, $logMessage);

        unset($_SESSION['case']);

        header("Location: " . BASE_URL . "/case/success");
        exit;

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        render_error($app, $e->getMessage());
    }
}

function insert_case_charges($db, $id, $charges) { 
    // inserts charges for case
    foreach ($charges as $charge) {
        Charge::create($id, $charge);
    }
}

function insert_case_events($db, $id, $events) {
    // inserts events for case
    foreach ($events as $event) {
        if (!empty($event['description']) || !empty($event['date']) || !empty($event['location'])) {
            CourtEvent::create($id, $event);
        }
    }
}

function show_manage_cases($app) {
    try {
        // query all cases
        $cases = CaseRecord::getAllCasesWithDetails();

        ($app->render)('standard', 'all_entities/all_cases', [
            'cases' => $cases
        ]);
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function delete_case($app, $id) {
    try {
        // Fetch full case details
        $caseDetails = CaseRecord::getAllCasesWithDetails($caseID);
        error_log("Case details fetched for deletion: " . print_r($caseDetails, true));

        if (empty($caseDetails)) {
            throw new Exception("Case with ID $caseID not found.");
        }

        $defendant = $caseDetails[0]['defendant_name'] ?? 'Unknown';

        $charges = [];
        $events = [];
        $lawyers = [];

        foreach ($caseDetails as $detail) {
            // Log raw data
            error_log("Detail row: " . print_r($detail, true));

            // Collect charges
            $chargeType = $detail['charge_type'] ?? null;
            if (!empty($chargeType) && !in_array($chargeType, $charges, true)) {
                $charges[] = $chargeType;
            }

            // Collect events (if both description and date are present)
            $eventDesc = $detail['event_description'] ?? null;
            $eventDate = $detail['event_date'] ?? null;
            if (!empty($eventDesc) && !empty($eventDate)) {
                $eventEntry = [
                    'description' => $eventDesc,
                    'date' => $eventDate,
                ];

                // Avoid duplicates
                $exists = false;
                foreach ($events as $e) {
                    if ($e['description'] === $eventEntry['description'] && $e['date'] === $eventEntry['date']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $events[] = $eventEntry;
                }
            }

            // Collect lawyers
            $lawyerName = $detail['lawyer_name'] ?? null;
            if (!empty($lawyerName) && !in_array($lawyerName, $lawyers, true)) {
                $lawyers[] = $lawyerName;
            }
        }

        error_log("Charges collected: " . implode(', ', $charges));
        error_log("Events collected: " . print_r($events, true));
        error_log("Lawyers collected: " . implode(', ', $lawyers));

        // Delete the case from DB
        CaseRecord::deleteCaseByID($caseID);

        // Log user info
        $userID = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';

        if ($userID !== null) {
            $user = Auth::getCurrentUser();
            if (!empty($user['username'])) {
                $username = $user['username'];
            }
        }

        // Build readable event summary
        $eventSummaries = array_map(function($e) {
            return "{$e['description']} (on {$e['date']})";
        }, $events);

        // Final log summary
        $summary = sprintf(
            "%s (UserID: %s) deleted case ID %s for defendant '%s'. \n Charges: %s. \n Events: %s. \n Lawyers: %s.",
            $username,
            $userID ?? 'N/A',
            $caseID,
            $defendant,
            !empty($charges) ? implode(', ', $charges) : 'None',
            !empty($eventSummaries) ? implode('; ', $eventSummaries) : 'None',
            !empty($lawyers) ? implode(', ', $lawyers) : 'None'
        );

        error_log("Summary for logging: $summary");

        // Save to log
        if ($userID) {
            LogModel::log_action($userID, $summary);
        }

        redirect_with_success("/case/manage", "Case deleted successfully.");
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function edit_case($app, $id) {
    try {
        // get charges and events for editing
        $charges = Charge::getChargesByCaseID($id);
        $events = CourtEvent::getEventsByCaseID($id);
    
($app->render)('standard', 'forms/edit_case', [
    'id' => $id,
    'charges' => $charges,
    'events' => $events
]);
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    } 
}
