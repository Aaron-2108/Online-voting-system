<?php
session_start();
include("connect.php");

if (!isset($_SESSION['userdata'])) {
    header("location: ../");
    exit;
}

$gid = $_POST['gid'];
$votes = $_POST['gvotes'];
$total_votes = $votes + 1;
$uid = $_SESSION['userdata']['id'];

// ✅ Check if user already voted
$check_status = mysqli_query($connect, "SELECT status FROM user WHERE id='$uid'");
$user_data = mysqli_fetch_assoc($check_status);

if ($user_data['status'] == 1) {
    echo '<script>alert("⚠️ You have already voted! You cannot vote again."); window.location="../router/dashboard.php";</script>';
    exit;
}

// ✅ Update group vote count
$update_votes = mysqli_query($connect, "UPDATE user SET votes='$total_votes' WHERE id='$gid'");

// ✅ Update user voting status
$update_user_status = mysqli_query($connect, "UPDATE user SET status=1 WHERE id='$uid'");

if ($update_votes && $update_user_status) {
    // Update session so status changes immediately on dashboard
    $_SESSION['userdata']['status'] = 1;
    echo '<script>alert("✅ Your vote has been recorded successfully!"); window.location="../router/dashboard.php";</script>';
} else {
    echo '<script>alert("❌ Error recording your vote. Please try again."); window.location="../router/dashboard.php";</script>';
}
?>
