<?php
session_start();
include("connect.php");

$username = $_POST['username'];
$password = $_POST['password'];

// Hardcoded admin credentials (you can store in DB if preferred)
$admin_user = "admin";
$admin_pass = "123";

if ($username == $admin_user && $password == $admin_pass) {
    $_SESSION['admin'] = true;
    header("Location: ../router/admin_dashboard.php");
} else {
    echo "<script>
        alert('Invalid admin credentials!');
        window.location = '../router/admin_login.html';
    </script>";
}
?>
