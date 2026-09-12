<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "<script>alert('Both username and password are required.');</script>";
    } else {
        $stmt = $conn->prepare('SELECT username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user['username']; // Store username
                $_SESSION['role'] = $user['role']; 
            
                // Redirect based on role with an alert
                if ($user['role'] === 'member') {
                    echo "<script>
                            alert('Login successful! Redirecting to the member dashboard.');
                            window.location.href = 'member_dashboard.php';
                          </script>";
                    exit();
                } elseif ($user['role'] === 'staff') {
                    echo "<script>
                            alert('Login successful! Redirecting to the staff dashboard.');
                            window.location.href = 'staff_dashboard.php';
                          </script>";
                    exit();
                } elseif ($user['role'] === 'admin') {
                    echo "<script>
                            alert('Login successful! Redirecting to the admin dashboard.');
                            window.location.href = 'admin_dashboard.php';
                          </script>";
                    exit();
                } else {
                    echo "<script>alert('Invalid role. Please contact the administrator.');</script>";
                }
            } else {
                echo "<script>alert('Incorrect username or password.');</script>";
            }
        } else {
            echo "<script>alert('Incorrect username or password.');</script>";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: rgb(3, 2, 52);
            font-family: 'Roboto', sans-serif;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: hsla(0, 10%, 86%, 1.00);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
            width: 400px;
            text-align: center;
        }

        .login-container h1 {
            color: rgba(249, 33, 195, 1);
            margin-bottom: 20px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container input[type="text"],
        .login-container input[type="email"],
        .login-container input[type="password"] {
            background-color: rgb(42, 41, 41);
            color: white;
        }

        .login-container input[type="submit"] {
            background-color: rgba(249, 33, 191, 1);
            color: black;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container input[type="submit"]:hover {
            background-color: rgba(252, 23, 214, 1);
        }

        .login-container .icon {
            font-size: 50px;
            color: rgba(249, 33, 188, 1);
            margin-bottom: 20px;
        }

        .login-container p {
            margin-top: 10px;
            color: silver;
        }

        .login-container a {
            color: rgba(249, 33, 234, 1);
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }
        .home-link {
            color: rgba(249, 33, 242, 1);
            text-decoration: none;
            font-weight: 200;
        }

        .home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <i class="fas fa-sign-in-alt icon"></i>
        <h1>Login</h1>
        <form method="POST" action="">
            <input type="text" name="username" id="username" placeholder="Enter your username" required>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="../backend/register.php">Register here</a></p>
        <p><a href="../public/index.html" class="home-link">Go back to Home</a></p>
    </div>
</body>
</html>
