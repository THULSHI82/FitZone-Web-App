<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Initialize variables
$error_message = '';
$success_message = '';

// Handle form submission for adding a new booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $program = $_POST['program'];
    $plan = $_POST['plan'];
    $trainer = $_POST['trainer'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Validate inputs
    if (empty($username) || empty($program) || empty($plan) || empty($trainer) || empty($location) || empty($status)) {
        $error_message = "All fields are required.";
    } else {
        // Insert new booking into the database
        $insert = $conn->prepare('INSERT INTO bookings (username, program, plan, trainer, location, status) VALUES (?, ?, ?, ?, ?, ?)');
        $insert->bind_param('ssssss', $username, $program, $plan, $trainer, $location, $status);
        if ($insert->execute()) {
            $success_message = "Booking added successfully!";
        } else {
            $error_message = "Error adding booking: " . mysqli_error($conn);
        }
    }
}

// Fetch all bookings
$bookings_sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$bookings_result = mysqli_query($conn, $bookings_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Add Bookings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(207, 195, 195, 1);
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
            border-right: 2px solid rgba(251, 20, 197, 1);
        }
        .sidebar h3 {
            color: rgb(255, 255, 255);
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
            background-color: rgba(251, 20, 182, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 186, 1);
            color: rgb(1, 1, 14);
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .content h1 {
            color: rgb(0, 0, 0);
        }
        .card {
            background-color: rgba(4, 4, 55, 1);
            border: 1px solid rgba(5, 59, 208, 1);
            color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }
        .card-title {
            color: rgb(255, 255, 255);
            font-weight: bold;
        }
        .table {
            color: white;
        }
        .table th, .table td {
            border: 1px solid rgba(251, 20, 201, 1);
        }
        .table th {
            color: rgb(255, 255, 255);
        }
        .btn-primary {
            background-color: rgba(243, 20, 251, 1);
            border: none;
            color: rgb(1, 1, 14);
        }
        .btn-primary:hover {
            background-color: rgba(200, 15, 188, 1);
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
                            <a class="nav-link" href="staff_dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="staff_profile.php">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_queries.php">
                                <i class="fas fa-question-circle"></i> Manage Queries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_bookings.php">
                                <i class="fas fa-calendar-check"></i> Manage Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_add_classes.php">
                                <i class="fas fa-plus-circle"></i> Add Bookings
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
                    <h1 class="h2">Manage Add Bookings</h1>
                </div>
                <div class="container mt-5">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" id="error-message" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success" id="success-message" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                    <!-- Add Booking Form -->
                    <div class="card mb-4">
                        <h5 class="card-title">Add New Booking</h5>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="program" class="form-label">Program</label>
                                <select class="form-select" id="program" name="program" required>
                                    <option value="">Select a program</option>
                                    <option value="Yoga">Yoga</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Weight Lifting">Weight Lifting</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="plan" class="form-label">Plan</label>
                                <select class="form-select" id="plan" name="plan" required>
                                    <option value="">Select a plan</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Platinum">Platinum</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="trainer" class="form-label">Trainer</label>
                                <select class="form-select" id="trainer" name="trainer" required>
                                    <option value="">Select a trainer</option>
                                    <option value="Antoly">Antoly</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <option value="Michael Lee">Michael Lee</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <select class="form-select" id="location" name="location" required>
                                    <option value="">Select a location</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Negombo">Negombo</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Canceled">Canceled</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Booking</button>
                        </form>
                    </div>

                    <!-- Bookings Table -->
                    <div class="card">
                        <h5 class="card-title">All Bookings</h5>
                        <table class="table table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Program</th>
                                    <th>Plan</th>
                                    <th>Trainer</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($bookings_result) > 0): ?>
                                    <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                                        <tr>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['username']); ?></td>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['program']); ?></td>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['plan']); ?></td>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['trainer']); ?></td>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['location']); ?></td>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['status']); ?></td>
                                            <td style="color: white;"><?php echo htmlspecialchars($booking['created_at']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center" style="color: white;">No bookings found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
<?php
// Close the database connection at the end
mysqli_close($conn);
?>
