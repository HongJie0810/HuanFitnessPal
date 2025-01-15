<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];  // Get username from POST data
    $password = $_POST['password'];  // Get password from POST data


    $stmt = $conn->prepare("SELECT user_id, password FROM huan_fitness_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];  // Retrieve stored password
        $user_id = $row['user_id'];  // Retrieve user_id


        if ($password === $stored_password) {
            $_SESSION['user_id'] = $user_id;  
            echo "<script>alert('Login successful!'); window.location.href='welcome.php';</script>";
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid username. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
