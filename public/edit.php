<?php
session_start();
require_once __DIR__ . '/../classes/TaskManager.php';

$taskManager = new TaskManager();
$task = null;

// Récupérer la tâche via une méthode propre
if (isset($_GET['id'])) {
    $task = $taskManager->getTaskById($_GET['id']);
}

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
    // Sécurité CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Erreur de validation CSRF');
    }

    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';

    $taskManager->updateTask($_GET['id'], $title, $description);
    header('Location: index.php');
    exit;
}

// Générer un token CSRF si non présent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une tâche - NassiTask</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="container">
        <h1>Modifier la tâche</h1>
        <?php if ($task): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                <textarea name="description"><?= htmlspecialchars($task['description'] ?? '') ?>"></textarea>
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