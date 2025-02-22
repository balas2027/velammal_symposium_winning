<?php
session_start();
include 'db.php'; // Ensure db.php correctly initializes $conn

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search_query = trim($_GET['search'] ?? ''); // Get search input

// Fetch tasks based on search input
if (!empty($search_query)) {
    $query = "SELECT * FROM tasks WHERE user_id = ? AND task LIKE ? ORDER BY due_date ASC";
    $stmt = $conn->prepare($query);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("is", $user_id, $search_param);
} else {
    $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch categories
$categories = ["Work", "Personal", "Shopping", "Fitness", "Others"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
       /* General Reset */
/* Modern Dark Theme Variables */
:root {
    --bg-gradient: linear-gradient(135deg, #1a1c1e 0%, #0a0c0e 100%);
    --card-bg: rgba(32, 34, 37, 0.95);
    --input-bg: rgba(40, 42, 45, 0.9);
    --primary-blue: #4f6ef7;
    --primary-hover: #5e7af8;
    --text-primary: #ffffff;
    --text-secondary: #b3b8c3;
    --border-color: rgba(255, 255, 255, 0.1);
    --success-color: #43a047;
    --danger-color: #e53935;
    --warning-color: #fb8c00;
}

/* Base Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: var(--bg-gradient);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    color: var(--text-primary);
}

/* Container Styling */
.container {
    background: var(--card-bg);
    padding: 2.5rem;
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 800px;
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
    animation: containerFade 0.6s ease-out;
}

/* Profile Link */
.container a {
    color: var(--primary-blue);
    text-decoration: none;
    font-size: 1.2rem;
    transition: color 0.3s ease;
    display: inline-block;
    margin-bottom: 1rem;
}

.container a:hover {
    color: var(--primary-hover);
    transform: translateX(5px);
}

/* Heading Styles */
h2, h3 {
    color: var(--text-primary);
    font-weight: 700;
    letter-spacing: -0.5px;
}

h2 {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #4f6ef7 0%, #7795f8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Form Controls */
.form-control {
    background: var(--input-bg);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 14px 18px;
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
    margin-bottom: 1rem;
}

.form-control::placeholder {
    color: var(--text-secondary);
    opacity: 0.8;
    font-weight: 500;
}

.form-control:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 4px rgba(79, 110, 247, 0.15);
    outline: none;
    transform: translateY(-2px);
}

/* Button Styles */
.btn {
    border: none;
    border-radius: 12px;
    padding: 14px 24px;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.btn:hover::after {
    width: 300px;
    height: 300px;
}

.btn:active {
    transform: scale(0.98);
}

/* Search Button */
.btn-search {
    background: linear-gradient(135deg, #4f6ef7 0%, #7795f8 100%);
    color: white;
    width: 100%;
    margin-bottom: 1.5rem;
}

/* Add Task Button */
.btn-add {
    background: linear-gradient(135deg, #4f6ef7 0%, #7795f8 100%);
    color: white;
    width: 100%;
    margin-bottom: 1.5rem;
}

/* Filter Buttons */
.filter-btn {
    background: var(--input-bg);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    padding: 10px 20px;
    border-radius: 20px;
    margin: 0 5px 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    background: rgba(79, 110, 247, 0.1);
    color: var(--primary-blue);
    transform: translateY(-2px);
}

.filter-btn.active {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

/* Task Cards */
/* Task Card Container */
.task-card {
    background: rgba(45, 48, 53, 0.95);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.task-card:hover {
    transform: translateY(-3px);
    background: rgba(50, 53, 58, 0.95);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.15);
}

/* Task Input Styling */
.task-input {
    background: rgba(35, 38, 43, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #ffffff;
    font-size: 1.1rem;
    width: 100%;
    padding: 12px 16px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.task-input:focus {
    outline: none;
    border-color: #4f6ef7;
    box-shadow: 0 0 0 2px rgba(79, 110, 247, 0.2);
    background: rgba(40, 43, 48, 0.95);
}

/* Task Details */
.card-text {
    color: #b3b8c3;
    font-size: 0.95rem;
    margin: 1rem 0;
    line-height: 1.5;
}

.card-text strong {
    color: #ffffff;
    margin-right: 0.5rem;
}

/* Icon Styling */
.task-card [class^="icon-"] {
    color: #4f6ef7;
    margin-right: 0.5rem;
    font-size: 1.1rem;
}

/* Task Category Badge */
.category-badge {
    display: inline-block;
    padding: 4px 12px;
    background: rgba(79, 110, 247, 0.15);
    color: #4f6ef7;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
    margin: 0.5rem 0;
}

/* Action Buttons */
.task-card .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    margin-right: 0.8rem;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.task-card .btn-success {
    background: #43a047;
    color: white;
}

.task-card .btn-success:hover {
    background: #4caf50;
    transform: translateY(-2px);
}

.task-card .btn-danger {
    background: #e53935;
    color: white;
}

.task-card .btn-danger:hover {
    background: #f44336;
    transform: translateY(-2px);
}

/* Task Status Indicators */
.task-card.completed-task {
    opacity: 0.75;
    background: rgba(40, 43, 48, 0.8);
}

.task-card.completed-task .task-input {
    text-decoration: line-through;
    color: #b3b8c3;
}

.task-card.due-soon {
    border-left: 4px solid #fb8c00;
}

.task-card.due-over {
    border-left: 4px solid #e53935;
}

/* Due Date Styling */
.due-date {
    display: flex;
    align-items: center;
    color: #b3b8c3;
    margin: 0.5rem 0;
    font-size: 0.9rem;
}

.due-date::before {
    content: "📅";
    margin-right: 0.5rem;
}

/* Category Styling */
.category {
    display: flex;
    align-items: center;
    color: #b3b8c3;
    margin: 0.5rem 0;
    font-size: 0.9rem;
}

.category::before {
    content: "🏷";
    margin-right: 0.5rem;
}

/* Button Icons */
.btn-success::before {
    content: "";
    margin-right: 0.5rem;
}

.btn-danger::before {
    content: "❌";
    margin-right: 0.5rem;
}

/* Hover Effects */
.task-card .btn:active {
    transform: scale(0.98);
}

/* Task Card Animation */
@keyframes taskCardAppear {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.task-card {
    animation: taskCardAppear 0.3s ease-out;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .task-card {
        padding: 1.2rem;
    }

    .task-input {
        font-size: 1rem;
        padding: 10px 14px;
    }

    .task-card .btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--card-bg);
}

::-webkit-scrollbar-thumb {
    background: var(--primary-blue);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-hover);
}

/* Date Input Styling */
input[type="datetime-local"] {
    background: var(--input-bg);
    color: var(--text-primary);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 14px 18px;
}

input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    opacity: 0.6;
    cursor: pointer;
}

/* Selection Style */
::selection {
    background: var(--primary-blue);
    color: white;
}
    </style>
</head>
<body>

<div class="container">
   <a href="profile.php"><h3>Profile</h3></a>
    <h2 class="text-center">📌 My To-Do List</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control mb-2" placeholder="Search tasks..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn btn-info w-100">🔍 Search</button>
    </form>

    <!-- Add Task Form -->
    <form action="add_task.php" method="POST" class="mb-3">
        <input type="text" name="task" class="form-control mb-2" placeholder="Enter task..." required>
        <select name="category" class="form-control mb-2">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
            <option value="Custom">Custom</option>
        </select>
        <input type="text" name="custom_category" class="form-control mb-2" placeholder="Custom Category (optional)">
        <input type="datetime-local" name="due_date" class="form-control mb-2">
        <button type="submit" class="btn btn-primary w-100">➕ Add Task</button>
    </form>

    <!-- Filters -->
    <div class="mb-3">
        <button class="btn btn-outline-primary filter-btn" data-filter="all">All</button>
        <button class="btn btn-outline-success filter-btn" data-filter="completed">Completed</button>
        <button class="btn btn-outline-warning filter-btn" data-filter="pending">Pending</button>
        <button class="btn btn-outline-secondary filter-btn" data-filter="due-date">Due Soon</button>
    </div>

    <!-- Task List -->
   <!-- Task List -->
<div id="task-list">
    <?php while ($task = $result->fetch_assoc()): ?>
        <?php 
            $task_class = "";
            if ($task['status'] === 'completed') { 
                $task_class = "completed-task";
            } else if (strtotime($task['due_date']) < time()) { 
                $task_class = "due-over";
            } else if (strtotime($task['due_date']) - time() < 86400) { 
                $task_class = "due-soon";
            }
        ?>
        <div class="card task-card mb-2 <?= $task_class ?>" data-category="<?= htmlspecialchars($task['category']) ?>" data-status="<?= $task['status'] ?>">
            <div class="card-body">
                <input type="text" class="form-control task-input" data-id="<?= $task['id'] ?>" value="<?= htmlspecialchars($task['task']) ?>">
                <p class="card-text">
                    📅 <strong>Due:</strong> <?= htmlspecialchars($task['due_date']) ?><br>
                    🏷 <strong>Category:</strong> <?= htmlspecialchars($task['category']) ?>
                </p>
                <button class="btn btn-success btn-sm complete-task" data-id="<?= $task['id'] ?>">✔ Mark Done</button>
                <button class="btn btn-danger btn-sm delete-task" data-id="<?= $task['id'] ?>"> Delete</button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</div>

<script>
$(document).ready(function() {
    $(".filter-btn").click(function() {
        let filter = $(this).data("filter");

        $(".task-card").show();

        if (filter === "completed") {
            $(".task-card[data-status='pending']").hide();
        } else if (filter === "pending") {
            $(".task-card[data-status='completed']").hide();
        } else if (filter === "due-date") {
            $(".task-card:not(.due-soon)").hide();
        }
    });

    $(".complete-task").click(function() {
        let taskId = $(this).data("id");
        $.post("complete_task.php", { id: taskId }, function(response) {
            alert(response);
            location.reload();
        });
    });

    $(".delete-task").click(function() {
        let taskId = $(this).data("id");
        $.post("delete_task.php", { id: taskId }, function(response) {
            alert(response);
            location.reload();
        });
    });
});
</script>
<script>
$(document).ready(function() {
    $(".task-input").on("blur", function() {
        let taskId = $(this).data("id");
        let updatedTask = $(this).val();

        $.post("edit_task.php", { id: taskId, task: updatedTask }, function(response) {
            alert(response);
        });
    });

    $(".complete-task").click(function() {
        let taskId = $(this).data("id");
        $.post("complete_task.php", { id: taskId }, function(response) {
            alert(response);
            location.reload();
        });
    });

    $(".delete-task").click(function() {
        let taskId = $(this).data("id");
        $.post("delete_task.php", { id: taskId }, function(response) {
            alert(response);
            location.reload();
        });
    });
});
</script>


</body>
</html>
