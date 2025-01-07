<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'];
    $username = $_POST['username'];

    $stmt = $conn->prepare("UPDATE huan_fitness_users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $newPassword, $username); 
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update password."]);
    }

    $stmt->close();
}

$conn->close();
?>
