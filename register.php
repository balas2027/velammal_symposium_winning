<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $profession = $_POST['profession'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Error: Username already exists. <a href='register.php'>Try again</a>";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, dob, profession, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $dob, $profession, $username, $password);

        if ($stmt->execute()) {
            echo "<alert>Registration successful.</alert>" ;
        } else {
            echo "Error: " . $conn->error;
        }
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
    font-family: 'Roboto', sans-serif;
}

body {
    background: linear-gradient(120deg, #a1c4fd, #c2e9fb);
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
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    animation: fadeIn 1s ease-in-out;
}

h2 {
    text-align: center;
    margin-bottom: 1rem;
    font-size: 1.8rem;
    color: #444;
}

form input {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

form input:focus {
    border-color: #6a82fb;
    box-shadow: 0 0 5px rgba(106, 130, 251, 0.6);
    outline: none;
}

button {
    width: 100%;
    padding: 0.8rem;
    background: linear-gradient(120deg, #6a82fb, #fc5c7d);
    color: #fff;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: linear-gradient(120deg, #fc5c7d, #6a82fb);
}

a {
    display: block;
    text-align: center;
    margin-top: 1rem;
    color: #6a82fb;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: #fc5c7d;
}

/* Add subtle animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Media Query for responsiveness */
@media (max-width: 768px) {
    form {
        padding: 1.5rem;
    }

    h2 {
        font-size: 1.5rem;
    }

    form input, button {
        font-size: 0.9rem;
        padding: 0.7rem;
    }
}
</style>
<body>
    

<form method="post">
    Name: <input type="text" name="name" required><br>
    D.O.B: <input type="date" name="dob" required><br>
    Profession: <input type="text" name="profession" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
    <a href="login.php">Already have an account? Login here</a>
</form>
<div>

</div>


</body>
</html>
