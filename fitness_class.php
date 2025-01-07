<?php
include('db_conn.php');
session_start();

// Retrieve user_id from session, redirect to login if not set
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


if ($user_id === null) {
    header('Location: login.php');
    exit();
}

$member_id_query = "SELECT member_id FROM huan_fitness_users WHERE user_id = ?";
$member_stmt = $conn->prepare($member_id_query);
$member_stmt->bind_param('s', $user_id);
$member_stmt->execute();
$member_result = $member_stmt->get_result();


if ($member_result->num_rows > 0) {
    $member_row = $member_result->fetch_assoc();
    $member_id = $member_row['member_id'];
} else {
    echo "<script>alert('User not found. Please log in again.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch fitness class details from the database and store in an array
$query = "SELECT fitness_class_id, fitness_class_category, day, start_time, end_time FROM fitness_class_details";
$result = $conn->query($query); 

if (!$result) {
    die("Query failed: " . $conn->error); 
}

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Class Registration</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/fitness_class.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body style = "background: linear-gradient(45deg, #f3e5f5, #e1f5fe);">
    <div class="container add" id="container">
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

    <div class="main-content-wrapper">
        <div class="main-content">
            <h2>Select a Fitness Class</h2>
            <div class="class-cards">
                <?php foreach ($classes as $class): ?>
                    <div class="class-card">
                        <h3><?php echo htmlspecialchars($class['fitness_class_category']); ?></h3>
                        <p>Day: <?php echo htmlspecialchars($class['day']); ?></p>
                        <p>Time: <?php echo htmlspecialchars($class['start_time']) . ' - ' . htmlspecialchars($class['end_time']); ?></p>
                        <form action="join_class.php" method="POST" onsubmit="return confirmClass()">
                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['fitness_class_id']); ?>">
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($class['fitness_class_category']); ?>">
                            <button type="submit" class="join-button">Join Class</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        var toggle = document.getElementById("toggle");
        var container = document.getElementById("container");

        toggle.onclick = function() {
            container.classList.toggle('active');
        }
    </script>

    <script>
        function confirmClass() {
            // Display a confirmation message
            const userConfirmed = window.confirm("Are you sure you want to register for this fitness class?");
            
            // Return true if confirmed, false to cancel submission
            return userConfirmed;
        }
    </script>

</body>
</html>
