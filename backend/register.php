
<?php
// Start session
session_start();

include 'connect.php'; // Adjust the path as needed

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($username) || empty($password)) {
        echo "<script>alert('All fields are required. Please fill out the form completely.');</script>";
    } elseif (strlen($username) < 3) {
        echo "<script>alert('Username must be at least 3 characters long.');</script>";
    } elseif (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.');</script>";
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $check->bind_param('s', $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Username already registered. Please use a different one.');</script>";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'member';
            $insert = $conn->prepare('INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)');
            $insert->bind_param('ssss', $name, $username, $passwordHash, $role);
            if ($insert->execute()) {
                echo "<script>alert('Registration successful! Please log in.'); window.location.href = 'login.php';</script>";
            } else {
                echo "<script>alert('Error occurred during registration. Please try again later.');</script>";
            }
            $insert->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: rgba(4, 4, 64, 1);
            font-family: 'Roboto', sans-serif;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: rgba(221, 211, 211, 1);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(223, 210, 210, 0.5);
            width: 400px;
            text-align: center;
        }

        .register-container h1 {
            color: rgba(249, 33, 195, 1);
            margin-bottom: 20px;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
        }

        .register-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            background-color: rgb(42, 41, 41);
            color: white;
        }

        .register-container input[type="submit"] {
            background-color: rgba(249, 33, 191, 1);
            color: black;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .register-container input[type="submit"]:hover {
            background-color: rgba(252, 23, 214, 1);
        }

        .register-container .icon {
            font-size: 50px;
            color: rgba(249, 33, 188, 1);
            margin-bottom: 20px;
        }

        .register-container p {
            margin-top: 10px;
            color: silver;
        }

        .register-container a {
            color: rgba(249, 33, 234, 1);
            text-decoration: none;
        }

        .register-container a:hover {
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
    <div class="register-container">
        <i class="fas fa-user-plus icon"></i>
        <h1>Register</h1>
        <form method="POST" action="">
            <input type="text" name="name" id="name" placeholder="Enter your name" required>
            <input type="text" name="username" id="username" placeholder="Enter your username" required>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="../backend/login.php">Login here</a></p>
        <p><a href="../public/index.html" class="home-link">Go back to Home</a></p>
    </div>
</body>
</html>
