<?php
session_start();
require_once __DIR__ . '/../classes/TaskManager.php';

$taskManager = new TaskManager();
$task = null;
if (isset($_GET['id'])) {
    $stmt = $taskManager->conn->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $task = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Erreur de validation CSRF');
    }
    $taskManager->updateTask($_GET['id'], $_POST['title'], $_POST['description'] ?? '');
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une tâche - Ordomuse</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="container">
        <h1>Modifier la tâche</h1>
        <?php if ($task): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                <textarea name="description"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                <button type="submit">Mettre à jour</button>
                <a href="index.php">Annuler</a>
            </form>
        <?php else: ?>
            <p>Tâche introuvable.</p>
            <a href="index.php">Retour</a>
        <?php endif; ?>
    </div>
</body>

</html>