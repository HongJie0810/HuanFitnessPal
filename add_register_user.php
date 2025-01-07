<?php
require_once('database.php');


if (!$con) {
    error_log("Connection failed: " . mysqli_connect_error());
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

function getNextUserId($con) {
    $query = "SELECT MAX(user_id) AS max_id FROM huan_fitness_users";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    
    return $max_id ? $max_id + 1 : 2303021;
}

// Get the next available user ID
$user_id = getNextUserId($con);

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists using prepared statements
    $check_stmt = $con->prepare("SELECT * FROM huan_fitness_users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
    } else {
        
        $count_query = "SELECT COUNT(*) AS total FROM huan_fitness_users";
        $count_result = mysqli_query($con, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $total_records = $count_row['total'];

        // Insert new user into the database without hashing the password using prepared statement
        $insert_stmt = $con->prepare("INSERT INTO huan_fitness_users (user_id, username, password) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iss", $user_id, $username, $password); 
        if ($insert_stmt->execute()) {
            
            $results_per_page = 5; 
            $new_total_records = $total_records + 1; 
            $page_number = ceil($new_total_records / $results_per_page);

            
            echo json_encode(['success' => true, 'page' => $page_number, 'user_id' => $user_id]);
        } else {
            error_log("Insert Error: " . $insert_stmt->error);
            echo json_encode(['success' => false, 'message' => 'Error registering user.']);
        }
    }
    $check_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
}

mysqli_close($con);
?>
