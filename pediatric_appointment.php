<?php
// Database connection
require_once('db_conn.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id === null) {
    header('Location: login.php');
    exit();
}

function getNextConsultID($conn) {
    $query = "SELECT MAX(consult_id) AS max_id FROM dietary_consultations_details";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        $numeric_part = (int)substr($max_id, 4);
        return 'CONS' . str_pad($numeric_part + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'CONS001';
}

function getNextInvoiceNumber($conn) {
    $query = "SELECT MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)) AS max_id FROM payment_information";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        return 'INV-' . str_pad($max_id + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'INV-001';
}


// Get user information
$user_sql = "SELECT user_id, member_id, username FROM huan_fitness_users WHERE user_id = ?";
if ($user_stmt = $conn->prepare($user_sql)) {
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $user_stmt->close();
} else {
    die("Failed to prepare user query: " . $conn->error);
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["nutritionist"]) || empty($_POST["date"]) || empty($_POST["time"]) || empty($_POST["payment_method"])) {
        $error_message = "Please fill in all required information.";
    } else {
        $member_id = isset($user_data['member_id']) ? $user_data['member_id'] : "TEST001";
        $nutritionist_id = $_POST["nutritionist"];
        $date = $_POST["date"];
        $time_slot = $_POST["time"];

        list($start_time, $end_time) = explode('-', $time_slot);

        $availability_query = "SELECT * FROM dietary_consultations_details 
                             WHERE date = ? 
                             AND (
                                (start_time <= ? AND end_time > ?) OR 
                                (start_time < ? AND end_time >= ?) OR 
                                (start_time >= ? AND end_time <= ?)
                             )";
        
        if ($stmt = $conn->prepare($availability_query)) {
            $stmt->bind_param("sssssss", $date, $start_time, $start_time, $end_time, $end_time, $start_time, $end_time);
            $stmt->execute();
            $availability_result = $stmt->get_result();

            if ($availability_result->num_rows > 0) {
                $error_message = "The selected time slot is not available.";
            } else {
                $consult_id = getNextConsultID($conn);

                $insert_query = "INSERT INTO dietary_consultations_details (consult_id, Nutritionist_ID, nutritionist_category, member_id, date, start_time, end_time, request_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                if ($insert_stmt = $conn->prepare($insert_query)) {
                    $nutritionist_category = "Pediatric Nutritionist";
                    $request_status = "Pending";
                    $insert_stmt->bind_param("ssssssss", $consult_id, $nutritionist_id, $nutritionist_category, $member_id, $date, $start_time, $end_time, $request_status);
                    
                    if ($insert_stmt->execute()) {
                        // 处理支付信息
                        $invoice_number = getNextInvoiceNumber($conn);
                        $payment_method = $_POST["payment_method"];
                        $payment_date = date('Y-m-d');
                        $payment_amount = 20;
                        $payment_category = "dietary";

                        $payment_query = "INSERT INTO payment_information (invoice_number, user_id, payment_method, payment_date, payment_amount, payment_category) VALUES (?, ?, ?, ?, ?, ?)";
                        
                        if ($payment_stmt = $conn->prepare($payment_query)) {
                            $payment_stmt->bind_param("sissss", $invoice_number, $user_id, $payment_method, $payment_date, $payment_amount, $payment_category);
                            
                            if ($payment_stmt->execute()) {
                                $_SESSION['last_invoice_number'] = $invoice_number;
                                $success_message = "Appointment and payment successful!";
                                header('Location:receipt.php');
                                exit();
                            } else {
                                $error_message = "Payment processing failed: " . $conn->error;
                            }
                            $payment_stmt->close();
                        } else {
                            $error_message = "Payment statement preparation failed: " . $conn->error;
                        }
                    } else {
                        $error_message = "Appointment Failed, Please try again later. Error: " . $conn->error;
                    }
                    $insert_stmt->close();
                } else {
                    $error_message = "Insertion statement preparation failed: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $error_message = "Query statement preparation failed: " . $conn->error;
        }
    }
}

// Get nutritionist list
$nutritionist_sql = "SELECT Nutritionist_ID, Name FROM huan_fitness_nutritionist WHERE Category = 'Pediatric Nutritionist' ORDER BY Name";
$result = $conn->query($nutritionist_sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutritionist Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(45deg, #f3e5f5, #e1f5fe);
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            flex: 0.7;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .back-btn {
            background-color: #f44336;
            color: white;
            flex: 0.3;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #da190b;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            color: green;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 4px;
            text-align: center;
        }

        .fee-input-group {
            display: flex;
            align-items: center;
        }

        .currency {
            padding: 8px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 4px 0 0 4px;
        }

        #fee {
            border: 1px solid #ddd;
            border-left: none;
            border-radius: 0 4px 4px 0;
            padding: 8px;
            width: 100px;
            background-color: #f5f5f5;
        }

        .payment-options {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .payment-options input[type="radio"] {
            margin-right: 5px;
        }

        .payment-icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
            margin: 0 5px;
        }

        .payment-label {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php if ($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form id="appointmentForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h2>Appointment with Pediatric Nutritionist</h2>

        <div class="form-group">
            <label for="date">Appointment Date:</label>
            <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label for="time">Appointment Time:</label>
            <select id="time" name="time" required>
                <option value="">Choose a Time Slot</option>
                <?php
                $start_time = strtotime('08:30');
                $end_time = strtotime('20:30');
                
                while ($start_time <= $end_time - 3600) { 
                    $time_slot_start = date('H:i', $start_time);
                    $time_slot_end = date('H:i', strtotime('+1 hour', $start_time));
                    $time_slot = $time_slot_start . '-' . $time_slot_end;
                    echo '<option value="' . htmlspecialchars($time_slot) . '">' . htmlspecialchars($time_slot) . '</option>';
                    $start_time = strtotime('+1 hour', $start_time);
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nutritionist">Choose a Nutritionist:</label>
            <select id="nutritionist" name="nutritionist" required>
                <option value="">Select Nutritionist</option>
                <?php 
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['Nutritionist_ID']) . '">' . htmlspecialchars($row['Name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="fee">Consultation Fee:</label>
            <div class="fee-input-group">
                <span class="currency">RM</span>
                <input type="text" id="fee" name="fee" value="20.00" readonly>
            </div>
        </div>

        <div class="form-group">
            <label>Payment Method:</label>
            <div class="payment-options">
                <div class="payment-option">
                    <input type="radio" id="credit" name="payment_method" value="credit card" required>
                    <label for="credit" class="payment-label">
                        <img src="https://cdn-icons-png.flaticon.com/512/16174/16174534.png" alt="Credit Card" class="payment-icon">
                        Credit Card
                    </label>
                </div>

                <div class="payment-option">
                    <input type="radio" id="debit" name="payment_method" value="debit card">
                    <label for="debit" class="payment-label">
                        <img src="https://cdn-icons-png.flaticon.com/512/179/179457.png" alt="Debit Card" class="payment-icon">
                        Debit Card
                    </label>
                </div>

                <div class="payment-option">
                    <input type="radio" id="tng" name="payment_method" value="touch n go">
                    <label for="tng" class="payment-label">
                        <img src="https://cdn.bitrefill.com/content/cn/b_rgb%3A1761b8%2Cc_pad%2Ch_800%2Cw_800/v1630678649/touchngo-malaysia.webp" alt="Touch n Go" class="payment-icon">
                        Touch n Go
                    </label>
                </div>
            </div>
        </div>

        <div class="button-group">
            <input type="submit" value="Submit Appointment" class="submit-btn">
            <a href="consultation_category.php" class="back-btn">Back</a>
        </div>
    </form>
</body>
</html>