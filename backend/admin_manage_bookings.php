<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Initialize variables
$error_message = '';
$success_message = '';

// Handle booking deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $delete = $conn->prepare('DELETE FROM bookings WHERE id = ?');
    $delete->bind_param('i', $delete_id);
    if ($delete->execute()) {
        $success_message = "Booking deleted successfully!";
    } else {
        $error_message = "Error deleting booking: " . mysqli_error($conn);
    }
}

// Handle booking update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_booking'])) {
    $update_id = (int) $_POST['update_id'];
    $program = trim($_POST['program']);
    $trainer = trim($_POST['trainer']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);

    // Validate inputs
    if (empty($program) || empty($trainer) || empty($location) || empty($status)) {
        $error_message = "All fields are required for updating.";
    } else {
        $update = $conn->prepare('UPDATE bookings SET program = ?, trainer = ?, location = ?, status = ? WHERE id = ?');
        $update->bind_param('ssssi', $program, $trainer, $location, $status, $update_id);
        if ($update->execute()) {
            $success_message = "Booking updated successfully!";
        } else {
            $error_message = "Error updating booking: " . mysqli_error($conn);
        }
    }
}

// Fetch all bookings
$bookings_query = "SELECT id, username, program, trainer, location, status, created_at FROM bookings ORDER BY created_at DESC";
$bookings_result = mysqli_query($conn, $bookings_query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(220, 207, 207, 1);
            color: white;
        }
        .sidebar {
            height: 100vh;
            background-color: rgba(101, 101, 223, 1);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            overflow-y: auto;
            border-right: 2px solid rgba(251, 20, 205, 1);
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
            background-color: rgba(251, 20, 186, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 186, 1);
            color: rgba(14, 14, 145, 1);
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .content h1 {
            color: rgba(26, 7, 102, 1);
        }
        .card {
            background-color: rgba(4, 4, 57, 1);
            border: 1px solid rgba(246, 15, 15, 1);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(8, 2, 36, 0.5);
        }
        .card-title {
            color: rgba(251, 20, 228, 1);
            font-weight: bold;
        }
        .table {
            color: rgba(4, 7, 62, 1);
        }
        .table th, .table td {
            border: 1px solid rgba(251, 20, 178, 1);
        }
        .table th {
            color: rgb(255, 255, 255);
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
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_profile.php">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_manage_users.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_manage_bookings.php">
                                <i class="fas fa-calendar-check"></i> Manage Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_manage_queries.php">
                                <i class="fas fa-question-circle"></i> Manage Queries
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
                    <h1 class="h2">Manage Bookings</h1>
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

                    <!-- Bookings Table -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">All Bookings</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Program</th>
                                        <th>Trainer</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($bookings_result) > 0): ?>
                                        <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['program']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['trainer']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
                                                <td>
                                                    <!-- Update Form -->
                                                    <form method="POST" action="" class="d-inline">
                                                        <input type="hidden" name="update_id" value="<?php echo $booking['id']; ?>">
                                                        <input type="text" name="program" value="<?php echo htmlspecialchars($booking['program']); ?>" required>
                                                        <input type="text" name="trainer" value="<?php echo htmlspecialchars($booking['trainer']); ?>" required>
                                                        <input type="text" name="location" value="<?php echo htmlspecialchars($booking['location']); ?>" required>
                                                        <select name="status" required>
                                                            <option value="Active" <?php echo $booking['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="Canceled" <?php echo $booking['status'] === 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                                                        </select>
                                                        <button type="submit" name="update_booking" class="btn btn-sm btn-warning">Update</button>
                                                    </form>
                                                    <!-- Delete Button -->
                                                    <a href="?delete_id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No bookings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
