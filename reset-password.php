<?php
// Include your database connection file
include 'db_connection.php';

session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to forgot password page if session is not set
    header("Location: forgot-password.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new-password'];
    $reenter_password = $_POST['reenter-password'];

    // Check if passwords match
    if ($new_password === $reenter_password) {
        // Directly store the new password without hashing
        $username = $_SESSION['username'];

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE huan_fitness_users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_password, $username);

        if ($stmt->execute()) {
            echo "<script>alert('Password reset successful!'); window.location.href = 'login.html';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again later.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    }
}
?>
