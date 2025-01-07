    <?php
    
    require_once('database.php');

    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Function to get the next available member ID for huan_fitness_members table
    function getNextUserId($con) {
        $query = "SELECT MAX(member_id) AS max_id FROM huan_fitness_members";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];

        if ($max_id) {
           
            $numeric_part = (int)substr($max_id, 1); 
            return 'M' . str_pad($numeric_part + 1, 2, '0', STR_PAD_LEFT); 
        }
        return 'M16'; 
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate 
        $username = strtolower(trim($_POST['username']));
        $email_address = strtolower(trim($_POST['email_address']));
        $phone_no = trim($_POST['phone_no']);
        $current_page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

        // Validate mandatory fields
        if (empty($username) || empty($email_address) || empty($phone_no)) {
            echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
            exit;
        }

        // Check if the user exists in huan_fitness_users and has no member_id
        $check_user_query = "SELECT member_id FROM huan_fitness_users WHERE LOWER(email_address) = ? AND phone_no = ? AND LOWER(username) = ?";
        if ($stmt = mysqli_prepare($con, $check_user_query)) {
            mysqli_stmt_bind_param($stmt, 'sss', $email_address, $phone_no, $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
               
                if (empty($row['member_id'])) {
                    $user_id = getNextUserId($con);
                    $regDate = date('Y-m-d'); 
                    $exprDate = date('Y-m-d', strtotime('+1 year'));
                    $status = 'active'; 

                    // Insert the new user into huan_fitness_members
                    $insert_query = "INSERT INTO huan_fitness_members (member_id, regDate, exprDate, status) VALUES (?, ?, ?, ?)";
                    if ($stmt2 = mysqli_prepare($con, $insert_query)) {
                        mysqli_stmt_bind_param($stmt2, 'ssss', $user_id, $regDate, $exprDate, $status);

                        if (mysqli_stmt_execute($stmt2)) {
                            // Update the member_id in huan_fitness_users
                            $update_user_query = "UPDATE huan_fitness_users SET member_id = ? WHERE LOWER(email_address) = ? AND phone_no = ? AND LOWER(username) = ?";
                            if ($stmt3 = mysqli_prepare($con, $update_user_query)) {
                                mysqli_stmt_bind_param($stmt3, 'ssss', $user_id, $email_address, $phone_no, $username);
                                mysqli_stmt_execute($stmt3);
                                mysqli_stmt_close($stmt3);

                                echo "<script>alert('Member successfully registered.'); window.location.href = 'Member_Information.php?page=$current_page';</script>";
                            } else {
                                echo "<script>alert('Error updating user member_id: " . mysqli_error($con) . "');</script>";
                            }
                        } else {
                            echo "<script>alert('Error adding user: " . mysqli_error($con) . "');</script>";
                        }

                        mysqli_stmt_close($stmt2);
                    }

                
                } else {
                    echo "<script>alert('Member already exists.'); window.history.back();</script>";
                }

            } else {
                
                echo "<script>alert('Please register the user account first before adding the member.'); window.history.back();</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error preparing query: " . mysqli_error($con) . "');</script>";
        }
    }

    
    mysqli_close($con);
    ?>
