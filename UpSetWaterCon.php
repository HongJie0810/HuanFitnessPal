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
    <title>Edit Daily Water Goal</title>
    <link rel="stylesheet" href="css/navBar.css?v=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/Water_Con.css">
</head>
        <script>
        var toggle = document.getElementById("toggle");
        var container = document.getElementById("container");

        toggle.onclick = function() {
          container.classList.toggle('active');
        }
        </script>

<body>
    <div class="container add" id="container">
        <!-- Menu code remains unchanged -->
        <div class="brand">
            <h3>Menu</h3>
            <a href="#" id="toggle"><i class="bi bi-list"></i></a>
        </div>
        <div class="user">
          <img src="icon/dumbbell.png" alt="">
            <div class="name">
                <h3>HuanFitness</h3>
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

    <div class="main-contentss">

    <h1>Edit Daily Water Goal</h1>
  <form id="goal" name="goal" action="SetWaterCon2.php" method="POST">

    <div class="water-add-details">
      <div class="input-groupss">
        <input type="hidden" name="action" value="update">
      </div>

      <div class="input-groupss">
        <!--ID-->
        <input type="hidden" id="txtidS" name="txtidS" maxlength="30" readonly value="<?=$idS?>">
      </div>

      <div class="input-groupss">
        <!--Amount of input water-->
        <label>Water Goal (ml):</label><input type="number" id="goal_amount" name="goal_amount"
        placeholder="Set your daily water goal" step="0.01" min="0" required value="<?=$goal_amount?>"><br><br>
      </div>

      <div class="input-groupss">
        <!--Calender-->
        <label for="goal_date">Date:</label>
        <input type="date" id="goal_date" name="goal_date" value="<?=$goal_date?>";>
        <br><br>
      </div>

      <div class="buttons">
              <button type="button" class="back-btn" onclick="window.history.back()">Back</button>
              <input type="submit" class="add-btn" value="Set Goal" id="nsubmit" name="nsubmit">
        </div>
      </div>
    </div>
  </form>

</body>
</html>
