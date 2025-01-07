<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
} 
// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "user_information"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new-password'];
    $reenter_password = $_POST['reenter-password'];


    if ($new_password === $reenter_password) {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            $sql = "UPDATE huan_fitness_users SET password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_password, $user_id);

            if ($stmt->execute()) {
                echo "Password updated successfully!";
                header("Location: user_profile_photo.html"); 
                exit();
            } else {
                echo "Error updating password: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Session expired or user ID not found.";
        }
    } else {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    }
}

$conn->close();
?>
