<?php
require 'db.php';

if (isset($_GET['id'])) {
    $subtaskId = $_GET['id'];

    // ID основной задачи
    $stmt = $pdo->prepare("SELECT task_id, status FROM subtasks WHERE id = ?");
    $stmt->execute([$subtaskId]);
    $subtask = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subtask) {
        header('Location: index.php');
        exit;
    }

    $taskId = $subtask['task_id'];
    $subtaskStatus = $subtask['status'];

    // удалить субтаску
    $stmt = $pdo->prepare("DELETE FROM subtasks WHERE id = ?");
    $stmt->execute([$subtaskId]);

    // остались ли еще субтаски у этой задачи
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subtasks WHERE task_id = ?");
    $stmt->execute([$taskId]);
    $subtaskCount = $stmt->fetchColumn();

    if ($subtaskCount == 0) {
        // Если была последняя субтаска и не завершенна, оставлять основную задачу активной
        if ($subtaskStatus !== 'completed') {
            header('Location: index.php');
            exit;
        }

        // были ли удаленные субтаски завершены
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM subtasks WHERE task_id = ? AND status != 'completed'");
        $stmt->execute([$taskId]);
        $unfinishedSubtasks = $stmt->fetchColumn();

        // Если не осталось незавершенных субтасков, переводим основную задачу в сомплитед
        if ($unfinishedSubtasks == 0) {
            $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed', completed_at = NOW() WHERE id = ?");
            $stmt->execute([$taskId]);
        }
    }

    header('Location: index.php');
    exit;
}
