<?php
session_start();
require 'config.php';

if (!isset($conn)) {
    die("Database connection error!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "Username and password are required!";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid username or password!";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap">
</head>
<body>
    <?php include('menu.php'); ?>
    <h1>Login</h1>
    <form method="POST">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
    <footer>
        <div class="footer-content bg-primary">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const loginButton = document.querySelector("button[type='submit']");
        const username = document.getElementById("username");
        const password = document.getElementById("password");

        function isEmpty() {
            return username.value.trim() === "" || password.value.trim() === "";
        }

        function moveButton() {
            const maxX = Math.max(window.innerWidth - loginButton.offsetWidth - 20, 0);
            const maxY = Math.max(window.innerHeight - loginButton.offsetHeight - 20, 0);
            
            const randomX = Math.random() * maxX;
            const randomY = Math.random() * maxY;
            
            loginButton.style.position = "absolute";
            loginButton.style.left = `${randomX}px`;
            loginButton.style.top = `${randomY}px`;
        }

        loginButton.addEventListener("mouseover", function () {
            if (isEmpty()) {
                moveButton();
            }
        });

        username.addEventListener("input", function () {
            if (!isEmpty()) {
                loginButton.style.position = "static";
            }
        });

        password.addEventListener("input", function () {
            if (!isEmpty()) {
                loginButton.style.position = "static";
            }
        });
    });
    </script>
</body>
</html>
