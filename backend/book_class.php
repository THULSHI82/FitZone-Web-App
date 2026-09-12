<?php

session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Initialize variables
$error_message = '';
$success_message = '';

// Handle form submission for booking a class
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $program = $_POST['program'];
    $plan = $_POST['plan'];
    $trainer = $_POST['trainer'];
    $location = $_POST['location'];

    // Validate inputs
    if (empty($program) || empty($plan) || empty($trainer) || empty($location)) {
        $error_message = "All fields are required.";
    } else {
        // Insert booking into the database
        $username = $_SESSION['user'];
        $insert = $conn->prepare('INSERT INTO bookings (username, program, plan, trainer, location) VALUES (?, ?, ?, ?, ?)');
        $insert->bind_param('sssss', $username, $program, $plan, $trainer, $location);
        if ($insert->execute()) {
            $success_message = "Class booked successfully!";
        } else {
            $error_message = "Error booking class: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Class</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(199, 184, 184, 1);
            color: white;
        }
        .sidebar {
            height: 100vh;
            background-color:  rgba(101, 101, 223, 1);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            overflow-y: auto;
            border-right: 2px solid rgba(251, 20, 178, 1);
        }
        .sidebar h3 {
            color: rgba(251, 20, 182, 1);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: rgba(251, 20, 216, 1);
            color: rgba(14, 1, 10, 1);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 209, 1);
            color: rgb(1, 1, 14);
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .content h1 {
            color: rgb(0, 0, 0);
        }
        .content h3 {
            color: rgb(0, 0, 0);
        }

        .card {
            background-color: rgba(4, 4, 54, 1);
            border: 1px solid rgba(5, 29, 208, 1);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(12, 10, 74, 1);
        }
        .card-title {
            color: rgba(251, 20, 209, 1);
            font-weight: bold;
        }
        .form-label {
            color: rgba(251, 20, 209, 1);
        }
        .btn-primary {
            background-color: rgba(251, 20, 236, 1);
            border: none;
            color: rgba(4, 4, 51, 1);
        }
        .btn-primary:hover {
            background-color: rgba(200, 15, 163, 1);
        }
        .plan-card, .trainer-card {
            background-color: rgba(11, 6, 66, 1);
            border: 1px solid rgba(86, 5, 208, 1);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }
        .trainer-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                    <h3 class="text-center py-3">Fitzone</h3>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="member_dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="submit_query.php">
                                <i class="fas fa-question-circle"></i> Submit Query
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="all_bookings.php">
                                <i class="fas fa-book"></i> All Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="book_class.php">
                                <i class="fas fa-calendar-check"></i> Book a Class
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Book a Class</h1>
                </div>
                <div class="container mt-5">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert" id="error-message">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert" id="success-message">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Plans Section -->
                    <h3>Our Plans</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="plan-card">
                                <h5>Silver</h5>
                                <p>One Month Rs.10,000.00/=</p>
                                <p>No Personal Trainer</p>
                                <p>Valid for 30 Days</p>
                                <p>Includes: Weight Lifting, Cardio, Yoga</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="plan-card">
                                <h5>Gold</h5>
                                <p>One Month Rs.15,000.00/=</p>
                                <p>Personal Trainer</p>
                                <p>Valid for 30 Days</p>
                                <p>Includes: Weight Lifting, Cardio, Yoga</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="plan-card">
                                <h5>Platinum</h5>
                                <p>One Month Rs.20,000.00/=</p>
                                <p>Personal Trainer with Pre-Workout</p>
                                <p>Valid for 30 Days</p>
                                <p>Includes: Weight Lifting, Cardio, Yoga</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trainers Section -->
                    <h3>Our Trainers</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="trainer-card text-center">
                                <img src="../public/assets/images/trainer-1.svg" alt="Anatoly">
                                <h5>Antoly</h5>
                                <p>Weight Lifting</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="trainer-card text-center">
                                <img src="../public/assets/images/trainer-2.svg" alt="Jane Smith">
                                <h5>Jane Smith</h5>
                                <p>Yoga</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="trainer-card text-center">
                                <img src="../public/assets/images/trainer-3.svg" alt="Michael Lee">
                                <h5>Michael Lee</h5>
                                <p>Cardio</p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <h3>Book Your Class</h3>
                    <div class="card p-4">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="program" class="form-label">Our Programs</label>
                                <select class="form-select" id="program" name="program" required>
                                    <option value="">Select a program</option>
                                    <option value="Yoga">Yoga</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Weight Lifting">Weight Lifting</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="plan" class="form-label">Our Plans</label>
                                <select class="form-select" id="plan" name="plan" required>
                                    <option value="">Select a plan</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Platinum">Platinum</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="trainer" class="form-label">Our Trainers</label>
                                <select class="form-select" id="trainer" name="trainer" required>
                                    <option value="">Select a trainer</option>
                                    <option value="Antoly">Antoly</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <option value="Michael Lee">Michael Lee</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Gym Locations</label>
                                <select class="form-select" id="location" name="location" required>
                                    <option value="">Select a location</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Negombo">Negombo</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Book Class</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Automatically hide success and error messages after 5 seconds
        setTimeout(() => {
            const errorMessage = document.getElementById('error-message');
            const successMessage = document.getElementById('success-message');
            if (errorMessage) errorMessage.style.display = 'none';
            if (successMessage) successMessage.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
