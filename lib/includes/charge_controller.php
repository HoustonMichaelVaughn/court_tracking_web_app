<?php

require_once __DIR__ . '/../models/Charge.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/logs.php'; 

// route internally within charge_controller
switch ($action) {
    case 'edit':
        save_charge($app, $chargeID);
        break;
    case 'add':
        save_charge($app);
        break;
    case 'delete':
        delete_charge($app, $chargeID);
        break;
    default:
        ($app->render)('standard', '404');
        exit;
}

// add and edit functionality combined into single function for DRY
function save_charge($app, $chargeID = null) {
    try {
        // caseID required for both adding and editing
        $caseID = $_GET['caseID'] ?? null;
        if (!$caseID) {
            throw new Exception("Case ID required.");
        }

        $isEdit = isset($chargeID);
        $charge = $isEdit ? Charge::getChargeByChargeID($chargeID) : null;

        if ($isEdit && !$charge) {
            throw new Exception("Charge not found.");
        }

        // get user data for POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? '';

            if (empty($description) || empty($status)) {
                throw new Exception("Description and status must be filled.");
            }

            $data = [
                'description' => $description,
                'status' => $status
            ];
            
            // database operations
            if ($isEdit) {
                list($oldData, $newData) = Charge::update($chargeID, $data);

            $userID = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            $username = $_SESSION['username'] ?? 'unknown';

            $logMessage = sprintf(
                "User %s (ID: %s) updated charge #%d. \n Description: '%s' → '%s', \n Status: '%s' → '%s'",
                $username,
                $userID,
                $chargeID,
                $oldData['Description'] ?? '',
                $newData['description'],
                $oldData['Status'] ?? '',
                $newData['status']
            );

        LogModel::log_action($userID, $logMessage);
        $successMessage = "Charge updated successfully.";
        } else {
        Charge::create($caseID, $data);

            $userID = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            $username = $_SESSION['username'] ?? 'unknown';

            $logMessage = sprintf(
                "User %s (ID: %s) added new charge to case #%d. \n Description: '%s', \n Status: '%s'",
            $username,
            $userID,
            $caseID,
            $data['description'],
            $data['status']
            );

        LogModel::log_action($userID, $logMessage);
        $successMessage = "Charge added successfully.";
        }

            redirect_with_success("/case/edit/" . $caseID, $successMessage);
        }

        // Render form on GET request
        ($app->render)('standard', 'forms/charge_form', [
            'caseID' => $caseID,
            'charge' => $charge,
            'isEdit' => $isEdit,
        ]);

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function delete_charge($app, $chargeID) {
    try {
        $caseID = $_GET['caseID'] ?? null;
        if (!$caseID) {
            throw new Exception("Case ID required.");
        }

        // get charge before deletion for logging
        $charge = Charge::getChargeByChargeID($chargeID);

        // perform database operation
        Charge::delete($chargeID);

        $userID = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $username = $_SESSION['username'] ?? 'unknown';

        $logMessage = sprintf(
            "User %s (ID: %s) deleted charge #%d from case #%d. \n Description: '%s', \n Status: '%s'",
        $username,
        $userID,
        $chargeID,
        $caseID,
        $charge['Description'] ?? '',
        $charge['Status'] ?? ''
        );

        LogModel::log_action($userID, $logMessage);

        redirect_with_success("/case/edit/" . $caseID, "Charge deleted successfully.");
        
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}