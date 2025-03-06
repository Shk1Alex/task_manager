document.addEventListener("DOMContentLoaded", function () {
    console.log("JS загружен!");

    // Начать задачу
    document.querySelectorAll(".start-task").forEach(button => {
        button.addEventListener("click", function () {
            let taskId = this.dataset.taskId;
            changeStatus(taskId, "in_progress");
        });
    });

    // Завершить задачу
    document.querySelectorAll(".complete-task").forEach(button => {
        button.addEventListener("click", function () {
            let taskId = this.dataset.taskId;
            changeStatus(taskId, "completed");
        });
    });

    // Добавить подзадачу
    document.querySelectorAll(".add-subtask").forEach(button => {
        button.addEventListener("click", function () {
            let taskId = this.dataset.taskId;
            document.querySelector(`#subtask-form-${taskId}`).style.display = "block";
        });
    });

    // Удалить задачу
    document.querySelectorAll(".delete-task").forEach(button => {
        button.addEventListener("click", function () {
            let taskId = this.dataset.taskId;
            if (confirm("Вы уверены, что хотите удалить задачу?")) {
                fetch(`actions/delete_task.php?id=${taskId}`)
                    .then(response => response.text())
                    .then(() => {
                        location.reload(); // Обновляем страницу
                    });
            }
        });
    });

    // Функция для изменения статуса
    function changeStatus(taskId, status) {
        fetch(`actions/change_status.php?id=${taskId}&status=${status}`)
            .then(response => response.text())
            .then(() => {
                location.reload();
            });
    }
});





