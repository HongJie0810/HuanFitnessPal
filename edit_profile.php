<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db_connection.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $member_id = (int)$_POST['member_id']; 
    $status = $_POST['status'];
    $phone_no = $_POST['phone_no'];
    $email_address = $_POST['email_address'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $height = $_POST['height'];
    $password = $_POST['password'];
    $profile_photo = ''; 

    
    if (!isset($member_id)) {
        die("Error: member_id is not set.");
    }

    // Query to get the current profile photo
    $sql_get_photo = "SELECT profile_photo FROM huan_fitness_users WHERE user_id = ?";
    $stmt_get_photo = $conn->prepare($sql_get_photo);
    if ($stmt_get_photo === false) {
        die("Error preparing get photo statement: " . $conn->error);
    }
    $stmt_get_photo->bind_param("i", $user_id);
    $stmt_get_photo->execute();
    $stmt_get_photo->bind_result($current_photo);
    $stmt_get_photo->fetch();
    $stmt_get_photo->close();

    // Handle file upload for profile photo
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    }

    // Check if a file is uploaded
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] === UPLOAD_ERR_OK) {
       
        $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
        $fileNameCmps = explode(".", $_FILES['fileUpload']['name']);
        $fileExtension = strtolower(end($fileNameCmps));

        // Set allowed extensions
        $allowedFileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
        if (in_array($fileExtension, $allowedFileExtensions)) {
            
            $newFileName = $user_id . '.' . $fileExtension;
            $uploadFileDir = "uploads/";
            $dest_path = $uploadFileDir . $newFileName;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Update the database with the new photo filename
                $profile_photo = $newFileName; 
            } else {
                echo "<script>alert('Error moving the file to the upload directory. Check directory permissions.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Allowed types: jpg, gif, png, jpeg');</script>";
        }
    }

    // Update the user's profile with profile photo and other details
    $sql = "UPDATE huan_fitness_users SET 
        username = ?,
        profile_photo = ?, 
        phone_no = ?, 
        email_address = ?, 
        gender = ?, 
        date_of_birth = ?, 
        height = ? 
    WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // If profile photo is empty, use the current photo
    if (empty($profile_photo)) {
        $profile_photo = $current_photo;
    }

   
    $stmt->bind_param("sssssssi", $username, $profile_photo, $phone_no, $email_address, $gender, $date_of_birth, $height, $user_id);



if ($stmt->execute()) {
    // If password is provided, update the password in a separate query
    if (!empty($password)) {
        $sql_password = "UPDATE huan_fitness_users SET password = ? WHERE user_id = ?";
        $stmt_password = $conn->prepare($sql_password);
        if ($stmt_password === false) {
            die("Error preparing password statement: " . $conn->error);
        }

        
        $stmt_password->bind_param("si", $password, $user_id);

        // Execute password update
        if ($stmt_password->execute()) {
            echo '<script>alert("Profile and password updated successfully."); window.location.href = "user_profile.php";</script>';
        } else {
            echo '<script>alert("Failed to update password: ' . $stmt_password->error . '"); window.location.href = "user_profile.php";</script>';
        }

        $stmt_password->close();
    } else {
        echo '<script>alert("Profile updated successfully."); window.location.href = "user_profile.php";</script>';
    }

    // Update the status from huan_fitness_members
    $sql_status = "UPDATE huan_fitness_members SET status = ? WHERE member_id = ?";
    $stmt_status = $conn->prepare($sql_status);
    if ($stmt_status === false) {
        die("Error preparing status statement: " . $conn->error);
    }

    
    $stmt_status->bind_param("si", $status, $member_id);

    // Execute status update
    if ($stmt_status->execute()) {
        
    } else {
        echo '<script>alert("Failed to update status: ' . $stmt_status->error . '"); window.location.href = "user_profile.php";</script>';
    }

    $stmt_status->close();
} else {
    echo '<script>alert("Failed to update profile: ' . $stmt->error . '"); window.location.href = "user_profile.php";</script>';
}

    $stmt->close();
    $conn->close();
}
?>
