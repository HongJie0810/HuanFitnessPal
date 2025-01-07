<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id === null) {
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Add Water Consumption</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/Water_Con.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
    

<script>
        function showOtherType(select) {
        var otherTypeContainer = document.getElementById("other_type_container");
        if (select.value === "other") {
            otherTypeContainer.style.display = "block"; // 显示容器
        } else {
            otherTypeContainer.style.display = "none"; // 隐藏容器
        }
    }
</script>

<body>
<div class="layout">
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
                <li><a href="weight.php"><i class="bi bi-3-square-fill"></i><span>Body Weight Record</span></a></li>
                <li><a href="MainWaterCon.php"><i class="bi bi-person-fill"></i><span>Water Consumption Record</span></a></li>
                <li><a href="exercise_index.php"><i class="bi bi-folder"></i><span>Exercise Record</span></a></li>
                <li><a href="consultation_category.php"><i class="bi bi-journal-medical"></i><span>Dietary Consultation</span></a></li>
                <li><a href="fitness_class.php"><i class="bi bi-journal-medical"></i><span>Fitness Class Registration</span></a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-contents">
    <h1>Add Water Consumption</h1><br>
    <form id="forder" name="forder" action="AddWaterCon2.php" method="POST">

        <div class="water-add-detail">

            <div class="input-group">
                <label for="amount">Water amount (ml):</label>
                <input type="number" id="amount" name="amount"
                    placeholder="Enter the amount of water consumption" step="0.01" min="0" required>
            </div>

            <div class="input-group">
                <label for="time">Time of Day:</label>
                <input type="time" id="time" name="time" required>
            </div>

            <div class="input-group">
                <label for="type">Type of Water:</label>
                <select id="type" name="type" onchange="showOtherType(this)">
                    <option value="plain_water">Plain Water</option>
                    <option value="sports_drink">Sports Drink</option>
                    <option value="juice">Juice</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div id="other_type_container" style="display: none;">
                <div class="input-group">
                    <label for="other_type">Other Type of Water:</label>
                    <input type="text" id="other_type" name="other_type" placeholder="Please specify the type of water">
                </div>
            </div>

            <div class="input-group">
                <label for="Adate">Date:</label>
                <input type="date" id="Adate" name="Adate" value="<?=date("Y-m-d")?>"
                    max="2025-12-31">
            </div>

            <div class="buttons">
                <div class="back-button">
                    <button type="button" class="bck-btn" onclick="window.history.back()">Back</button>
                </div>
                <div class="action-buttons">
                    <button type="reset" class="add-btn">Clear</button>
                    <button type="submit" class="add-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

    
</body>
</html>
