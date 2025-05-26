<?php
session_start();

require_once __DIR__ . '/../models/Logs.php';

$controller = new LogController();
$controller->index($app);

class LogController
{
    public function index($app)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $logsPerPage = 10;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $logsPerPage;

        $logs = LogModel::getPaginatedLogs($logsPerPage, $offset);
        $totalLogs = LogModel::countLogs();
        $totalPages = ceil($totalLogs / $logsPerPage);

        ($app->render)('standard', 'logs', [
            'logs' => $logs,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }
}
