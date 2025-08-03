<?php
session_start();
require_once __DIR__ . '/../classes/TaskManager.php';

if (isset($_GET['id']) && isset($_GET['status']) && isset($_GET['csrf_token']) && $_GET['csrf_token'] === $_SESSION['csrf_token']) {
    $taskManager = new TaskManager();
    $taskManager->updateStatus($_GET['id'], $_GET['status']);
}
header('Location: index.php');
exit;
