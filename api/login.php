<?php
session_start();
include("connect.php");

// Initialize attempt counter if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

$mobile = trim($_POST['mobile']);
$password = trim($_POST['password']);
$role = $_POST['role'];

// Fetch user with credentials
$query = mysqli_query($connect, "SELECT * FROM user WHERE mobile='$mobile' AND password='$password' AND role='$role'");

if (mysqli_num_rows($query) > 0) {
    // Reset attempts on successful login
    $_SESSION['login_attempts'] = 0;

    $userdata = mysqli_fetch_assoc($query);
    $_SESSION['userdata'] = $userdata;

    // If group role = 2, consider admin session
    if ($role == 2) {
        $_SESSION['admin'] = true;
    }

    // Fetch groups for dashboard
    $groups = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
    $_SESSION['groupsdata'] = mysqli_fetch_all($groups, MYSQLI_ASSOC);

    echo '<script>window.location="../router/dashboard.php";</script>';

} else {
    // Increment failed attempt count
    $_SESSION['login_attempts']++;

    if ($_SESSION['login_attempts'] >= 3) {
        echo '<script>alert("⚠️ Too many failed login attempts! Please try again later."); window.location="../index.html";</script>';
        session_destroy();
    } else {
        echo '<script>alert("Invalid credentials! Attempt '.$_SESSION['login_attempts'].' of 3."); window.location="../index.html";</script>';
    }
}
?>
