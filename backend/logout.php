<?php
session_start();

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<script>
    // Show confirmation dialog before logging out
    const confirmLogout = confirm("Are you sure you want to log out?");
    if (confirmLogout) {
        window.location.href = "logout.php?confirm=yes";
    } else {
        window.history.back(); // Go back to the previous page if canceled
    }
</script>