<?php

require_once __DIR__ . '/../models/logs.php';

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
                $oldData = $defendant;

                Defendant::update($defendantID, $data);

                $changes = [];
                foreach (DEFENDANT_FIELDS as $field) {
                    $oldValue = $oldData[ucfirst($field)] ?? '';
                    $newValue = $data[$field] ?? '';
                    if ($oldValue != $newValue) {
                        $changes[] = ucfirst($field) . " changed from '$oldValue' to '$newValue'";
                    }
                }
                $changeSummary = implode("\n", $changes);
                if (empty($changeSummary)) {
                    $changeSummary = "No changes were made.";
                }

                LogModel::log_action($_SESSION['user_id'], "Edited defendant ID $defendantID. $changeSummary");

                $successMessage = "Defendant edited successfully.";

            } else {
                Defendant::create($data);

                $details = [];
                foreach (DEFENDANT_FIELDS as $field) {
                    $details[] = ucfirst($field) . ": '{$data[$field]}'";
                }
                $detailStr = implode("; ", $details);

                LogModel::log_action($_SESSION['user_id'], "Added new defendant. $detailStr");

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

        // Fetch data before deleting for logging
        $defendant = Defendant::getDefendantByDefendantId($defendantID);

        // update database
        Defendant::delete($defendantID);

        $details = "Deleted defendant ID $defendantID. Details - ";

        $fieldMap = [
            'name'     => 'Name',
            'dob'      => 'Date_of_Birth',
            'address'  => 'Address',
            'ethnicity'=> 'Ethnicity',
            'phone'    => 'Phone_Number',
            'email'    => 'Email',
        ];

        $parts = [];
        foreach (DEFENDANT_FIELDS as $field) {
            $dbField = $fieldMap[$field];
            $value = $defendant[$dbField] ?? '';
            $parts[] = ucfirst($field) . ": '" . $value . "'";
        }
        
        $details .= implode("; ", $parts);

        LogModel::log_action($_SESSION['user_id'], $details);

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