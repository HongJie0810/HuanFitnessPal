<?php

session_start();


$host = 'localhost';
$db = 'user_information';  
$user = 'root';  
$password = '';  

$conn = new mysqli($host, $user, $password, $db);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = $_POST['admin_name']; 
    $admin_password = $_POST['password'];

   
    $stmt = $conn->prepare("SELECT * FROM huan_fitness_admin WHERE admin_name = ? AND password = ?");
    $stmt->bind_param("ss", $admin_name, $admin_password);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        
        $_SESSION['success_message'] = "Admin login successfully!";
        header("Location: home_page.php");  
        exit();
    } else {
        
        echo "<script>alert('Invalid Admin Name or Password. Please try again.');</script>";
        echo "<script>window.location.href = 'admin.html';</script>";
    }

    
    $stmt->close();
    $conn->close();
}
?>
