<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Fetch user details
$username = $_SESSION['user'];
$user_query = $conn->prepare('SELECT name FROM users WHERE username = ?');
$user_query->bind_param('s', $username);
$user_query->execute();
$user_result = $user_query->get_result();
$user = mysqli_fetch_assoc($user_result);

// Fetch upcoming classes
$classes_query = $conn->prepare("SELECT program, trainer, location, created_at FROM bookings WHERE username = ? AND status = 'Active' ORDER BY created_at ASC LIMIT 3");
$classes_query->bind_param('s', $username);
$classes_query->execute();
$classes_result = $classes_query->get_result();

// Fetch membership status
$membership_query = $conn->prepare("SELECT plan, status FROM bookings WHERE username = ? AND status = 'Active' ORDER BY created_at DESC LIMIT 1");
$membership_query->bind_param('s', $username);
$membership_query->execute();
$membership_result = $membership_query->get_result();
$membership = mysqli_fetch_assoc($membership_result);

// Fetch progress data
$progress_query = $conn->prepare("SELECT COUNT(*) AS total_classes, COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS completed_classes FROM bookings WHERE username = ?");
$progress_query->bind_param('s', $username);
$progress_query->execute();
$progress_result = $progress_query->get_result();
$progress = mysqli_fetch_assoc($progress_result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(210, 194, 194, 1);
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
            border-right: 2px solid rgba(251, 20, 236, 1);
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
            background-color: rgba(251, 20, 216, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 247, 1);
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
            background-color: rgba(6, 6, 63, 1);
            border: 1px solid rgba(5, 46, 208, 1);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }
        .card-title {
            color: rgba(251, 20, 236, 1);
            font-weight: bold;
        }
        .progress-bar {
            background-color: rgb(6, 15, 250);
        }
        .icon {
            font-size: 50px;
            color: rgb(255, 255, 255);
            margin-right: 10px;
        }
        .list-group-item {
            background-color: rgb(1, 1, 14);
            color: white;
            border: 1px solid rgba(5, 59, 208, 1);
        }
        .list-group-item i {
            color: rgba(251, 20, 228, 1);
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
                            <a class="nav-link active" href="member_dashboard.php">
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
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                </div>

                <!-- Gym Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-dumbbell icon"></i> About FitZone</h5>
                                <p class="card-text">FitZone is your ultimate fitness destination, offering state-of-the-art facilities, expert trainers, and a variety of programs to help you achieve your fitness goals. Join us and be part of the FitZone community!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership and Progress -->
                <div class="row">
                    <!-- Membership Status -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-id-card icon"></i> Membership Status</h5>
                                <p>Plan: <strong><?php echo htmlspecialchars($membership['plan'] ?? 'N/A'); ?></strong></p>
                                <p>Status: <strong><?php echo htmlspecialchars($membership['status'] ?? 'Inactive'); ?></strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Your Progress -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-chart-line icon"></i> Your Progress</h5>
                                <p>Total Classes: <strong><?php echo $progress['total_classes'] ?? 0; ?></strong></p>
                                <p>Completed Classes: <strong><?php echo $progress['completed_classes'] ?? 0; ?></strong></p>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo ($progress['completed_classes'] / max(1, $progress['total_classes'])) * 100; ?>%;" aria-valuenow="<?php echo $progress['completed_classes']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $progress['total_classes']; ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Classes -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calendar-alt icon"></i> Upcoming Classes</h5>
                                <?php if (mysqli_num_rows($classes_result) > 0): ?>
                                    <ul class="list-group">
                                        <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-running"></i> <?php echo htmlspecialchars($class['program']); ?> with <?php echo htmlspecialchars($class['trainer']); ?> at <?php echo htmlspecialchars($class['location']); ?> on <?php echo htmlspecialchars(date('F j, Y', strtotime($class['created_at']))); ?>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="card-text">No upcoming classes.</p>
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
