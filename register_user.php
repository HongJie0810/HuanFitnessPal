<?php
session_start(); 


$host = "localhost";
$user = "root";
$pass = "";
$dbname = "user_information";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function newUserId($conn) {
    $prefix = "2305"; 
    $newIdNumber = 1;  

    while (true) {
        $newUserId = $prefix . str_pad($newIdNumber, 3, "0", STR_PAD_LEFT);
        
        $sql = "SELECT user_id FROM huan_fitness_users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $newUserId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) { 
            break; 
        }
        $newIdNumber++;  
    }

    return $newUserId;  
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = newUserId($conn);

    $username = $_POST['username'];
    $email = $_POST['email_address'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone_no = $_POST['phone_no'];
    $height = $_POST['height'];
    $password = isset($_POST['new-password']) ? $_POST['new-password'] : '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password

    // Prepare and execute insert statement
    $stmt = $conn->prepare("INSERT INTO huan_fitness_users (user_id, username, gender, phone_no, email_address, date_of_birth, height, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $user_id, $username, $gender, $phone_no, $email, $dob, $height, $hashed_password); // Use $hashed_password

    if ($stmt->execute()) {
        // Store user_id in session
        session_start();
        $_SESSION['user_id'] = $user_id;

        // Redirect if successful
        header("Location: register_password.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;  // Display error
    }
    $stmt->close();
}

$conn->close();
?>
