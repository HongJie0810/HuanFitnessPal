<?php
// Database connection
require_once('database.php');

// Check if the database connection is successful
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = mysqli_real_escape_string($con, $_POST['member_id']);
    $regDate = mysqli_real_escape_string($con, $_POST['regDate']);

    // Validate mandatory fields and date logic
    if (empty($member_id) || empty($regDate)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    $today = date('Y-m-d');
    if ($regDate < $today) {
        echo "<script>alert('Registration date cannot be in the past.'); window.history.back();</script>";
        exit;
    }

    $exprDate = date('Y-m-d', strtotime($regDate . ' +1 year'));

    // Update the member information in the database (including status to valid)
    $update_query = "UPDATE huan_fitness_members SET regDate = ?, exprDate = ?, status = 'active' WHERE member_id = ?";
    
    if ($stmt = mysqli_prepare($con, $update_query)) {
        mysqli_stmt_bind_param($stmt, 'sss', $regDate, $exprDate, $member_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Membership renewed successfully.'); window.location.href = 'Member_Information.php';</script>";
        } else {
            echo "<script>alert('Error renewing membership: " . mysqli_error($con) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing query: " . mysqli_error($con) . "');</script>";
    }
}

// Close the database connection
mysqli_close($con);
?>
