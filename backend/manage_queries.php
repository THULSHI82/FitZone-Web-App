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

// Handle form submission for replying to a query
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reply_query'])) {
        $query_id = (int) $_POST['query_id'];
        $reply = trim($_POST['reply']);
        $status = $_POST['status'];

        // Validate inputs
        if (empty($reply)) {
            $error_message = "Reply cannot be empty.";
        } else {
            // Update the query with the reply and status
            $update = $conn->prepare('UPDATE queries SET reply = ?, status = ? WHERE id = ?');
            $update->bind_param('ssi', $reply, $status, $query_id);
            if ($update->execute()) {
                $success_message = "Query updated successfully!";
            } else {
                $error_message = "Error updating query: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch all queries
$queries_sql = "SELECT * FROM queries ORDER BY created_at DESC";
$queries_result = mysqli_query($conn, $queries_sql);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Queries</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(209, 195, 195, 1);
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
            border-right: 2px solid rgba(251, 20, 220, 1);
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
            background-color: rgba(251, 20, 220, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 205, 1);
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
            background-color: rgba(13, 5, 62, 1);
            border: 1px solid rgba(5, 22, 208, 1);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            padding: 20px;
        }
        .card-title {
            color: rgb(255, 255, 255);
            font-weight: bold;
        }
        .table {
            color: white;
        }
        .table th, .table td {
            border: 1px solid rgba(251, 20, 239, 1);
        }
        
        .table th {
            color: rgb(255, 255, 255);
        }
        .btn-primary {
            background-color: rgba(251, 20, 220, 1);
            border: none;
            color: rgb(1, 1, 14);
        }
        .btn-primary:hover {
            background-color: rgba(200, 15, 197, 1);
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
                            <a class="nav-link active" href="manage_queries.php">
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
                    <h1 class="h2">Manage Queries</h1>
                </div>
                <div class="container mt-5">
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

                    <!-- Card Wrapper -->
                    <div class="card">
                        <h5 class="card-title">All Queries</h5>
                        <table class="table table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Reply</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($queries_result) > 0): ?>
                                    <?php while ($query = mysqli_fetch_assoc($queries_result)): ?>
                                        <tr>
                                            <form method="POST" action="">
                                                <td style="color: white;"><?php echo htmlspecialchars($query['username']); ?></td>
                                                <td style="color: white;"><?php echo htmlspecialchars($query['subject']); ?></td>
                                                <td style="color: white;"><?php echo htmlspecialchars($query['message']); ?></td>
                                                <td>
                                                    <textarea class="form-control" name="reply" rows="2"><?php echo htmlspecialchars($query['reply'] ?? ''); ?></textarea>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="status">
                                                        <option value="Pending" <?php if ($query['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                                        <option value="Resolved" <?php if ($query['status'] === 'Resolved') echo 'selected'; ?>>Resolved</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="query_id" value="<?php echo $query['id']; ?>">
                                                    <button type="submit" name="reply_query" class="btn btn-primary btn-sm">Update</button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No queries found.</td>
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
            const errorMessage = document.querySelector('.alert-danger');
            const successMessage = document.querySelector('.alert-success');
            if (errorMessage) errorMessage.style.display = 'none';
            if (successMessage) successMessage.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
