<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    require_once('database.php');

    // Get the form data
    $Nutritionist_ID = $_POST['Nutritionist_ID'];
    $Name = mysqli_real_escape_string($con, $_POST['Name']);
    $Gender = mysqli_real_escape_string($con, $_POST['Gender']);
    $PhoneNo = mysqli_real_escape_string($con, $_POST['PhoneNo']);
    $Email_address = mysqli_real_escape_string($con, $_POST['Email_address']);
    $Category = mysqli_real_escape_string($con, $_POST['Category']);
    $Date_of_birth = mysqli_real_escape_string($con, $_POST['Date_of_birth']);
    $current_page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    // Validate mandatory fields
    if (empty($Name) || empty($Gender) || empty($PhoneNo) || empty($Email_address) || empty($Category) || empty($Date_of_birth)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    if (!preg_match('/^\+60\d{2}-\d{3}-\d{4,5}$/', $PhoneNo)) {
        echo "<script>alert('Invalid phone number format. Please use +60xx-xxx-xxxx or +60xx-xxx-xxxxx format.'); window.history.back();</script>";
        exit;
    }

    // Validate email format
    if (!filter_var($Email_address, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address.'); window.history.back();</script>";
        exit;
    }

    // Validate date of birth format (example: YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $Date_of_birth)) {
        echo "<script>alert('Invalid date of birth format. Use YYYY-MM-DD.'); window.history.back();</script>";
        exit;
    }

    // Update the user in the database
    $query = "UPDATE huan_fitness_nutritionist SET 
              Name='$Name', 
              Gender='$Gender', 
              PhoneNo='$PhoneNo', 
              Email_address='$Email_address', 
              Category='$Category',
              Date_of_birth='$Date_of_birth'
              WHERE Nutritionist_ID='$Nutritionist_ID'";

    // Execute the query and check if successful
    if (mysqli_query($con, $query)) {
        echo "<script>alert('User updated successfully.'); window.location.href='Nutritionist_Information.php?page=$current_page';</script>";
    } else {
        echo "<script>alert('Error updating user: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}
?>
