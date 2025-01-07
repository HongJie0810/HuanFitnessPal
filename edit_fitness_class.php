<?php

require_once('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errors = [];
    
    // Validate fitness_class_id
    if (empty($_POST['fitness_class_id'])) {
        $errors[] = 'Fitness class ID is required.';
    } else {
        $fitness_class_id = mysqli_real_escape_string($con, $_POST['fitness_class_id']);
    }

    // Validate fitness_class_category
    if (empty($_POST['fitness_class_category'])) {
        $errors[] = 'Category is required.';
    } else {
        $fitness_class_category = mysqli_real_escape_string($con, $_POST['fitness_class_category']);
    }

    // Validate day
    if (empty($_POST['day'])) {
        $errors[] = 'Day is required.';
    } else {
        $day = mysqli_real_escape_string($con, $_POST['day']);
    }

    // Validate start_time
    if (empty($_POST['start_time'])) {
        $errors[] = 'Start time is required.';
    } else {
        $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
    }

    // Validate end_time
    if (empty($_POST['end_time'])) {
        $errors[] = 'End time is required.';
    } else {
        $end_time = mysqli_real_escape_string($con, $_POST['end_time']);
    }

    
    if (empty($errors)) {
       
        $update_query = "UPDATE fitness_class_details SET 
                            fitness_class_category = '$fitness_class_category', 
                            day = '$day', 
                            start_time = '$start_time', 
                            end_time = '$end_time' 
                        WHERE fitness_class_id = '$fitness_class_id'";

        if (mysqli_query($con, $update_query)) {
            echo "<script>alert('Fitness class updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating class: " . mysqli_error($con) . "');</script>";
        }

        
        echo "<script>window.location.href = 'Fitness_class_details.php?page=" . $_POST['page'] . "';</script>";
        exit(); 
    } else {
        
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>
