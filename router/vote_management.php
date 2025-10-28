<?php
session_start();
include("../api/connect.php");

if (!isset($_SESSION['admin'])) {
    header("Location: ../router/admin_login.html");
    exit;
}

// 🧮 Fetch all groups
$groups_query = mysqli_query($connect, "SELECT * FROM user WHERE role='2'");
$groups = mysqli_fetch_all($groups_query, MYSQLI_ASSOC);

// 🧠 Find top vote count and winners
$winner_votes = 0;
$winners = [];

foreach ($groups as $group) {
    if ($group['votes'] > $winner_votes) {
        $winner_votes = $group['votes'];
        $winners = [$group];
    } elseif ($group['votes'] == $winner_votes) {
        $winners[] = $group;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Management | Online Voting System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
            color: #333;
        }

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
        }

        .header-center {
            flex: 1;
            text-align: center;
        }

        .main-header h1 {
            font-size: 1.5rem;
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

        .container {
            padding: 25px;
        }

        .result-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 25px;
        }

        .result-card h2 {
            color: #007bff;
            margin-bottom: 15px;
        }

        .winner-box {
            background: #e9f7ef;
            padding: 15px;
            border-left: 5px solid #28a745;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .tie-box {
            background: #fff3cd;
            padding: 15px;
            border-left: 5px solid #ffc107;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .group-list {
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
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .group-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .group-card h3 {
            color: #007bff;
            margin: 5px 0;
        }

        .group-card p {
            margin: 0;
        }

        @media (max-width: 600px) {
            .group-list {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<header class="main-header">
    <a href="admin_dashboard.php" class="back-btn">← Back</a>
    <div class="header-center">
        <h1>Vote Management</h1>
    </div>
    <a href="../api/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
    <div class="result-card">
        <h2>🏆 Winner Announcement</h2>
        <?php
        if ($winner_votes == 0) {
            echo "<p>No votes have been cast yet.</p>";
        } elseif (count($winners) > 1) {
            echo "<div class='tie-box'><b>It's a Tie!</b> " . count($winners) . " groups with <b>$winner_votes votes</b> each.</div>";
            foreach ($winners as $tie) {
                echo "<div class='tie-box'>🤝 <b>{$tie['name']}</b></div>";
            }
        } else {
            echo "<div class='winner-box'>🏆 <b>Winner:</b> {$winners[0]['name']} with <b>$winner_votes votes!</b></div>";
        }
        ?>
    </div>

    <div class="result-card">
        <h2>📊 Vote Tally</h2>
        <div class="group-list">
            <?php foreach ($groups as $group) { ?>
                <div class="group-card">
                    <img src="../uploads/<?php echo $group['photo']; ?>" alt="Group">
                    <h3><?php echo $group['name']; ?></h3>
                    <p>Total Votes: <b><?php echo $group['votes']; ?></b></p>
                    <?php if ($group['votes'] == $winner_votes && $winner_votes > 0) {
                        echo "<p style='color:green;font-weight:bold;'>Leading 🏆</p>";
                    } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

</body>
</html>
