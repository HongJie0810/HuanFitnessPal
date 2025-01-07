<?php
include('db_conn.php'); 
session_start(); 

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fitness_class_id = $_POST['class_id'];
    $category = $_POST['category'];

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id === null) {
        echo "<script>alert('User not logged in. Please log in to enroll.'); window.location.href = 'login.php';</script>";
        exit();
    }

    $memberQuery = "SELECT member_id FROM huan_fitness_users WHERE user_id = ?";
    $member_stmt = $conn->prepare($memberQuery);
    $member_stmt->bind_param('s', $user_id);
    $member_stmt->execute();
    $memberResult = $member_stmt->get_result();

    if ($memberResult->num_rows > 0) {
        $member_row = $memberResult->fetch_assoc();
        $member_id = $member_row['member_id']; // Get the member_id

        // Check if the user is already enrolled in this specific class
        $checkQuery = "SELECT * FROM fitness_class_member WHERE member_id = ? AND fitness_class_id = ?";
        $check_stmt = $conn->prepare($checkQuery);
        $check_stmt->bind_param('ss', $member_id, $fitness_class_id);
        $check_stmt->execute();
        $checkResult = $check_stmt->get_result();

        if ($checkResult->num_rows > 0) {
            // User is already enrolled in this class
            echo "<script>alert('You are already enrolled in this class.'); window.history.back();</script>";
        } else {
            // Proceed to enroll the user in the class with status 'pending'
            $enrollQuery = "INSERT INTO fitness_class_member (member_id, fitness_class_id, category, request_status) VALUES (?, ?, ?, 'pending')";
            $enroll_stmt = $conn->prepare($enrollQuery);
            $enroll_stmt->bind_param('sss', $member_id, $fitness_class_id, $category);
            $enrollResult = $enroll_stmt->execute();

            if (!$enrollResult) {
                die("Enrollment failed: " . $conn->error);
            } else {
                echo "<script>alert('Your fitness class request is successfully send to the admin!'); window.location.href = 'fitness_class.php';</script>";
            }
        }
    } else {
        echo "<script>alert('No member found.'); window.history.back();</script>";
    }
} else {
    header("Location: fitness_class.php");
    exit();
}
?>
