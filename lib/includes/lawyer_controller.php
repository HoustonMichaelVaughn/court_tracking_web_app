<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Logs.php';
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
        save_lawyer($app, $id);
        break;
    case 'delete':
        delete_lawyer($app, $id);
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
    try {
        $isEdit = isset($lawyerID);
        $lawyer = $isEdit ? Lawyer::getLawyerByLawyerID($lawyerID) : null;

        if ($isEdit && !$lawyer) {
            throw new Exception("Lawyer not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'firm' => $_POST['firm'] ?? ''
            ];

            $userID = $_SESSION['user_id'] ?? null;
            $username = $_SESSION['username'] ?? 'Unknown';

            if ($isEdit) {
                $old = $lawyer;
                Lawyer::update($lawyerID, $data);

                $fields = [
                    'Name' => ['name', 'Name'],
                    'Email' => ['email', 'Email'],
                    'Phone_Number' => ['phone', 'Phone'],
                    'Firm' => ['firm', 'Firm']
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
                $log = "User $username (ID: $userID) updated lawyer ID $lawyerID. $summary";

                LogModel::log_action($userID, $log);
                redirect_with_success("/lawyers", "Lawyer updated successfully.");
            } else {
                $newID = Lawyer::create($data);

                $log = sprintf(
                    "User %s (ID: %s) added new lawyer (ID: %s):\nName: %s\nEmail: %s\nPhone: %s\nFirm: %s",
                    $username,
                    $userID,
                    $newID,
                    $data['name'],
                    $data['email'],
                    $data['phone'],
                    $data['firm']
                );

                LogModel::log_action($userID, $log);
                redirect_with_success("/lawyers", "Lawyer added successfully.");
            }
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
    try {
        $lawyer = Lawyer::getLawyerByLawyerID($lawyerID);
        if (!$lawyer) {
            throw new Exception("Lawyer not found.");
        }

        Lawyer::delete($lawyerID);

        $userID = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'Unknown';

        $log = sprintf(
            "User %s (ID: %s) deleted lawyer ID %s:\nName: %s\nEmail: %s\nPhone: %s\nFirm: %s",
            $username,
            $userID,
            $lawyerID,
            $lawyer['Name'] ?? 'N/A',
            $lawyer['Email'] ?? 'N/A',
            $lawyer['Phone_Number'] ?? 'N/A',
            $lawyer['Firm'] ?? 'N/A'
        );

        LogModel::log_action($userID, $log);

        redirect_with_success("/lawyers", "Lawyer deleted successfully.");
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