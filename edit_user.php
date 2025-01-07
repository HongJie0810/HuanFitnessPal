<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once('database.php');

    
    $user_id = $_POST['user_id'];
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $phone_no = mysqli_real_escape_string($con, $_POST['phone_no']);
    $email_address = mysqli_real_escape_string($con, $_POST['email_address']);
    $date_of_birth = mysqli_real_escape_string($con, $_POST['date_of_birth']);

    // Simple validation
    if (empty($username) || empty($phone_no) || empty($email_address) || empty($date_of_birth)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    // Validate phone number format 
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

    
    $query = "UPDATE huan_fitness_users SET 
              username=?, 
              gender=?, 
              phone_no=?, 
              email_address=?, 
              date_of_birth=?
              WHERE user_id=?";

    if ($stmt = mysqli_prepare($con, $query)) {
        
        mysqli_stmt_bind_param($stmt, 'ssssss', $username, $gender, $phone_no, $email_address, $date_of_birth, $user_id);

        
        if (mysqli_stmt_execute($stmt)) {
           
            echo "<script>alert('User updated successfully!'); window.location.href = 'User_Information.php';</script>";
        } else {
           
            echo "<script>alert('Error: " . mysqli_stmt_error($stmt) . "'); window.history.back();</script>";
        }

        
        mysqli_stmt_close($stmt);
    } else {
       
        echo "<script>alert('Error preparing statement: " . mysqli_error($con) . "'); window.history.back();</script>";
    }

    
    mysqli_close($con);
}
?>
