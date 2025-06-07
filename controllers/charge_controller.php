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
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../models/Logs.php';

// Route internally within charge_controller
switch ($action) {
    case 'edit':
        save_charge($app, $id);
        break;
    case 'add':
        save_charge($app);
        break;
    case 'delete':
        delete_charge($app, $id);
        break;
    default:
        ($app->render)('standard', '404');
        exit;
}

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
                $oldData = $charge;

                Charge::update($chargeID, $data);

                $logMessage = sprintf(
                    "User %s (ID: %s) updated charge #%d.\nDescription: '%s' → '%s'\nStatus: '%s' → '%s'",
                    $username,
                    $userID,
                    $chargeID,
                    $oldData['description'] ?? $oldData['Description'] ?? '',
                    $data['description'],
                    $oldData['status'] ?? $oldData['Status'] ?? '',
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

        // GET: Render form
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

        $charge = Charge::getChargeByChargeID($chargeID);
        if (!$charge) {
            throw new Exception("Charge not found.");
        }

        $description = $charge['description'] ?? $charge['Description'] ?? '[unknown]';
        $status = $charge['status'] ?? $charge['Status'] ?? '[unknown]';

        Charge::delete($chargeID);

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
