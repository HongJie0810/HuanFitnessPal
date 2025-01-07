<?php

include 'db_conn.php';

session_start(); 

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


if ($user_id === null) {
    // Handle the case where the user_id is not set 
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Navigation Bar</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/excercise_index.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
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

    <div class="main-content"  style = "background: linear-gradient(45deg, #f3e5f5, #e1f5fe);">
    <div class="calendar-title">Calendar</div>

    <div class="date-selection">
        <p>Choose Date to Record:</p>
    </div>

      <div class="calendar-container">
        <header class="calendar-header">
            <p class="calendar-current-date"></p>
            <div class="calendar-navigation">
                <span id="calendar-prev" class="material-symbols-rounded">
                    chevron_left
                </span>
                <span id="calendar-next" class="material-symbols-rounded">
                    chevron_right
                </span>
            </div>
        </header>

        <div class="calendar-body">
            <ul class="calendar-weekdays">
                <li>Sun</li>
                <li>Mon</li>
                <li>Tue</li>
                <li>Wed</li>
                <li>Thu</li>
                <li>Fri</li>
                <li>Sat</li>
            </ul>
            <ul class="calendar-dates"></ul>
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

    
    <script src="exercise_index.js"></script>
</body>
</html>