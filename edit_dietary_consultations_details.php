<?php

require_once('database.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $consult_id = mysqli_real_escape_string($con, $_POST['consult_id']);
    $Nutritionist_ID = mysqli_real_escape_string($con, $_POST['Nutritionist_ID']);
    $category = mysqli_real_escape_string($con, $_POST['category']);

    // Validate if the category matches the Nutritionist_ID in huan_fitness_nutritionist table
    $validate_query = "SELECT Category FROM huan_fitness_nutritionist WHERE Nutritionist_ID = '$Nutritionist_ID'";
    $validate_result = mysqli_query($con, $validate_query);

    if (mysqli_num_rows($validate_result) > 0) {
        $row = mysqli_fetch_assoc($validate_result);
        if ($row['Category'] == $category) {
            // Update query if validation is successful
            $update_query = "UPDATE dietary_consultations_details 
                             SET Nutritionist_ID = '$Nutritionist_ID', nutritionist_category = '$category' 
                             WHERE consult_id = '$consult_id'";

            if (mysqli_query($con, $update_query)) {
                echo "<script>alert('Consultation details updated successfully.');</script>";
                echo "<script>window.location.href = 'dietary_consultation_details.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error updating details: " . mysqli_error($con) . "');</script>";
            }
        } else {
            
            echo "<script>alert('This category does not belong to this Nutritionist ID.');</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Nutritionist ID not found.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }
}
?>
