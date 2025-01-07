<?php

require_once('database.php');


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

function getNextUserId($con) {
    $query = "SELECT MAX(Nutritionist_ID) AS max_id FROM huan_fitness_nutritionist";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        
        $numeric_part = (int)substr($max_id, 1); 
        return 'N' . str_pad($numeric_part + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'N010'; 
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate 
    $username = trim($_POST['Name']);
    $gender = trim($_POST['Gender']);
    $phone_no = trim($_POST['PhoneNo']);
    $email_address = trim($_POST['Email_address']);
    $category = trim($_POST['Category']);
    $date_of_birth = date('Y-m-d', strtotime(trim($_POST['Date_of_birth'])));
    $current_page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    // Validate mandatory fields
    if (empty($username) || empty($gender) || empty($phone_no) || empty($email_address) || empty($category) || empty($date_of_birth)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    if (!preg_match('/^\+60\d{2}-\d{3}-\d{4,5}$/', $phone_no)) {
        echo "<script>alert('Invalid phone number format. Please use +60xx-xxx-xxxx or +60xx-xxx-xxxxx format.'); window.history.back();</script>";
        exit;
    }

    // Validate email format
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address.'); window.history.back();</script>";
        exit;
    }

    // Validate date of birth format 
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) {
        echo "<script>alert('Invalid date of birth format. Use YYYY-MM-DD.'); window.history.back();</script>";
        exit;
    }

    
    $user_id = getNextUserId($con);

    // Prepare the SQL statement for inserting a new user
    $insert_query = "INSERT INTO huan_fitness_nutritionist (Nutritionist_ID, Name, Gender, PhoneNo, Email_address, Category, Date_of_birth) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Use prepared statements to prevent SQL injection
    if ($stmt = mysqli_prepare($con, $insert_query)) {
        
        mysqli_stmt_bind_param($stmt, 'sssssss', $user_id, $username, $gender, $phone_no, $email_address, $category, $date_of_birth);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            
            echo "<script>alert('User added successfully!'); window.location.href = 'Nutritionist_Information.php?page=$current_page';</script>";
        } else {
            
            echo "<script>alert('Error: " . mysqli_stmt_error($stmt) . "');</script>";
        }

        
        mysqli_stmt_close($stmt);
    } else {
       
        echo "<script>alert('Error preparing statement: " . mysqli_error($con) . "');</script>";
    }
}


mysqli_close($con);

?>