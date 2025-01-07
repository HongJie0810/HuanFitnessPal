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

  // From previous lesson
  $id = isset($_GET["water_id"]) ? (int)$_GET["water_id"] : null;

  // Get the specified date
  $selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

  // Delete only if water_id is not null and greater than 0
  if ($id > 0) {
      $sql = "DELETE FROM water_consumption WHERE water_id = ? AND user_id = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
      
      if (mysqli_stmt_execute($stmt)) {
          echo "Record deleted successfully";
          header("Location: MainWaterCon.php?date=" . urlencode($selected_date));
          exit(); 
      } else {
          echo "Error deleting record: " . mysqli_error($conn);
      }

      mysqli_stmt_close($stmt);
  } else {
      echo "Invalid water_id. Record not found.";
      header("Location: MainWaterCon.php?date=" . urlencode($selected_date) . "&refresh=" . time());
      exit();
  }

  mysqli_close($conn);
?>
