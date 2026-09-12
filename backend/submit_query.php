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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate inputs
    if (empty($subject) || empty($message)) {
        $error_message = "Both subject and message are required.";
    } else {
        // Insert query into the database
        $insert = $conn->prepare("INSERT INTO queries (username, subject, message, status) VALUES (?, ?, ?, 'Pending')");
        $insert->bind_param('sss', $username, $subject, $message);
        if ($insert->execute()) {
            $success_message = "Your query has been submitted successfully!";
        } else {
            $error_message = "Error submitting query: " . mysqli_error($conn);
        }
    }
}

// Fetch all queries submitted by the user
$queries = $conn->prepare('SELECT * FROM queries WHERE username = ? ORDER BY created_at DESC');
$queries->bind_param('s', $username);
$queries->execute();
$queries_result = $queries->get_result();

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Query</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(206, 197, 197, 1);
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
            border-right: 2px solid rgba(251, 20, 228, 1);
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
            background-color: rgba(251, 20, 201, 1);
            color: rgb(1, 1, 14);
        }
        .sidebar a.active {
            background-color: rgba(251, 20, 201, 1);
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
            background-color: rgba(6, 6, 61, 1);
            border: 1px solid rgba(8, 5, 208, 1);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }
        .card-title {
            color: rgba(251, 20, 247, 1);
            font-weight: bold;
        }
        .form-label {
            color: rgba(251, 20, 193, 1);
        }
        .btn-primary {
            background-color: rgba(251, 20, 216, 1);
            border: none;
            color: rgb(1, 1, 14);
        }
        .btn-primary:hover {
            background-color: rgba(200, 15, 141, 1);
        }
        .table {
            color: white;
        }
        .table th, .table td {
            border: 1px solid rgb(0, 0, 0);
        }
        .table th {
            color: rgb(1, 1, 14);
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
                            <a class="nav-link active" href="submit_query.php">
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
                    <h1 class="h2">Submit a Query</h1>
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
                    <div class="card p-4">
                        <h5 class="card-title">Submit Your Query</h5>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Query</button>
                        </form>
                    </div>

                    <!-- Display Submitted Queries -->
                    <h3 class="mt-5">Your Queries</h3>
                    <table class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Reply</th>
                                <th>Status</th>
                                <th>Submitted On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($queries_result) > 0): ?>
                                <?php while ($query = mysqli_fetch_assoc($queries_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($query['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($query['message']); ?></td>
                                        <td><?php echo htmlspecialchars($query['reply'] ?? 'No reply yet'); ?></td>
                                        <td><?php echo htmlspecialchars($query['status']); ?></td>
                                        <td><?php echo htmlspecialchars($query['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No queries submitted yet.</td>
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
