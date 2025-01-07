<?php
include('db_conn.php');
session_start(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $weight = $_POST['weight'];
    $date = $_POST['date'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id === null) {
        // Handle the case where the user_id is not set
        $_SESSION['message'] = "Error: User is not logged in.";
        $response = ['success' => false, 'message' => $_SESSION['message']];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    //  check if a weight for this date already exists for the user
    $check_sql = "SELECT * FROM weights WHERE date = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $date, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // If there is already an entry for the same date, send an error message
        $_SESSION['message'] = "Error: Weight for this date has already been entered.";
        $response = ['success' => false, 'message' => $_SESSION['message']];
    } else {
        // If no duplicate date, proceed to insert the new weight
        $sql = "INSERT INTO weights (weight, date, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsi", $weight, $date, $user_id); 

        if ($stmt->execute()) {
            // Set session message for success
            $_SESSION['message'] = "Weight added successfully!";
            $response = ['success' => true, 'message' => $_SESSION['message']];
        } else {
            // Handle error during insertion
            $_SESSION['message'] = "Error adding weight.";
            $response = ['success' => false, 'message' => $_SESSION['message']];
        }

        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();

    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; 
}
?>
