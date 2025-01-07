<?php

require_once('database.php');


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$current_page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate 
    $member_id = mysqli_real_escape_string($con, $_POST['member_id']);
    $regDate = mysqli_real_escape_string($con, $_POST['regDate']);

    // Validate 
    if (empty($member_id) || empty($regDate)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    // Ensure the registration date
    $today = date('Y-m-d');
    if ($regDate < $today) {
        echo "<script>alert('Registration date cannot be in the past.'); window.history.back();</script>";
        exit;
    }

    // Automatically calculate the expiration date
    $exprDate = date('Y-m-d', strtotime($regDate . ' +1 year'));

    // Update the member information in the database 
    $update_query = "UPDATE huan_fitness_members SET regDate = ?, exprDate = ?, status = 'valid' WHERE member_id = ?";
    
    if ($stmt = mysqli_prepare($con, $update_query)) {
        mysqli_stmt_bind_param($stmt, 'sss', $regDate, $exprDate, $member_id);

        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Membership renewed successfully.'); window.location.href = 'Member_Information.php?page=$current_page';</script>";
        } else {
            echo "<script>alert('Error renewing membership: " . mysqli_error($con) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing query: " . mysqli_error($con) . "');</script>";
    }
}


mysqli_close($con);
?>
