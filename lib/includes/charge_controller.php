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

// Route internally within charge_controller
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

// Add and edit functionality combined into single function
function save_charge($app, $chargeID = null) {
    try {
        $caseID = $_GET['caseID'] ?? null;
        if (!$caseID) {
            throw new Exception("Case ID required.");
        }

        $isEdit = isset($chargeID);
        $charge = $isEdit ? Charge::getChargeByChargeID($chargeID) : null;

        if ($isEdit && !$charge) {
            throw new Exception("Charge not found.");
        }

        // POST: Save form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = trim($_POST['description'] ?? '');
            $status = trim($_POST['status'] ?? '');

            if (empty($description) || empty($status)) {
                throw new Exception("Description and status must be filled.");
            }

            $data = [
                'description' => $description,
                'status' => $status
            ];

            $userID = $_SESSION['user_id'] ?? null;
            $username = $_SESSION['username'] ?? 'Unknown';

            if ($isEdit) {
                $old = $charge;
                Charge::update($chargeID, $data);

                $logMessage = sprintf(
                    "User %s (ID: %s) updated charge #%d.\nDescription: '%s' → '%s'\nStatus: '%s' → '%s'",
                    $username,
                    $userID,
                    $chargeID,
                    $old['description'] ?? '',
                    $data['description'],
                    $old['status'] ?? '',
                    $data['status']
                );

                $message = "Charge updated successfully.";
            } else {
                Charge::create($caseID, $data);

                $logMessage = sprintf(
                    "User %s (ID: %s) added new charge to case #%d.\nDescription: '%s'\nStatus: '%s'",
                    $username,
                    $userID,
                    $caseID,
                    $data['description'],
                    $data['status']
                );

                $message = "Charge added successfully.";
            }

            LogModel::log_action($userID, $logMessage);
            redirect_with_success("/case/edit/" . $caseID, $message);
        }

        // GET: Render charge form
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

        // Fetch charge details before deletion
        $charge = Charge::getChargeByChargeID($chargeID);
        if (!$charge) {
            throw new Exception("Charge not found.");
        }

        $description = $charge['description'] ?? '[unknown]';
        $status = $charge['status'] ?? '[unknown]';

        // Perform deletion
        Charge::delete($chargeID);

        // Logging
        $userID = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'Unknown';
        $logMessage = sprintf(
            "User %s (ID: %s) deleted charge #%d from case #%d.\nDescription: '%s'\nStatus: '%s'",
            $username,
            $userID,
            $chargeID,
            $caseID,
            $description,
            $status
        );
        LogModel::log_action($userID, $logMessage);

        redirect_with_success("/case/edit/" . $caseID, "Charge deleted successfully.");

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}
