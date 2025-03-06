<?php
require 'db.php';

if (isset($_GET['id'], $_GET['status'])) {
    $status = $_GET['status'];
    $id = $_GET['id'];

    $timeField = ($status == 'in_progress') ? 'started_at' : 'completed_at';
    $stmt = $pdo->prepare("UPDATE tasks SET status = ?, $timeField = NOW() WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header('Location: index.php');
exit;
?>
