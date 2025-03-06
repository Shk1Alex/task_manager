<?php
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_name'])) {
    $taskName = trim($_POST['task_name']);

    if (!empty($taskName)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (name) VALUES (:name)");
        $stmt->execute(['name' => $taskName]);
    }
}

header('Location: index.php');
exit;
