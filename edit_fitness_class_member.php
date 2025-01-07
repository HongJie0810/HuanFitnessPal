<?php

require_once('database.php');


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate 
    $member_id = mysqli_real_escape_string($con, $_POST['member_id']);
    $fitness_class_id = mysqli_real_escape_string($con, $_POST['fitness_class_id']);

  
    if (empty($member_id) || empty($fitness_class_id)) {
        echo "<script>alert('Member ID and Fitness Class ID are required.');</script>";
        echo "<script>window.location.href = 'Fitness_Class_Member.php';</script>";
        exit();
    }

    // Check the fitness_class_id 
    $query = "SELECT fitness_class_category FROM fitness_class_details WHERE fitness_class_id = '$fitness_class_id'";
    $result = mysqli_query($con, $query);

    
    if (!$result) {
        echo "<script>alert('Database query failed: " . mysqli_error($con) . "');</script>";
        echo "<script>window.location.href = 'Fitness_Class_Member.php';</script>";
        exit();
    }

    if (mysqli_num_rows($result) > 0) {
        // Fetch the category for the fitness_class_id
        $row = mysqli_fetch_assoc($result);
        $category = $row['fitness_class_category'];

        // Update the fitness_class_id and category in the fitness_class_member table
        $update_query = "UPDATE fitness_class_member SET fitness_class_id = '$fitness_class_id', category = '$category' WHERE member_id = '$member_id'";

        if (mysqli_query($con, $update_query)) {
            echo "<script>alert('Fitness Class updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating Fitness Class: " . mysqli_error($con) . "');</script>";
        }
    } else {
        // If fitness_class_id does not exist, show an error
        echo "<script>alert('Unknown Fitness Class ID.');</script>";
    }

   
    echo "<script>window.location.href = 'Fitness_Class_Member.php';</script>";
    exit();
}
?>
