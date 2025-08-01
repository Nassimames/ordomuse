<?php
session_start();
require_once __DIR__ . '/../classes/TaskManager.php';

$taskManager = new TaskManager();
$filter = $_GET['filter'] ?? 'all';
$tasks = $taskManager->getTasks($filter);

// Générer un jeton CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Erreur de validation CSRF');
    }
    $description = $_POST['description'] ?? '';
    $taskManager->addTask($_POST['title'], $description);
    header('Location: index.php?filter=' . $filter);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordomuse - Gestion de Tâches</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>

<body>
    <div class="container">
        <h1>Ordomuse - Mes Tâches</h1>
        <p>Total des tâches : <?= count($tasks) ?></p>
        <?php
        $done_count = count(array_filter($tasks, fn($task) => $task->is_done));
        $not_done_count = count($tasks) - $done_count;
        ?>
        <p>Tâches terminées : <?= $done_count ?> | Tâches non terminées : <?= $not_done_count ?></p>

        <!-- Filtre -->
        <form>
            <label for="filter">Filtrer :</label>
            <select name="filter" onchange="this.form.submit()">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Toutes</option>
                <option value="done" <?= $filter === 'done' ? 'selected' : '' ?>>Terminées</option>
                <option value="not_done" <?= $filter === 'not_done' ? 'selected' : '' ?>>Non terminées</option>
            </select>
        </form>

        <!-- Formulaire d'ajout -->
        <form id="add-task-form" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="title" placeholder="Titre de la tâche" required>
            <textarea name="description" placeholder="Description (optionnel)"></textarea>
            <button type="submit">Ajouter</button>
        </form>

        <!-- Liste des tâches -->
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task->is_done ? 'done' : '' ?>" data-id="<?= $task->id ?>">
                    <div>
                        <strong><?= htmlspecialchars($task->title) ?></strong>
                        <p><?= htmlspecialchars($task->description) ?: 'Aucune description' ?></p>
                        <small>Créée le : <?= $task->created_at ?> | Modifiée le : <?= $task->updated_at ?></small>
                    </div>
                    <div class="actions">
                        <a href="toggle.php?id=<?= $task->id ?>&filter=<?= $filter ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>">✓</a>
                        <a href="edit.php?id=<?= $task->id ?>">✏️</a>
                        <a href="#" class="delete-task" data-id="<?= $task->id ?>">🗑️</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>