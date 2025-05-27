<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Auth.php';

if (!Auth::isAuthenticated()) {
    header("Location: " . BASE_URL . "/login");
    exit;
}

// define Defendant DB fields
const DEFENDANT_FIELDS = [
    'name',
    'dob',
    'address',
    'ethnicity',
    'phone',
    'email'
];

require_once __DIR__ . '/../models/Defendant.php';
require_once __DIR__ . '/../includes/helpers.php';

switch ($action) {
    // direct user to correct page
    case 'edit':
        save_defendant($app, $defendantID);
        break;
    case 'delete':
        delete_defendant($app, $defendantID);
        break;
    case 'add':
        save_defendant($app);
        break;
    case 'search':
        handle_search_defendants($app);
        break;
    case 'manage':
        show_manage_defendants($app);
        break;
    default:
        ($app->render)('standard', '404');
        exit;
}

function save_defendant($app, $defendantID = null) {
    // edit and add functionality combined into a single function

    try {
        // do not define defendantID if using for adding
        $isEdit = !is_null($defendantID);
        $defendant = $isEdit ? Defendant::getDefendantByDefendantId($defendantID) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = extract_post_data(DEFENDANT_FIELDS);

            // server side checking - there is also already client-side
            if (empty($data['name']) || empty($data['dob'])) {
                throw new Exception("Defendant NOT " . ($isEdit ? "edited" : "added") . ". Error with input.");
            }

            if ($isEdit) { // update database
                Defendant::update($defendantID, $data);
                $successMessage = "Defendant edited successfully.";
            } else {
                Defendant::create($data);
                $successMessage = "Defendant added successfully.";
            }

            redirect_with_success("/defendant/manage", $successMessage);
        }

        ($app->render)('standard', 'forms/defendant_form', [
            'defendant' => $defendant,
            'isEdit' => $isEdit,
        ]);
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function delete_defendant($app, $defendantID) {
    try {
        // update database
        Defendant::delete($defendantID);

        // Keep user on same page
        redirect_with_success("/defendant/manage", "Defendant deleted successfully.");
        
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function handle_search_defendants($app) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        $model = new Defendant();
        $query = $_GET ?? [];
        $results = [];
    
        if (!empty(trim($query['q'] ?? '')) && !empty($query['field'])) {
            $results = $model->search_fielded($query['field'], $query['q']);
        }
    
        ($app->set_message)('results', $results);
        ($app->set_message)('query', $query);
        ($app->render)('standard', 'search');
    }
}

function show_manage_defendants($app) {
    try {
        // get all defendants from DB
        $defendants = Defendant::getAllDefendantsWithDetails();

        ($app->render)('standard', 'all_entities/all_defendants', [
            'defendants' => $defendants
        ]);
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}