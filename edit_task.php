<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['task'])) {
    $task_id = $_POST['id'];
    $task = trim($_POST['task']);

    $query = "UPDATE tasks SET task = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $task, $task_id);

    if ($stmt->execute()) {
        echo "Task updated successfully!";
    } else {
        echo "Error updating task.";
    }
}
?>
