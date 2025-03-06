<?php
require 'db.php';

if (isset($_GET['id'])) {
    $taskId = $_GET['id'];

    // Принудительно завершаем задачу
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed', completed_at = NOW() WHERE id = ?");
    $stmt->execute([$taskId]);

    // Дополнительно можно удалить все субтаски (если нужно)
    //$stmt = $pdo->prepare("DELETE FROM subtasks WHERE task_id = ?");
    //$stmt->execute([$taskId]);
}

header('Location: index.php');
exit;
?>
