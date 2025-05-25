<?php
// define Defendant DB fields
const LAWYER_FIELDS = [
    'name',
    'email',
    'phone',
    'firm'
];

require_once __DIR__ . '/../models/Lawyer.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/logs.php';

switch ($action) {
    // direct user to correct page
    case 'edit':
        save_lawyer($app, $lawyerID);
        break;
    case 'delete':
        delete_lawyer($app, $lawyerID);
        break;
    case 'add':
        save_lawyer($app);
        break;
    case 'manage':
        show_manage_lawyers($app);
        break;
    default:
        ($app->render)('standard', '404');
        exit;
}

function save_lawyer($app, $lawyerID = null) {
    // edit and add functionality combined into a single function
    try {
        // do not define defendantID if using for adding
        $isEdit = !is_null($lawyerID);
        $lawyer = $isEdit ? Lawyer::getLawyerByLawyerId($lawyerID) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = extract_post_data(LAWYER_FIELDS);

            // server side checking - there is also already client-side
            if (empty($data['name'])) {
                throw new Exception("Lawyer NOT " . ($isEdit ? "edited" : "added") . ". Error with input.");
            }

            if ($isEdit) { // update database
                $oldData = Lawyer::getLawyerByLawyerId($lawyerID);
                
                Lawyer::update($lawyerID, $data);

                $changes = [];
                foreach (LAWYER_FIELDS as $field) {
                    $oldValue = trim($oldData[ucfirst($field)] ?? '');
                    $newValue = trim($data[$field] ?? '');
                    if ($oldValue !== $newValue) {
                        $changes[] = ucfirst($field) . " changed from '$oldValue' to '$newValue'";
                    }
                }

                $changeSummary = empty($changes) ? "No changes were made." : implode("\n", $changes);

                LogModel::log_action($_SESSION['user_id'], "Edited lawyer ID $lawyerID.\n$changeSummary");

                $successMessage = "Lawyer updated successfully.";
            } else {
                Lawyer::create($data);

                $details = [];
                foreach (LAWYER_FIELDS as $field) {
                    $details[] = ucfirst($field) . ": '{$data[$field]}'";
                }
                $detailStr = implode("; ", $details);

                LogModel::log_action($_SESSION['user_id'], "Added new lawyer. $detailStr");

                $successMessage = "Lawyer added successfully.";
            }

            redirect_with_success("/lawyer/manage", $successMessage);
        }

        ($app->render)('standard', 'forms/lawyer_form', [
            'lawyer' => $lawyer,
            'isEdit' => $isEdit,
        ]);
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}


function delete_lawyer($app, $lawyerID) {
    try{
        // Fetch data before deleting for logging
        $lawyer = Lawyer::getLawyerByLawyerId($lawyerID);
        
        Lawyer::delete($lawyerID);

        $details = "Deleted lawyer ID $lawyerID. Details - ";

        $fieldMap = [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone_Number',
            'firm' => 'Firm',
        ];

        $parts = [];
        foreach (LAWYER_FIELDS as $field) {
            $dbField = $fieldMap[$field];
            $value = $lawyer[$dbField] ?? '';
            $parts[] = ucfirst($field) . ": '" . $value . "'";
        }
        
        $details .= implode("; ", $parts);

        LogModel::log_action($_SESSION['user_id'], $details);

        redirect_with_success("/lawyer/manage", "Lawyer deleted successfully.");
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function show_manage_lawyers($app) {
    // get all lawyers from DB
    $lawyers = Lawyer::getAllLawyersWithDetails();

    ($app->render)('standard', 'all_entities/all_lawyers', [
        'lawyers' => $lawyers
    ]);
}