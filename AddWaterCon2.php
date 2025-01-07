<?php

    // Database connection setup
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

    // Retrieve and check values
    $amount = empty($_POST["amount"]) ? "" : $_POST["amount"];
    $time = !empty($_POST["time"]) ? $_POST["time"] : "";
    $type = !empty($_POST["type"]) ? $_POST["type"] : "";
    $other_type = !empty($_POST["other_type"]) ? $_POST["other_type"] : NULL;
    $Adate = empty($_POST["Adate"]) ? "" : $_POST["Adate"];
    $goal_amount = !empty($_POST["goal_amount"]) ? $_POST["goal_amount"] : "";

    // Get the current maximum water_id and add 1 to it
    $query_max_id = "SELECT MAX(water_id) AS max_id FROM water_consumption";
    $result_max_id = mysqli_query($conn, $query_max_id);
    $row_max_id = mysqli_fetch_assoc($result_max_id);
    $new_water_id = $row_max_id['max_id'] + 1;

    // Check if a goal exists for the specified date and user_id
    $sql_check = "SELECT COUNT(*) as count FROM water_consumption WHERE date = ? AND user_id = ? AND goal_amount IS NOT NULL";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "si", $Adate, $user_id);
    mysqli_stmt_execute($stmt_check);

    // Fetch the result to check if a goal exists
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($result_check);

    // Insert a default goal if no goal exists for that date
    if ($row_check['count'] == 0) {
        $default_goal = 2000; // Default goal amount

        $sql_goal = "INSERT INTO water_consumption 
                    (water_id, user_id, time, type, other_type, amount, date, goal_amount) 
                    VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_goal = mysqli_prepare($conn, $sql_goal);
        mysqli_stmt_bind_param($stmt_goal, "iisssisi", $new_water_id, $user_id, $time, $type, $other_type, $amount, $Adate, $default_goal);
        mysqli_stmt_execute($stmt_goal);
        mysqli_stmt_close($stmt_goal);

    } else {
        // Insert a record without goal_amount if it already exists for the date
        $insert_query = "INSERT INTO water_consumption (water_id, user_id, date, amount, type, other_type, time) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "iisssss", $new_water_id, $user_id, $Adate, $amount, $type, $other_type, $time);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }

    // Redirect back to MainWaterCon.php with the selected date
    header("Location: MainWaterCon.php?date=" . urlencode($Adate));
    exit();

    mysqli_stmt_close($stmt_check);
    mysqli_close($conn);
?>
