<?php
require_once('database.php'); // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consult_id = $_POST['consult_id'];
    $status = $_POST['request_status']; // Use "request_status" to match JavaScript

    // Update request status in the database
    $stmt = $con->prepare("UPDATE dietary_consultations_details SET request_status = ? WHERE consult_id = ?");
    $stmt->bind_param("ss", $status, $consult_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating request status";
    }

    $stmt->close();
    $conn->close(); 
}
?>
