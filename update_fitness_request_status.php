<?php
require_once('database.php'); // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $status = $_POST['request_status']; // Use "request_status" to match JavaScript

    // Update request status in the database
    $stmt = $con->prepare("UPDATE fitness_class_member SET request_status = ? WHERE member_id = ?");
    $stmt->bind_param("ss", $status, $member_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating request status";
    }

    $stmt->close();
    $conn->close();
}
?>
