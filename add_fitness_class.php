<?php

require_once('database.php');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

function getNextFitnessClassId($con) {
    $query = "SELECT MAX(fitness_class_id) AS max_id FROM fitness_class_details";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        
        $numeric_part = (int)substr($max_id, 1); 
        return 'F' . str_pad($numeric_part + 1, 2, '0', STR_PAD_LEFT); 
    }
    return 'F06'; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errors = [];
    
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
        
        $fitness_class_id = getNextFitnessClassId($con);

        
        $insert_query = "INSERT INTO fitness_class_details (fitness_class_id, fitness_class_category, day, start_time, end_time) 
                        VALUES ('$fitness_class_id', '$fitness_class_category', '$day', '$start_time', '$end_time')";

        if (mysqli_query($con, $insert_query)) {
            echo "<script>alert('Fitness class added successfully.');</script>";
        } else {
            echo "<script>alert('Error adding class: " . mysqli_error($con) . "');</script>";
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
