<?php
session_start();
include("../api/connect.php");

// Check admin session
if (!isset($_SESSION['admin'])) {
    header("Location: ../router/admin_login.html");
    exit;
}


// 🔹 Handle Reset Votes
if (isset($_POST['reset_votes'])) {
    mysqli_query($connect, "UPDATE user SET status=0 WHERE role=1"); // Reset voter status
    mysqli_query($connect, "UPDATE user SET votes=0 WHERE role=2"); // Reset group votes
    $_SESSION['groupsdata'] = null; // optional
    header("Location: admin_dashboard.php");
    exit;
}


// 🧮 Fetch voter and group data
$voters_query = mysqli_query($connect, "SELECT * FROM user WHERE role='1'");
$total_voters = mysqli_num_rows($voters_query);

$voted_query = mysqli_query($connect, "SELECT * FROM user WHERE role='1' AND status='1'");
$total_voted = mysqli_num_rows($voted_query);

$not_voted = $total_voters - $total_voted;

$groups_query = mysqli_query($connect, "SELECT * FROM user WHERE role='2'");
$groups = mysqli_fetch_all($groups_query, MYSQLI_ASSOC);

// 🔹 Calculate winner(s)
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
    <title>Admin Dashboard | Online Voting</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f4f7fb;
            margin: 0;
            color: #333;
        }

        .main-header {
            background: #007bff;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .main-header h1 {
            color: #fff;
            font-size: 1.5rem;
            margin: 0;
        }

        .back-btn,
        .logout-btn {
            background: #fff;
            color: #007bff;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            min-width: 80px;
            text-align: center;
        }

        .back-btn:hover,
        .logout-btn:hover {
            background: #0056b3;
            color: #fff;
        }

        .dashboard-container {
            padding: 20px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            min-width: 220px;
            text-align: center;
        }

        .stat-card h2 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .stats-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .group-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .group-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .dataTables_wrapper {
            padding: 0 15px;
        }

        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            padding: 0 20px;
        }

        .dataTables_filter input {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 6px 10px;
            outline: none;
        }

        @media (max-width:900px) {
            .stats-section {
                flex-direction: column;
            }

            .groups-container {
                justify-content: center;
            }
        }

        .reset-btn {
            background: red;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <header class="main-header">
        <a href="javascript:history.back()" class="back-btn">← Back</a>
        <h1>👑 Admin Dashboard</h1>
        <a href="../router/logout.php" class="logout-btn">Logout</a>
    </header>

    <div class="dashboard-container">

        <!-- 🔹 1. Voter Statistics -->
        <div class="stats-section">
            <div class="stat-card">
                <h2>Total Voters</h2><b style="font-size:25px;"><?php echo $total_voters; ?></b>
            </div>
            <div class="stat-card">
                <h2>Voted</h2><b style="color:green;font-size:25px;"><?php echo $total_voted; ?></b>
            </div>
            <div class="stat-card">
                <h2>Not Voted</h2><b style="color:red;font-size:25px;"><?php echo $not_voted; ?></b>
            </div>
        </div>

        <!-- 🔹 2. Group Vote Tally -->
        <section style="margin-top:30px; 
            background:#fff; 
            padding:20px; 
            border-radius:12px; 
            box-shadow:0 4px 12px rgba(0,0,0,0.1);">
            <h2>Group Vote Tally</h2>
            <div class="groups-container">
                <?php foreach ($groups as $group) { ?>
                    <div class="group-card">
                        <img src="../uploads/<?php echo $group['photo']; ?>" alt="Group">
                        <h3><?php echo $group['name']; ?></h3>
                        <p>Total Votes: <b><?php echo $group['votes']; ?></b></p>
                        <?php if ($group['votes'] == $winner_votes && $winner_votes > 0) {
                            echo "<p style='color:green; font-weight:bold;'>🏆 Leading</p>";
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- 🔹 3. Winner Announcement -->
        <section
            style="margin-top:30px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); text-align:center;">
            <h2>🏆 Winner Announcement</h2>
            <?php
            if ($winner_votes == 0) {
                echo "<p style='color:gray;'>No votes have been cast yet.</p>";
            } elseif (count($winners) > 1) {
                echo "<p style='color:orange; font-weight:bold;'>It's a Tie! (" . count($winners) . " groups with $winner_votes votes each)</p>";
                echo "<div style='display:flex; flex-wrap:wrap; justify-content:center; gap:20px; margin-top:15px;'>";
                foreach ($winners as $tie) {
                    echo "<div style='background:#f9f9f9; border-radius:10px; padding:15px; box-shadow:0 0 8px rgba(0,0,0,0.1); width:180px;'>
        <img src='../uploads/{$tie['photo']}' style='width:80px;height:80px;border-radius:50%;object-fit:cover;'>
        <h3 style='margin:10px 0 5px; color:#007bff;'>{$tie['name']}</h3>
        <p style='margin:0;color:#555;'>Votes: <b>{$tie['votes']}</b></p>
        </div>";
                }
                echo "</div>";
            } else {
                $winner = $winners[0];
                echo "<div style='margin-top:15px;'>
    <img src='../uploads/{$winner['photo']}' style='width:100px;height:100px;border-radius:50%;object-fit:cover;box-shadow:0 0 10px rgba(0,123,255,0.4);'>
    <h3 style='color:#007bff; margin:10px 0 5px;'>{$winner['name']}</h3>
    <p style='color:green; font-weight:bold;'>Winner with {$winner_votes} votes!</p>
    </div>";
            }
            ?>
        </section>

        <!-- 🔹 4. Voter Details Table -->
        <section
            style="margin-top:30px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
            <h2>Voter Details</h2>
            <div style="overflow-x:auto;">
                <table id="votersTable" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#007bff; color:#fff;">
                            <th>#</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $voters = mysqli_query($connect, "SELECT * FROM user WHERE role='1'");
                        $i = 1;
                        while ($voter = mysqli_fetch_assoc($voters)) {
                            $status = ($voter['status'] == 1) ? "<span style='color:green;font-weight:bold;'>Voted ✅</span>" : "<span style='color:red;font-weight:bold;'>Not Voted ❌</span>";
                            echo "<tr><td>$i</td><td>{$voter['name']}</td><td>{$voter['mobile']}</td><td>{$voter['address']}</td><td>$status</td></tr>";
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 🔹 Reset Votes Button -->
        <form method="post" style="text-align: left; width:200px; float: right">
            <button type="submit" name="reset_votes" class="reset-btn">Reset Votes</button>
        </form>

    </div>

    <script>
        $(document).ready(function () {
            $('#votersTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50],
                "order": [[0, "asc"]],
                "columnDefs": [{ "orderable": false, "targets": 4 }]
            });
        });
    </script>

</body>

</html>