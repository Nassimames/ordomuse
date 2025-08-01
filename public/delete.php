<?php
session_start();
require_once __DIR__ . '/../classes/TaskManager.php';

$filter = $_GET['filter'] ?? 'all';
if (isset($_GET['id']) && isset($_GET['csrf_token']) && $_GET['csrf_token'] === $_SESSION['csrf_token']) {
    $taskManager = new TaskManager();
    $taskManager->deleteTask($_GET['id']);
}
header('Location: index.php?filter=' . $filter);
exit;
