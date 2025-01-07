<?php

require_once('database.php');


function getNextConsultID($con) {
    $query = "SELECT MAX(consult_id) AS max_id FROM dietary_consultations_details";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

if ($max_id) {

        $numeric_part = (int)substr($max_id, 4); 
        return 'CONS' . str_pad($numeric_part + 1, 3, '0', STR_PAD_LEFT); 
    }
    return 'CONS004'; 
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $consult_id = getNextConsultID($con); // Get the next consult_id
    $Nutritionist_ID = mysqli_real_escape_string($con, $_POST['Nutritionist_ID']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $member_id = mysqli_real_escape_string($con, $_POST['member_id']);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($con, $_POST['end_time']);

   
    $member_query = "SELECT * FROM huan_fitness_members WHERE member_id = '$member_id'";
    $member_result = mysqli_query($con, $member_query);

    if (mysqli_num_rows($member_result) == 0) {
        echo "<script>alert('Member ID does not exist.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }

   
    $nutritionist_query = "SELECT Category FROM huan_fitness_nutritionist WHERE Nutritionist_ID = '$Nutritionist_ID'";
    $nutritionist_result = mysqli_query($con, $nutritionist_query);

    if (mysqli_num_rows($nutritionist_result) > 0) {
        $row = mysqli_fetch_assoc($nutritionist_result);
        if ($row['Category'] != $category) {
            echo "<script>alert('This category does not belong to this Nutritionist ID.');</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Nutritionist ID not found.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }

    // Check for availability of date
    $availability_query = "SELECT * FROM dietary_consultations_details 
                           WHERE date = '$date' 
                           AND (
                               (start_time <= '$start_time' AND end_time > '$start_time') OR 
                               (start_time < '$end_time' AND end_time >= '$end_time') OR 
                               (start_time >= '$start_time' AND end_time <= '$end_time')
                           )";
    $availability_result = mysqli_query($con, $availability_query);

    if (mysqli_num_rows($availability_result) > 0) {
        echo "<script>alert('The selected time slot is not available.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }

   
    $insert_query = "INSERT INTO dietary_consultations_details (consult_id, Nutritionist_ID, nutritionist_category, member_id, date, start_time, end_time, request_status) 
                     VALUES ('$consult_id', '$Nutritionist_ID', '$category', '$member_id', '$date', '$start_time', '$end_time', 'approved')";

    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('New consultation details added successfully.');</script>";
        echo "<script>window.location.href = 'dietary_consultation_details.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error adding consultation details: " . mysqli_error($con) . "');</script>";
    }
}
?>
