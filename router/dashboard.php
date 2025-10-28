<?php
session_start();
if(!isset($_SESSION['userdata'])){
    header("location: ../index.html");
    exit;
}
$userdata = $_SESSION['userdata'];
$groupsdata = $_SESSION['groupsdata'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Online Voting</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<style>
/* 🌐 Base Layout */
body {
    font-family: "Poppins", sans-serif;
    background: #f4f7fb;
    margin: 0;
    padding: 0;
    color: #333;
}

/* 🔹 Header */
.main-header {
    background: #007bff;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.header-center {
    flex: 1;
    text-align: center;
}

.main-header h1 {
    font-size: 1.4rem;
    margin: 0;
}

.back-btn, .logout-btn {
    background: #fff;
    color: #007bff;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}

.back-btn:hover, .logout-btn:hover {
    background: #0056b3;
    color: #fff;
}

/* 🔹 Dashboard Container */
.dashboard-container {
    display: grid;
    grid-template-columns: 320px 1fr;
    height: calc(100vh - 70px);
    gap: 20px;
    padding: 20px;
}

/* 🧑‍💼 Profile Section (Fixed Left) */
.profile-section {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: sticky;
    top: 100px;
    height: fit-content;
}

.profile-section h2 {
    color: #007bff;
    text-align: left;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
    margin-bottom: 15px;
}

.profile-card {
    text-align: center;
}

.profile-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}

.voted {
    color: green;
    font-weight: 600;
}

.notvoted {
    color: red;
    font-weight: 600;
}

/* 🗳️ Group Section (Right Scrollable Area) */
.group-section {
    background: transparent;
    overflow-y: auto;
    padding-right: 10px;
}

.group-section h2 {
    color: #007bff;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
    margin-bottom: 15px;
}

.groups-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.group-card {
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    width: 220px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.group-card:hover {
    transform: translateY(-3px);
}

.group-card img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.vote-btn {
    margin-top: 10px;
    padding: 8px 12px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.vote-btn:hover {
    background: #1e7e34;
}

.vote-btn[disabled] {
    background: #aaa;
    cursor: not-allowed;
}

/* 🔹 Responsive Design */
@media (max-width: 900px) {
    .dashboard-container {
        grid-template-columns: 1fr;
        height: auto;
    }
    .profile-section {
        position: relative;
        top: 0;
        width: 100%;
    }
    .group-section {
        overflow: visible;
        width: 100%;
    }
}
</style>

<!-- 🔝 Header -->
<header class="main-header">
    <a href="javascript:history.back()" class="back-btn">← Back</a>
    <div class="header-center">
        <h1>Online Voting System</h1>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<!-- 🧭 Dashboard -->
<div class="dashboard-container">
    <!-- 🧑‍💼 Left: Profile -->
    <section class="profile-section">
        <center><h2>Profile</h2></center>
        <div class="profile-card">
            <img src="../uploads/<?php echo $userdata['photo']; ?>" alt="Profile">
            <h3><?php echo $userdata['name']; ?></h3>
            <p><b>Mobile:</b> <?php echo $userdata['mobile']; ?></p>
            <p><b>Address:</b> <?php echo $userdata['address']; ?></p>
            <p><b>Status:</b>
                <?php echo ($userdata['status']==0) ? "<span class='notvoted'>Not Voted</span>" : "<span class='voted'>Voted</span>"; ?>
            </p>
        </div>
    </section>

    <!-- 🗳️ Right: Groups -->
    <section class="group-section">
        <h2>Available Groups</h2>
        <div class="groups-container">
            <?php foreach($groupsdata as $group){ ?>
            <div class="group-card">
                <img src="../uploads/<?php echo $group['photo']; ?>" alt="Group">
                <h3><?php echo $group['name']; ?></h3>
                <p>Total Votes: <?php echo $group['votes']; ?></p>

                <?php if($userdata['status']==0){ ?>
                <form action="../api/votes.php" method="POST">
                    <input type="hidden" name="gvotes" value="<?php echo $group['votes']; ?>">
                    <input type="hidden" name="gid" value="<?php echo $group['id']; ?>">
                    <button type="submit" class="vote-btn">Vote</button>
                </form>
                <?php } else { ?>
                <button class="vote-btn" disabled>Voted ✅</button>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </section>
</div>

</body>
</html>
