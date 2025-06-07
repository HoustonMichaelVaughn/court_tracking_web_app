<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Logs.php';

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
require_once __DIR__ . '/helpers.php';

switch ($action) {
    // direct user to correct page
    case 'edit':
        save_defendant($app, $id);
        break;
    case 'delete':
        delete_defendant($app, $id);
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
    try {
        $isEdit = isset($defendantID);
        $defendant = $isEdit ? Defendant::getDefendantByDefendantID($defendantID) : null;

        if ($isEdit && !$defendant) {
            throw new Exception("Defendant not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'dob' => $_POST['dob'] ?? '',
                'ethnicity' => $_POST['ethnicity'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];

            $userID = $_SESSION['user_id'] ?? null;
            $username = $_SESSION['username'] ?? 'Unknown';

            if ($isEdit) {
                $old = $defendant;
                Defendant::update($defendantID, $data);

                $fields = [
                    'Name' => ['name', 'Name'],
                    'Date_of_Birth' => ['dob', 'Date of Birth'],
                    'Ethnicity' => ['ethnicity', 'Ethnicity'],
                    'Phone_Number' => ['phone', 'Phone'],
                    'Address' => ['address', 'Address'],
                    'Email' => ['email', 'Email']
                ];

                $changes = [];
                foreach ($fields as $dbField => [$key, $label]) {
                    $before = $old[$dbField] ?? '';
                    $after = $data[$key];
                    if ($before !== $after) {
                        $changes[] = "$label: '$before' → '$after'";
                    }
                }

                $summary = $changes ? implode("; ", $changes) : "No changes were made.";
                $log = "User $username (ID: $userID) updated defendant ID $defendantID. $summary";

                LogModel::log_action($userID, $log);
                redirect_with_success("/defendants", "Defendant updated successfully.");
            } else {
                $newID = Defendant::create($data);

                $log = sprintf(
                    "User %s (ID: %s) added new defendant (ID: %s):\nName: %s\nDOB: %s\nEthnicity: %s\nPhone: %s\nAddress: %s\nEmail: %s",
                    $username,
                    $userID,
                    $newID,
                    $data['name'],
                    $data['dob'],
                    $data['ethnicity'],
                    $data['phone'],
                    $data['address'],
                    $data['email']
                );

                LogModel::log_action($userID, $log);
                redirect_with_success("/defendants", "Defendant added successfully.");
            }
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
        $defendant = Defendant::getDefendantByDefendantID($defendantID);
        if (!$defendant) {
            throw new Exception("Defendant not found.");
        }

        Defendant::delete($defendantID);

        $userID = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'Unknown';

        $log = sprintf(
            "User %s (ID: %s) deleted defendant ID %s:\nName: %s\nDOB: %s\nEthnicity: %s\nPhone: %s\nAddress: %s\nEmail: %s",
            $username,
            $userID,
            $defendantID,
            $defendant['Name'] ?? 'N/A',
            $defendant['Date_of_Birth'] ?? 'N/A',
            $defendant['Ethnicity'] ?? 'N/A',
            $defendant['Phone_Number'] ?? 'N/A',
            $defendant['Address'] ?? 'N/A',
            $defendant['Email'] ?? 'N/A'
        );

        LogModel::log_action($userID, $log);

        redirect_with_success("/defendants", "Defendant deleted successfully.");
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