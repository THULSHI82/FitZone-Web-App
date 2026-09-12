<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Fetch admin details
$username = $_SESSION['user'];
$admin_query = $conn->prepare('SELECT name FROM users WHERE username = ?');
$admin_query->bind_param('s', $username);
$admin_query->execute();
$admin_result = $admin_query->get_result();
$admin = mysqli_fetch_assoc($admin_result);

// Fetch recent users
$recent_users_query = "SELECT username, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = mysqli_query($conn, $recent_users_query);

// Fetch recent bookings
$recent_bookings_query = "SELECT username, program, trainer, location, created_at FROM bookings ORDER BY created_at DESC LIMIT 5";
$recent_bookings_result = mysqli_query($conn, $recent_bookings_query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(214, 199, 199, 1);
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
            background-color: rgba(251, 20, 205, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 216, 1);
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
            background-color: rgba(4, 7, 62, 1);
            border: 1px solid rgb(208, 100, 5);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }
        .card-title {
            color: rgba(251, 20, 182, 1);
            font-weight: bold;
        }
        .table {
            color: white;
        }
        .table th, .table td {
            border: 1px solid rgba(251, 20, 189, 1);
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
                            <a class="nav-link active" href="admin_dashboard.php">
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
                            <a class="nav-link" href="admin_manage_bookings.php">
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
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>
                </div>

                <!-- Recent Users -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-users"></i> Recent Users</h5>
                                <?php if (mysqli_num_rows($recent_users_result) > 0): ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th>Created On</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($user = mysqli_fetch_assoc($recent_users_result)): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>No recent users found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calendar-alt"></i> Recent Bookings</h5>
                                <?php if (mysqli_num_rows($recent_bookings_result) > 0): ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Program</th>
                                                <th>Trainer</th>
                                                <th>Location</th>
                                                <th>Booked On</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($booking = mysqli_fetch_assoc($recent_bookings_result)): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['program']); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['trainer']); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>No recent bookings found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
