<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$task_id = $_POST['id'] ?? 0;
if ($task_id == 0) {
    die("Invalid task ID.");
}

$stmt = $conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $task_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo "Task deleted successfully!";
} else {
    echo "Task deletion failed.";
}

$stmt->close();
$conn->close();
?>
