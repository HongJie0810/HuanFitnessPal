<?php
// user_renew_member.php

session_start();
include('db_conn.php');  // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user ID and new registration date from the form
    $user_id = $_POST['user_id'];
    $reg_date = $_POST['reg_date'];

    if (empty($user_id) || empty($reg_date)) {
        die('Error: All fields are required.');
    }

    $current_date = date('Y-m-d');
    if ($reg_date < $current_date) {
        die('Error: Renewal date cannot be earlier than today.');
    }

    $expiration_date = date('Y-m-d', strtotime($reg_date . ' + 1 year'));

    $query = "SELECT member_id FROM huan_fitness_users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('i', $user_id); 
    $stmt->execute();
    $stmt->bind_result($member_id);
    $stmt->fetch();
    $stmt->close();

    if (empty($member_id)) {
        die('Error: No member found for this user ID.');
    }

    // Update the membership information in the database
    $query = "UPDATE huan_fitness_members 
              SET regDate = ?, exprDate = ?, status = 'active', payment_status = 'paid' 
              WHERE member_id = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('sss', $reg_date, $expiration_date, $member_id);

    // Execute the update
    if ($stmt->execute()) {
        echo '<script>window.location.href="renmem_receipt.php";</script>';
        exit;
    } else {
        die('Error updating membership: ' . $stmt->error);
    }

    $stmt->close();
} else {
    header('Location: user_renew_membership.php');
    exit;
}
?>
