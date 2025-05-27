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
const LAWYER_FIELDS = [
    'name',
    'email',
    'phone',
    'firm'
];

require_once __DIR__ . '/../models/Lawyer.php';
require_once __DIR__ . '/../includes/helpers.php';

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
                Lawyer::update($lawyerID, $data);
                $successMessage = "Lawyer updated successfully.";
            } else {
                Lawyer::create($data);
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

    Lawyer::delete($lawyerID);

    // Keep user on same page
    redirect_with_success("/lawyer/manage", "Lawyer deleted successfully.");
}

function show_manage_lawyers($app) {
    // get all lawyers from DB
    $lawyers = Lawyer::getAllLawyersWithDetails();

    ($app->render)('standard', 'all_entities/all_lawyers', [
        'lawyers' => $lawyers
    ]);
}