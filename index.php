<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM tasks WHERE status != 'completed' ORDER BY created_at DESC");
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Менеджер задач</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/aac4fe91c8.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>

    <div class="container mt-4 custom">

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-active-tasks-tab" data-toggle="tab" href="#nav-active-tasks" role="tab">Активные задачи</a>
                <a class="nav-item nav-link" id="nav-completed-tasks-tab" data-toggle="tab" href="#nav-completed-tasks" role="tab">Завершенные задачи</a>
            </div>
        </nav>

        <div class="tab-content mt-3" id="nav-tabContent">
            <!-- Вкладка активных задач -->
            <div class="tab-pane fade show active" id="nav-active-tasks" role="tabpanel">

                <!-- Форма добавления новой задачи -->
                <form action="task_manager.php" method="post">
                    <div class="input-group">
                        <div class="input-group-prepend">
                        </div>
                        <input type="text" name="task_name" required placeholder="Введите название задачи" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary">Добавить</button>
                        </div>
                    </div>
                </form>



                <div class="accordion" id="tasksAccordion">
                    <?php foreach ($tasks as $index => $task): ?>
                        <?php
                        // Получаем подзадачи
                        $stmt_subtasks = $pdo->prepare("SELECT * FROM subtasks WHERE task_id = ?");
                        $stmt_subtasks->execute([$task['id']]);
                        $subtasks = $stmt_subtasks->fetchAll();

                        $totalSubtasks = count($subtasks);
                        $completedSubtasks = count(array_filter($subtasks, fn($subtask) => $subtask['status'] == 'completed'));

                        // прогресс
                        $progress = ($totalSubtasks > 0) ? round(($completedSubtasks / $totalSubtasks) * 100, 1) : ($task['status'] == 'completed' ? 100 : 0);
                        ?>
                        <div class="card">
                            <div class="card-header" id="heading<?= $index ?>">
                                <h2 class="mb-0">
                                    <button class="btn btn-link toggle-icon collapsed" type="button" data-toggle="collapse"
                                        data-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                                        <i class="fa-solid fa-sort-down" aria-hidden="true"></i> <span class="task-text"><?= htmlspecialchars($task['name']) ?></span>
                                        <span class="badge badge-<?php echo $task['status'] === 'completed' ? 'success' : 'progress'; ?>">
                                            <?= ucfirst($task['status']) ?>
                                        </span>
                                    </button>



                                    <!-- Прогресс-бар -->
                                    <div class="progress w-50">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%;"
                                            aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $progress ?>%
                                        </div>
                                    </div>
                                    <div class="taskmainbuttons float-right">
                                        <!-- Кнопки управления задачей -->
                                        <?php if ($task['status'] == 'created'): ?>
                                            <a href="change_status.php?id=<?= $task['id'] ?>&status=in_progress" class="btn btn-sm btn-success"><i class="fa-solid fa-play"></i></a>
                                        <?php endif; ?>

                                        <?php if ($task['status'] == 'in_progress' && empty($subtasks)): ?>
                                            <a href="change_status.php?id=<?= $task['id'] ?>&status=completed" class="btn btn-sm btn-warning"><i class="fa-solid fa-flag-checkered"></i></a>
                                        <?php endif; ?>

                                        <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger ">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </h2>
                            </div>

                            <div id="collapse<?= $index ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>"
                                aria-labelledby="heading<?= $index ?>" data-parent="#tasksAccordion">
                                <div class="card-body">

                                    <!-- Список субтасков -->
                                    <?php
                                    $stmt_subtasks = $pdo->prepare("SELECT * FROM subtasks WHERE task_id = ?");
                                    $stmt_subtasks->execute([$task['id']]);
                                    $subtasks = $stmt_subtasks->fetchAll();
                                    ?>

                                    <ul class="list-group">
                                        <?php foreach ($subtasks as $subtask): ?>
                                            <li class="list-group-item <?php echo $subtask['status'] === 'completed' ? '' : ''; ?>">

                                                <!-- Кнопка переключения статуса -->
                                                <?php if ($task['status'] === 'in_progress'): ?>
                                                    <?php if ($subtask['status'] === 'completed'): ?>
                                                        <a href="change_subtask_status.php?id=<?= $subtask['id'] ?>&status=created" class="text-dark">
                                                            <i class="fa-solid fa-square-check text-success checkbox-icon"></i> <!-- Активный чекбокс ✅ -->
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="change_subtask_status.php?id=<?= $subtask['id'] ?>&status=completed" class="text-dark">
                                                            <i class="fa-regular fa-square checkbox-icon"></i> <!-- Неактивный чекбокс ⬜ -->
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <i class="fa-regular fa-square checkbox-icon text-muted"></i> <!-- Заблокированный чекбокс -->
                                                <?php endif; ?>

                                                <!-- Название субтаска -->
                                                <span class="<?= $subtask['status'] === 'completed' ? 'completed-subtask' : 'created-subtask'; ?>">
                                                    <?= htmlspecialchars($subtask['name']) ?>
                                                </span>

                                                <!-- Статус -->
                                                <span class="badge badge-<?php echo $subtask['status'] === 'completed' ? 'success' : 'secondary'; ?>">
                                                    <?= ucfirst($subtask['status']) ?>
                                                </span>

                                                <!-- Кнопка удаления -->
                                                <a href="delete_subtask.php?id=<?= $subtask['id'] ?>" class="btn btn-sm btn-danger float-right">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>



                                    <!-- Форма добавления субтаски -->
                                    <div class="button-container" onclick="toggleSubtaskForm(<?= $task['id'] ?>)">
                                        <button class="btn btn-sm btn-primary submanager">
                                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <form id="subtask-form-<?= $task['id'] ?>" class="hidden mt-2" action="add_subtask.php" method="post">

                                        <div class="input-group">
                                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                            <div class="input-group-prepend">
                                            </div>
                                            <input type="text" name="subtask_name" required placeholder="Добавить подзадачу" class="form-control">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-outline-secondary">+</button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php if (!empty($subtasks)): ?>
                                        <div class="force-wrapper">
                                            <a href="force_complete.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger force">
                                                Принудительно завершить весь таск
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Вкладка завершенных задач -->
            <div class="tab-pane fade" id="nav-completed-tasks" role="tabpanel">
                <?php include 'completed_tasks.php'; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSubtaskForm(taskId) {
            document.getElementById('subtask-form-' + taskId).classList.toggle('hidden');
        }
    </script>

</body>

</html>