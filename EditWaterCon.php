<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_information";

// Create connection
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

// Get water_id from URL parameters
$id = isset($_GET["water_id"]) ? $_GET["water_id"] : "";
if (empty($id)) {
    header("Location: MainWaterCon.php?error=missing_id");
    exit(); 
}

$type = isset($_GET["type"]) ? $_GET["type"] : "";
$Adate = isset($_GET["Adate"]) ? $_GET["Adate"] : "";

// Select only records that match both water_id and user_id
$sql = "SELECT * FROM water_consumption WHERE water_id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row["water_id"];
        $amount = $row["amount"];
        $time = $row["time"];
        $type = $row["type"];
        $other_type = $row["other_type"];
        $Adate = $row["date"];
    }
} else {
    // If the record is not found, redirect or display an error
    header("Location: MainWaterCon.php?error=not_found");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Edit Water Consumption</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/Water_Con.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<script>
        function showOtherType(select) {
        var otherTypeContainer = document.getElementById("other_type_container");
        var otherTypeInput = document.getElementById("other_type");

        if (select.value === "other") {
            otherTypeContainer.style.display = "block"; 
        } else {
            otherTypeContainer.style.display = "none"; 
            otherTypeInput.value = ""; // Clear the value of “Other Type of Water”.
        }
    }

    window.onload = function() {
      var selectedType = "<?= $type ?>";
      if (selectedType === "other") {
        document.getElementById("other_type_container").style.display = "block";
      }
    };
    </script>
</head>
<body>
<div class="layout">
<div class="container add" id="container">
        <!-- Menu code remains unchanged -->
        <div class="brand">
            <h3>Menu</h3>
            <a href="#" id="toggle"><i class="bi bi-list"></i></a>
        </div>
        <div class="user">
            <img src="dumbbell.webp" alt="">
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

    <div class="main-contents">
        <h1>Edit Water Consumption</h1><br>
        <form id="patient" name="patient" action="EditWaterCon2.php" method="POST">

            <div class="water-add-detail">
                <div class="input-group">
                    <!--ID-->
                    <input type="hidden" id="water_id" name="water_id" maxlength="30" readonly value="<?=$id?>">
                </div>

                <div class="input-group">
                    <!--Amount of input water-->
                    <label for="amount">Water amount (ml): <input type="number" id="amount" name="amount"
                    placeholder="Enter the amount of water consumption" step="0.01" min="0" required value="<?=$amount?>"><br><br>
                </div>

                <div class="input-group">
                    <!--Time selection-->
                    <label for="time">Time of Day:</label>
                    <input type="time" id="time" name="time" required value="<?=$time?>"><br><br>
                </div>

                <div class="input-group">
                    <!--Type of water-->
                    <label for="type">Type of Water:</label>
                    <select id="type" name="type" onchange="showOtherType(this)">
                        <option value="plain_water"
                            <?php
                                if($type=="plain_water") echo "selected";
                            ?>
                            >Plain Water</option>
                            <option value="sports_drink"
                            <?php
                                if($type=="sports_drink") echo "selected";
                            ?>
                            >Sports Drink</option>
                            <option value="juice"
                            <?php
                                if($type=="juice") echo "selected";
                            ?>
                            >Juice</option>
                            <option value="other"
                            <?php
                                if($type=="other") echo "selected";
                            ?>
                            >Other</option><br>
                    </select><br><br>
                </div>

                <div class="input-group">
                    <!--Input box for "other" types of water-->
                    <div id="other_type_container" style="display: none;">
                        <label for="other_type">Other Type of Water: </label>
                        <input type="text" id="other_type" name="other_type" placeholder="Please specify the type of water" value="<?=$other_type?>"><br>
                    </div>
                </div>

                <div class="input-group">
                    <!--Calender-->
                    <label for="date">Date:</label>
                    <input type="date" id="Adate" name="Adate" value="<?=$Adate?>";>
                    <br>
                </div>    

                <div class="buttons">
                    <div class="back-button">
                        <button type="button" class="back-btn" onclick="window.history.back()">Back</button>
                    </div>
                    <div class="action-buttons">
                        <input type="reset" value="Clear" id="nreset" name="nreset">
                        <input type="submit" value="Submit" id="nsubmit" name="nsubmit">
                    </div>
                </div>
            </div>   
        </form>
    </div> 
</div>
</body>
</html>