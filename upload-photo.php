<?php
// Include your database connection file
include 'db_connection.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}


if (isset($_POST['submit']) && $_POST['submit'] === 'Upload File') {
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] === UPLOAD_ERR_OK) {
        $user_id = $_SESSION['user_id']; 
        
        $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
        $fileNameCmps = explode(".", $_FILES['fileUpload']['name']);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Set allowed extensions
        $allowedFileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedFileExtensions)) {
            // Use user_id as the file name
            $newFileName = $user_id . '.' . $fileExtension;
            $uploadFileDir = "uploads/";
            $dest_path = $uploadFileDir . $newFileName;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Update the database with the new photo filename
                $sql = "UPDATE huan_fitness_users SET profile_photo = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $newFileName, $user_id);

                if ($stmt->execute()) {
                    echo "<script>alert('Profile photo uploaded successfully!'); window.location.href = 'welcome.php';</script>";
                } else {
                    echo "<script>alert('Error updating the database. Please try again.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Error moving the file to the upload directory. Check directory permissions.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Allowed types: jpg, gif, png, jpeg');</script>";
        }
    } else {
        echo "<script>alert('File upload error: " . $_FILES['fileUpload']['error'] . "');</script>";
    }
}
?>
