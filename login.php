<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashedPassword)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["username"] = $username;
        header("Location: index.php");
    } else {
        echo "Invalid login credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: radial-gradient(circle, #ff9a9e, #fad0c4);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    overflow: hidden;
    color: #333;
}

form {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 350px;
    animation: popIn 1s ease-in-out;
}

form h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    color: #555;
}

form input {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

form input:focus {
    border-color: #ff758c;
    box-shadow: 0 0 8px rgba(255, 117, 140, 0.5);
    outline: none;
}

button {
    width: 100%;
    padding: 0.8rem;
    background: linear-gradient(45deg, #ff758c, #ff7eb3);
    color: #fff;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}

button:hover {
    background: linear-gradient(45deg, #ff7eb3, #ff758c);
    transform: translateY(-3px);
}

form a {
    display: block;
    text-align: center;
    margin-top: 1rem;
    color: #ff758c;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

form a:hover {
    color: #ff4b69;
}

/* Animation for form */
@keyframes popIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Media Query for responsiveness */
@media (max-width: 768px) {
    form {
        padding: 1.5rem;
    }

    form h2 {
        font-size: 1.6rem;
    }

    form input, button {
        font-size: 0.9rem;
        padding: 0.7rem;
    }
}
</style>
<body>
<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
    <a href="register.php">register</a>
</form>
</body>
</html>

