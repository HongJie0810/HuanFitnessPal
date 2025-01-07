<?php

$host = 'localhost';
$db = 'user_information';  
$user = 'root';  
$password = '';  

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email']; 

    // Prepare and execute the SQL statement to check for a matching user
    $stmt = $conn->prepare("SELECT * FROM huan_fitness_users WHERE username = ? AND email_address = ?"); 
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, redirect to reset password page
        session_start();
        $_SESSION['username'] = $username; // Store the username 
        header("Location: reset-password.html");
        exit();
    } else {
        // User not found
        echo "<script>alert('Username or email does not match. Please try again.');</script>";
        header("Location: forgot-password.html");
    }
}
?>
