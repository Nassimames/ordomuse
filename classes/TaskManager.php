<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Task.php';

class TaskManager
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getTasks($filter = 'all')
    {
        $query = "SELECT * FROM tasks";
        if ($filter === 'done') {
            $query .= " WHERE is_done = 1";
        } elseif ($filter === 'not_done') {
            $query .= " WHERE is_done = 0";
        }
        $query .= " ORDER BY created_at DESC";
        $stmt = $this->conn->query($query);
        $tasks = [];
        while ($row = $stmt->fetch()) {
            $tasks[] = new Task($row['id'], $row['title'], $row['description'], $row['is_done'], $row['created_at'], $row['updated_at']);
        }
        return $tasks;
    }

    public function addTask($title, $description = '')
    {
        $title = htmlspecialchars($title);
        $description = htmlspecialchars($description);
        if (empty($title)) {
            return false;
        }
        $stmt = $this->conn->prepare("INSERT INTO tasks (title, description) VALUES (:title, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function updateTask($id, $title, $description)
    {
        $title = htmlspecialchars($title);
        $description = htmlspecialchars($description);
        if (empty($title)) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE tasks SET title = :title, description = :description WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function deleteTask($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toggleTask($id)
    {
        $stmt = $this->conn->prepare("UPDATE tasks SET is_done = NOT is_done WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTaskById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
