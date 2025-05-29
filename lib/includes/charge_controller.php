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
        $id = $_GET['caseID'] ?? null;
        if (!$id) {
            throw new Exception("Case ID required.");
        }

        $isEdit = isset($chargeID);
        $charge = $isEdit ? Charge::getChargeByChargeID($chargeID) : null;

        if ($isEdit && !$charge) {
            throw new Exception("Charge not found.");
        }

        // POST request: save new or updated charge
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

            if ($isEdit) {
                Charge::update($chargeID, $data);
                $message = "Charge updated successfully.";
            } else {
                Charge::create($id, $data);
                $message = "Charge added successfully.";
            }

            redirect_with_success("/case/edit/" . $id, $message);
        }

        // GET request: render form
        ($app->render)('standard', 'forms/charge_form', [
            'caseID' => $id,
            'charge' => $charge,
            'isEdit' => $isEdit,
        ]);

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}

function delete_charge($app, $chargeID) {
    try {
        $id = $_GET['caseID'] ?? null;
        if (!$id) {
            throw new Exception("Case ID required.");
        }

        Charge::delete($chargeID);
        redirect_with_success("/case/edit/" . $id, "Charge deleted successfully.");

    } catch (Exception $e) {
        render_error($app, $e->getMessage());
    }
}
