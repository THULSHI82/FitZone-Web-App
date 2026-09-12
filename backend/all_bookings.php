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
$username = $_SESSION['user'];

// Handle form submission for updating or canceling a booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_booking'])) {
        $booking_id = (int) $_POST['booking_id'];
        $program = $_POST['program'];
        $trainer = $_POST['trainer'];
        $location = $_POST['location'];

        // Update booking in the database
        $update = $conn->prepare("UPDATE bookings SET program = ?, trainer = ?, location = ?, status = 'Active' WHERE id = ? AND username = ?");
        $update->bind_param('sssis', $program, $trainer, $location, $booking_id, $username);
        if ($update->execute()) {
            $success_message = "Booking updated successfully!";
        } else {
            $error_message = "Error updating booking: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['cancel_booking'])) {
        $booking_id = (int) $_POST['booking_id'];

        // Mark booking as canceled
        $cancel = $conn->prepare("UPDATE bookings SET status = 'Canceled' WHERE id = ? AND username = ?");
        $cancel->bind_param('is', $booking_id, $username);
        if ($cancel->execute()) {
            $success_message = "Booking canceled successfully!";
        } else {
            $error_message = "Error canceling booking: " . mysqli_error($conn);
        }
    }
}

// Fetch all bookings for the logged-in user
$bookings = $conn->prepare('SELECT * FROM bookings WHERE username = ?');
$bookings->bind_param('s', $username);
$bookings->execute();
$bookings_result = $bookings->get_result();

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(211, 199, 199, 1);
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
            border-right: 2px solid rgba(251, 20, 236, 1);
        }
        .sidebar h3 {
            color: rgba(251, 20, 236, 1);
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
            background-color: rgba(251, 20, 228, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 251, 1);
            color: rgb(1, 1, 14);
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .content h1 {
            color: rgb(0, 0, 0);
        }
        .table-container {
            margin-top: 20px;
            padding: 20px;
            background-color: rgba(12, 4, 53, 1);
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        }

        .card {
            background-color: rgba(10, 10, 90, 1);
            border: 1px solid rgba(251, 20, 228, 1);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }
        .card-title {
            color: rgba(239, 20, 251, 1);
            font-weight: bold;
        }
        .table {
            color: white;
        }
        .table th, .table td {
            border: 1px solid rgba(251, 20, 209, 1);
        }
        .table th {
            color: rgb(255, 255, 255);
        }
        .btn-success {
            background-color: rgba(251, 20, 197, 1);
            border: none;
            color: rgb(1, 1, 14);
        }
        .btn-success:hover {
            background-color: rgba(200, 15, 188, 1);
        }
        .btn-danger {
            background-color: rgba(0, 55, 255, 1);
            border: none;
        }
        .btn-danger:hover {
            background-color: rgba(67, 0, 200, 1);
        }
        .form-select:disabled {
            background-color: rgb(1, 1, 14);
            color: red;
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
                            <a class="nav-link active" href="all_bookings.php">
                                <i class="fas fa-book"></i> All Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="book_class.php">
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
                    <h1 class="h2">All Bookings</h1>
                </div>
                <div class="container table-container">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Plan</th>
                                <th>Trainer</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($bookings_result) > 0): ?>
                                <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                                    <tr>
                                        <form method="POST" action="">
                                            <td>
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <select class="form-select" name="program" <?php echo ($booking['status'] === 'Canceled') ? 'disabled' : ''; ?> required>
                                                    <option value="Yoga" <?php if ($booking['program'] == 'Yoga') echo 'selected'; ?>>Yoga</option>
                                                    <option value="Cardio" <?php if ($booking['program'] == 'Cardio') echo 'selected'; ?>>Cardio</option>
                                                    <option value="Weight Lifting" <?php if ($booking['program'] == 'Weight Lifting') echo 'selected'; ?>>Weight Lifting</option>
                                                </select>
                                            </td>
                                            <td><?php echo htmlspecialchars($booking['plan']); ?></td>
                                            <td>
                                                <select class="form-select" name="trainer" <?php echo ($booking['status'] === 'Canceled') ? 'disabled' : ''; ?> required>
                                                    <option value="Antoly" <?php if ($booking['trainer'] == 'Antoly') echo 'selected'; ?>>Antoly</option>
                                                    <option value="Jane Smith" <?php if ($booking['trainer'] == 'Jane Smith') echo 'selected'; ?>>Jane Smith</option>
                                                    <option value="Michael Lee" <?php if ($booking['trainer'] == 'Michael Lee') echo 'selected'; ?>>Michael Lee</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select" name="location" <?php echo ($booking['status'] === 'Canceled') ? 'disabled' : ''; ?> required>
                                                    <option value="Colombo" <?php if ($booking['location'] == 'Colombo') echo 'selected'; ?>>Colombo</option>
                                                    <option value="Kandy" <?php if ($booking['location'] == 'Kandy') echo 'selected'; ?>>Kandy</option>
                                                    <option value="Galle" <?php if ($booking['location'] == 'Galle') echo 'selected'; ?>>Galle</option>
                                                    <option value="Negombo" <?php if ($booking['location'] == 'Negombo') echo 'selected'; ?>>Negombo</option>
                                                </select>
                                            </td>
                                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                            <td>
                                                <button type="submit" name="update_booking" class="btn btn-success btn-sm" <?php echo ($booking['status'] === 'Canceled') ? 'disabled' : ''; ?>>Save</button>
                                                <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm" <?php echo ($booking['status'] === 'Canceled') ? 'disabled' : ''; ?>>Cancel</button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No bookings found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Automatically hide success and error messages after 5 seconds
        setTimeout(() => {
            const errorMessage = document.querySelector('.alert-danger');
            const successMessage = document.querySelector('.alert-success');
            if (errorMessage) errorMessage.remove();
            if (successMessage) successMessage.remove();
        }, 5000);
    </script>
</body>
</html>
