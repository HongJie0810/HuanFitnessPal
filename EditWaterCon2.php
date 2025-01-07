<?php

// set-up db connection
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

// Retrieve and check elements' values
$id = !empty($_POST["water_id"]) ? $_POST["water_id"] : null;
if (!$id) {
    die("Invalid water_id");
}

$amount = !empty($_POST["amount"]) ? $_POST["amount"] : "";
$time = !empty($_POST["time"]) ? $_POST["time"] : "";
$type = !empty($_POST["type"]) ? $_POST["type"] : "";
$other_type = !empty($_POST["other_type"]) ? $_POST["other_type"] : null;
$Adate = !empty($_POST["Adate"]) ? $_POST["Adate"] : "";

// Update statement with user_id verification
$sql = "UPDATE water_consumption 
        SET time = ?, 
            type = ?, 
            other_type = ?, 
            amount = ?, 
            date = ? 
        WHERE water_id = ? 
          AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssisi", $time, $type, $other_type, $amount, $Adate, $id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo "Record updated successfully.";
    header("location: MainWaterCon.php?date=" . urlencode($Adate));
    exit();
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
