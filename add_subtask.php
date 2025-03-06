<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subtask_name'], $_POST['task_id'])) {
    $subtaskName = trim($_POST['subtask_name']);
    $taskId = (int) $_POST['task_id'];

    if (!empty($subtaskName) && $taskId > 0) {
        $stmt = $pdo->prepare("INSERT INTO subtasks (task_id, name) VALUES (:task_id, :name)");
        $stmt->execute(['task_id' => $taskId, 'name' => $subtaskName]);
    }
}

header('Location: index.php');
exit;
