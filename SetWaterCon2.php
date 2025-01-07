<?php

// Connect to database
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

$goal_amount = !empty($_POST['goal_amount']) ? $_POST['goal_amount'] : "";
$goal_date = !empty($_POST['goal_date']) ? $_POST['goal_date'] : "";
$action = $_POST['action'] ?? "";

if ($action === "insert") {
    // Check if a goal already exists for this user on this date
    $sql_check = "SELECT * 
                  FROM water_consumption 
                  WHERE date = ? 
                  AND user_id = ?";

    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "si", $goal_date, $user_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "A goal for this date already exists.";
        echo "<div style='display:inline-block; margin-left:10px;'>
                <button id='backButton'>Back</button>
                <script>
                    document.getElementById('backButton').onclick = function() {
                        window.location.href = 'MainWaterCon.php?date=" . urlencode($goal_date) . "';
                    };
                </script>
              </div>";
    } else {
        $sql_insert = "INSERT INTO water_consumption (goal_amount, date, user_id) 
                       VALUES (?, ?, ?)";

        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "isi", $goal_amount, $goal_date, $user_id);

        if (mysqli_stmt_execute($stmt_insert)) {
            echo "Water goal set successfully!";
            echo "<div style='display:inline-block; margin-left:10px;'>
                    <button id='doneButton'>Done</button>
                    <script>
                        document.getElementById('doneButton').onclick = function() {
                            window.location.href = 'MainWaterCon.php?date=" . urlencode($goal_date) . "';
                        };
                    </script>
                  </div>";
        } else {
            echo "Error setting goal.";
            echo "<div style='display:inline-block; margin-left:10px;'>
                    <button id='backButton'>Back</button>
                    <script>
                        document.getElementById('backButton').onclick = function() {
                            window.location.href = 'MainWaterCon.php?date=" . urlencode($goal_date) . "';
                        };
                    </script>
                  </div>";
        }
    }
    mysqli_stmt_close($stmt_check);

} elseif ($action === "update") {
    $sql_update = "UPDATE water_consumption SET 
                   goal_amount = ? 
                   WHERE date = ? 
                   AND user_id = ?";

    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "isi", $goal_amount, $goal_date, $user_id);

    if (mysqli_stmt_execute($stmt_update)) {
        echo "Water goal updated successfully!";
        header("Location: MainWaterCon.php?date=" . urlencode($goal_date));
        exit();
    } else {
        echo "Error updating goal.";
        header("Location: MainWaterCon.php?date=" . urlencode($goal_date));
        exit();
    }
}
mysqli_close($conn);
?>
