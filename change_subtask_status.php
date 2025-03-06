<?php
require 'db.php';

if (isset($_GET['id'], $_GET['status'])) {
    $status = $_GET['status'];
    $id = $_GET['id'];

    $stmt = $pdo->prepare("UPDATE subtasks SET status = ?, completed_at = NOW() WHERE id = ?");
    $stmt->execute([$status, $id]);

    // Проверяем, завершены ли все подзадачи
    $taskIdStmt = $pdo->prepare("SELECT task_id FROM subtasks WHERE id = ?");
    $taskIdStmt->execute([$id]);
    $taskId = $taskIdStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subtasks WHERE task_id = ? AND status != 'completed'");
    $stmt->execute([$taskId]);
    $remainingSubtasks = $stmt->fetchColumn();

    // Если все субтаски завершены, фиксируем завершение основной задачи
    if ($remainingSubtasks == 0) {
        $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed', completed_at = NOW() WHERE id = ?");
        $stmt->execute([$taskId]);
    }
}

header('Location: index.php');
exit;
