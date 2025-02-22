<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$task = trim($_POST['task'] ?? '');
$category = trim($_POST['category'] ?? '');
$custom_category = trim($_POST['custom_category'] ?? '');
$due_date = trim($_POST['due_date'] ?? '');

// Ensure task is not empty
if (empty($task)) {
    die("❌ Task cannot be empty.");
}

// Use custom category if provided
if (!empty($custom_category)) {
    $category = $custom_category;
}

// Validate due date
if (empty($due_date) || !strtotime($due_date)) {
    die("❌ Invalid due date.");
}

// Prepare SQL
$stmt = $conn->prepare("INSERT INTO tasks (user_id, task, category, due_date, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("isss", $user_id, $task, $category, $due_date);

// Execute query
if ($stmt->execute()) {
    echo "✅ Task added successfully!";
} else {
    echo "❌ Task addition failed.";
}

// Close connection
$stmt->close();
$conn->close();
?>
