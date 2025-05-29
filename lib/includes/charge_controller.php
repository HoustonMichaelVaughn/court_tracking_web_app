<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Auth.php';

if (!Auth::isAuthenticated()) {
    header("Location: " . BASE_URL . "/login");
    exit;
}

require_once __DIR__ . '/../models/Charge.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Logs.php';

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
                $oldData = Charge::getChargeByChargeID($chargeID);

                Charge::update($chargeID, $data);

                $userID = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
                $username = $_SESSION['username'] ?? 'unknown';


            $logMessage = sprintf(
                "User %s (ID: %s) updated charge #%d. \n Description: '%s' → '%s', \n Status: '%s' → '%s'",
                $username,
                $userID,
                $chargeID,
                $oldData['Description'] ?? $oldData['description'] ?? '',
                $data['description'],
                $oldData['Status'] ?? $oldData['status'] ?? '',
                $data['status']
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
    
        // Fetch charge details before deleting
        $charge = Charge::getChargeByChargeID($chargeID);
        if (!$charge) {
            throw new Exception("Charge not found.");
        }

        // Safely access fields (assuming lowercase keys)
        $description = $charge['description'] ?? '[unknown]';
        $status = $charge['status'] ?? '[unknown]';
        
        // perform database operation
        Charge::delete($chargeID);

        $details = "Deleted charge ID $chargeID from case ID $caseID. \n ";
        $details .= "Details - Description: '{$charge['Description']}',\n Status: '{$charge['Status']}'.";

        LogModel::log_action($_SESSION['user_id'], $details);

        // Redirect back to edit case page
        redirect_with_success("/case/edit/" . $caseID, "Charge deleted successfully.");  
        
    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}