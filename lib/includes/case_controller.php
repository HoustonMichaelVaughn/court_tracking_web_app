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

        // Validate required fields
        if (empty($data['defendant_ID']) || empty($data['charges']) || empty($data['lawyer_ID'])) {
            throw new Exception("Missing required data in session. Please complete all steps.");
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // Step 1: Create Case
        $caseID = CaseRecord::create($data['defendant_ID']);

        // Step 2: Add Charges
        insert_case_charges($db, $caseID, $data['charges']);

        // Step 3: Link Lawyer
        CaseRecord::linkLawyer($caseID, $data['lawyer_ID']);

        // Step 4: Add Court Events (if any)
        insert_case_events($db, $caseID, $data['events']);

        $db->commit();
        unset($_SESSION['case']);

        // Prepare log context
        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId) {
            $user = Auth::getCurrentUser();
            $username = $user['username'] ?? $username;
        }

        $defendant = Defendant::getDefendantByDefendantID($data['defendant_ID']);
        $lawyer = Lawyer::getLawyerByLawyerID($data['lawyer_ID']);
        $charges = array_map(fn($c) => $c['description'], $data['charges']);
        $chargeText = implode(', ', $charges);

        $firstEvent = $data['events'][0] ?? ['description' => 'N/A', 'date' => 'N/A', 'location' => 'N/A'];

        $logMessage = sprintf(
            "%s (UserID: %s) added a new case (CaseID: %s).\nDefendant: %s,\nCharges: %s,\nCourtroom: %s,\nDate: %s,\nLocation: %s,\nLawyer: %s",
            $username,
            $userId ?? 'Unknown',
            $caseID,
            $defendant['Name'] ?? 'Unknown',
            $chargeText,
            $firstEvent['description'] ?? 'N/A',
            $firstEvent['date'] ?? 'N/A',
            $firstEvent['location'] ?? 'N/A',
            $lawyer['Name'] ?? 'Unknown'
        );

        LogModel::log_action($userId, $logMessage);

        header("Location: " . BASE_URL . "/case/success");
        exit;

    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) {
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

function delete_case($app, $caseID) {
    try {
        // Fetch case details before deletion
        $caseDetails = CaseRecord::getAllCasesWithDetails($caseID);
        if (empty($caseDetails)) {
            throw new Exception("Case with ID $caseID not found.");
        }

        $defendant = $caseDetails[0]['defendant_name'] ?? 'Unknown';

        $charges = [];
        $events = [];
        $lawyers = [];

        foreach ($caseDetails as $detail) {
            if (!empty($detail['charge_type']) && !in_array($detail['charge_type'], $charges, true)) {
                $charges[] = $detail['charge_type'];
            }

            if (!empty($detail['event_description']) && !empty($detail['event_date'])) {
                $eventEntry = "{$detail['event_description']} (on {$detail['event_date']})";
                if (!in_array($eventEntry, $events, true)) {
                    $events[] = $eventEntry;
                }
            }

            if (!empty($detail['lawyer_name']) && !in_array($detail['lawyer_name'], $lawyers, true)) {
                $lawyers[] = $detail['lawyer_name'];
            }
        }

        // Perform deletion
        CaseRecord::deleteCaseByID($caseID);

        // Build log message
        $userId = $_SESSION['user_id'] ?? null;
        $username = 'Unknown User';
        if ($userId) {
            $user = Auth::getCurrentUser();
            $username = $user['username'] ?? $username;
        }

        $logMessage = sprintf(
            "%s (UserID: %s) deleted case ID %s for defendant '%s'.\nCharges: %s.\nEvents: %s.\nLawyers: %s.",
            $username,
            $userId ?? 'N/A',
            $caseID,
            $defendant,
            implode(', ', $charges) ?: 'None',
            implode('; ', $events) ?: 'None',
            implode(', ', $lawyers) ?: 'None'
        );

        LogModel::log_action($userId, $logMessage);

        // Return to case list
        show_manage_cases($app);

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
