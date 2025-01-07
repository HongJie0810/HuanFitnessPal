<?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_information";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id === null) {
        header('Location: login.php');
        exit();
    }

    // Get the date of the day
    $today_date = date("Y-m-d");

    // Get the specified date
    $selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

    // Queries the total amount of water consumed on the day
    $sql_consumed = "SELECT SUM(amount) AS total_consumed 
                    FROM water_consumption 
                    WHERE date = ? AND user_id = ?";
                    
    $stmt_consumed = mysqli_prepare($conn, $sql_consumed);
    mysqli_stmt_bind_param($stmt_consumed, "si", $selected_date, $user_id);
    mysqli_stmt_execute($stmt_consumed);
    $result_consumed = mysqli_stmt_get_result($stmt_consumed);
    $total_consumed = 0;

    if ($row_consumed = mysqli_fetch_assoc($result_consumed)) {
        $total_consumed = $row_consumed['total_consumed'];
    }

    mysqli_stmt_close($stmt_consumed);

    // Query targets for the selected date
    $sql_goal = "SELECT goal_amount 
                FROM water_consumption 
                WHERE date = ? AND user_id = ?
                ORDER BY date DESC LIMIT 1";

    $stmt_goal = mysqli_prepare($conn, $sql_goal);
    mysqli_stmt_bind_param($stmt_goal, "si", $selected_date, $user_id);
    mysqli_stmt_execute($stmt_goal);

    $result_goal = mysqli_stmt_get_result($stmt_goal);
    // If no goal is found, set to 0 or null
    $daily_goal = ($row_goal = mysqli_fetch_assoc($result_goal)) ? $row_goal['goal_amount'] : 0;
    mysqli_stmt_close($stmt_goal);

    $goal_achieved = $daily_goal !== null && $total_consumed >= $daily_goal;

    // New query to get the total water amount for the last 7 days
    $sql_all_data = "SELECT date, SUM(amount) as total_amount, goal_amount 
                     FROM water_consumption 
                     WHERE user_id = ?
                     GROUP BY date 
                     ORDER BY date ASC";
                     
    $stmt_all_data = mysqli_prepare($conn, $sql_all_data);
    mysqli_stmt_bind_param($stmt_all_data, "i", $user_id);
    mysqli_stmt_execute($stmt_all_data);
    $result_all_data = mysqli_stmt_get_result($stmt_all_data);

    // Put all data into an array
    $all_data = [];
    while($row = mysqli_fetch_assoc($result_all_data)) {
        $all_data[] = $row;
    }

    mysqli_stmt_close($stmt_all_data);

    // Calculate how many 7-day cycles are needed
    $total_records = count($all_data);
    $periods_needed = ceil($total_records / 7);

    // Query to get the total water amount for each date
    $sql_summary = "SELECT date, SUM(amount) AS total_amount, goal_amount
                    FROM water_consumption
                    WHERE user_id = ?
                    GROUP BY date
                    ORDER BY date DESC";
                    
    $stmt_summary = mysqli_prepare($conn, $sql_summary);
    mysqli_stmt_bind_param($stmt_summary, "i", $user_id);
    mysqli_stmt_execute($stmt_summary);
    $result_summary = mysqli_stmt_get_result($stmt_summary);

    // Query to get details for the selected date
    $sql_details = "SELECT *
                    FROM water_consumption 
                    WHERE date = ? AND user_id = ?";

    $stmt_details = mysqli_prepare($conn, $sql_details);
    mysqli_stmt_bind_param($stmt_details, "si", $selected_date, $user_id);
    mysqli_stmt_execute($stmt_details);
    $result_details = mysqli_stmt_get_result($stmt_details);

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Water Consumption</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/Water_Con.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<script>
        
    var toggle = document.getElementById("toggle");
		var container = document.getElementById("container");

		toggle.onclick = function() {
			container.classList.toggle('active');
		}
</script>
<body style = "background: linear-gradient(45deg, #f3e5f5, #e1f5fe);">
    <div class="container add" id="container">
        <!-- Menu code remains unchanged -->
        <div class="brand">
            <h3>Menu</h3>
            <a href="#" id="toggle"><i class="bi bi-list"></i></a>
        </div>
        <div class="user">
            <img src="css/img/dumbbell.png" alt="">
            <div class="name">
                <h3 style = "color:white">HuanFitness</h3>
            </div>
        </div>
        <div class="navbar">
            <ul>
                <li><a href="dashboard.php"><i class="bi bi-house"></i><span>DashBoard</span></a></li>
                <li><a href="user_profile.php"><i class="bi bi-person-circle"></i><span>User Profile</span></a></li>
                <li><a href="weight.php"><i class="bi bi-calendar2-fill"></i><span>Body Weight Record</span></a></li>
                <li><a href="MainWaterCon.php"><i class="bi bi-droplet-fill"></i><span>Water Consumption Record</span></a></li>
                <li><a href="exercise_index.php"><i class="bi bi-radar"></i><span>Exercise Record</span></a></li>
                <li><a href="consultation_category.php"><i class="bi bi-journal-medical"></i><span>Dietary Consultation</span></a></li>
                <li><a href="fitness_class.php"><i class="bi bi-universal-access-circle"></i><span>Fitness Class Registration</span></a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
            <?php if (!isset($_GET['date'])): ?>
                <h1>Water Consumption Weekly</h1><br>
                <!-- Water Consumption Summary -->
                <div class="water-summary">
                    <div class="water-details">
                    <h3>Your Water Consumption Detail</h3><br>
                <?php
                // Loop to create a table for each 7-day 
                for($i = 0; $i < $periods_needed; $i++) {
                    $start = $i * 7;  // Starting index for each week
                    $period_total = 0; // Total water for the week
                ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Water Amount (ml)</th>
                                <th>Water Goal (ml)</th>
                                <th>View / Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Show 7 days of data
                            $end = min($start + 7, $total_records);
                            for($j = $start; $j < $end; $j++) {
                                $row = $all_data[$j];
                                $period_total += $row['total_amount'];
                            ?>
                                <tr>
                                    <td><?= $row['date'] ?></td>
                                    <td><?= $row['total_amount'] ?> ml</td>
                                    <td><?= $row['goal_amount'] ? $row['goal_amount'] : 0 ?></td>
                                    <td><a href="MainWaterCon.php?date=<?= $row['date'] ?>" title="View/Edit"><i class="bi bi-pencil-square"></i></a></td>
                                </tr>
                            <?php
                            } 
                            ?>
                                    <tr class="weekly-summary">
                                        <td colspan="4">
                                            Weekly Total (<?= $all_data[$start]['date'] ?> to <?= $all_data[$end - 1]['date'] ?>): 
                                            <?= $period_total ?> ml
                                        </td>
                                    </tr>
                            </tbody>
                        </table>
                        <br>
                        <?php
                        }
                        ?>
                            <div class="buttons">
                                <button class="add-btn" onclick="location.href='AddWaterCon.php'">Add New Date Water</button>
                            </div>
                    </div>
                </div>
            
            <?php else: ?>
                <h1>Water Consumption Daily</h1>
                    <div class="water-info">
                        <div class="water-status-container">
                            <div class="consumption-status">
                                <div>Date: <?= $selected_date ?></div>
                            </div>
                            <div class="consumption-status">
                                <div>Current water consumption: <?= $total_consumed ?> ml</div>
                            </div>
                            <div class="consumption-status">
                                <div>Water Goals: 
                                    <?php if ($daily_goal !== null): ?> 
                                        <?= $daily_goal ?> ml
                                        <div class="buttons">
                                        <a class="edit-goals" href="UpSetWaterCon.php" title="Edit Goal"><i class="bi bi-pencil"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        </div>

                        <?php if ($goal_achieved): ?>
                                <p style="color: green; font-weight: bold; font-size: 1.1em; text-align: center;">Congratulations! You've reached your water intake goal.</p>
                        <?php endif; ?>

                        <div class="water-details">
                            <h3>Your Water Consumption Detail</h3><br>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Water Amount (ml)</th>
                                            <th>Type of Water</th>
                                            <th>Time</th>
                                            <th>Date</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($row = mysqli_num_rows($result_details) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result_details)): ?>
                                            <tr>
                                                <td><?= $row['amount'] ?></td>
                                                <?php 
                                                $displayType = ($row['type'] === "other" && !empty($row['other_type'])) ? $row['other_type'] : $row['type'];
                                                ?>
                                                <td><?= $displayType ?></td>
                                                <td><?= $row['time'] ?></td>
                                                <td><?= $row['date'] ?></td>
                                                <td><a href="EditWaterCon.php?water_id=<?=$row['water_id']?>" title="Edit"><i class="bi bi-pencil-square"></i></a></td>
                                                <td><a href="DeleteWaterCon.php?water_id=<?= $row['water_id'] ?>&date=<?= $selected_date ?>" title="Delete"><i class="bi bi-trash3"></i></a></td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6">No water consumption data found for <?= $selected_date ?>.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                    <div class="buttons">
                                        <button class="adds-btn" onclick="location.href='AddWaterCon.php'" title="Add New Water Consumption">Add+</button>
                                    </div>
                                        <button class="backs-btn" onclick="location.href='MainWaterCon.php'" title="Back to previous page">Back</button>
                        </div>
            </div>
            <?php endif; ?>
        </div>
    
    </body>
</html>

<?php 
// Close connections
mysqli_stmt_close($stmt_details);
?>