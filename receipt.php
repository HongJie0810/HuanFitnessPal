<?php
require_once('db_conn.php');

// Start session
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$last_invoice_number = isset($_SESSION['last_invoice_number']) ? $_SESSION['last_invoice_number'] : null;

if ($user_id === null || $last_invoice_number === null) {
    header('Location: consultation_category.php');
    exit();
}

// Get payment information
$invoice_query = "SELECT p.*, u.username, u.email_address, u.phone_no 
                 FROM payment_information p
                 JOIN huan_fitness_users u ON p.user_id = u.user_id
                 WHERE p.invoice_number = ?";

if ($stmt = $conn->prepare($invoice_query)) {
    $stmt->bind_param("s", $last_invoice_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_info = $result->fetch_assoc();
    $stmt->close();
    
    if (!$payment_info) {
        die("No payment information found for invoice number: " . htmlspecialchars($last_invoice_number));
    }
    
    unset($_SESSION['last_invoice_number']);
} else {
    die("Payment query failed: " . $conn->error);
}

// Get consultation details
$consult_query = "SELECT d.*, n.Name as nutritionist_name
                 FROM dietary_consultations_details d
                 JOIN huan_fitness_nutritionist n ON d.Nutritionist_ID = n.Nutritionist_ID
                 JOIN huan_fitness_users u ON d.member_id = u.member_id
                 WHERE u.user_id = ?
                 ORDER BY d.date DESC, d.start_time DESC
                 LIMIT 1";

if ($stmt = $conn->prepare($consult_query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $consult_result = $stmt->get_result();
    $consult_info = $consult_result->fetch_assoc();
    $stmt->close();
    
    if (!$consult_info) {
        die("No consultation information found for user ID: " . htmlspecialchars($user_id));
    }
    
    // Debug consult info
    echo "<!-- Consult Info: ";
    print_r($consult_info);
    echo " -->";
} else {
    die("Consultation query failed: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .receipt-header h1 {
            margin: 0;
            color: #333;
        }

        .receipt-details {
            margin-bottom: 30px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }

        .receipt-label {
            font-weight: bold;
            color: #666;
        }

        .receipt-value {
            text-align: right;
        }

        .receipt-total {
            border-top: 2px solid #333;
            margin-top: 20px;
            padding-top: 10px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 0.9em;
        }

        .print-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        @media print {
            .print-button {
                display: none;
            }
            body {
                background-color: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Payment Receipt</h1>
            <p>Huan Fitness Center</p>
        </div>

        <div class="receipt-details">
            <div class="receipt-row">
                <span class="receipt-label">Invoice Number:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($payment_info['invoice_number'] ?? 'N/A'); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Date:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($payment_info['payment_date'] ?? 'N/A'); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Customer Name:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($payment_info['username'] ?? 'N/A'); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Email:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($payment_info['email_address'] ?? 'N/A'); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Phone:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($payment_info['phone_no'] ?? 'N/A'); ?></span>
            </div>
        </div>

        <div class="receipt-details">
            <h3>Appointment Details</h3>
            <div class="receipt-row">
                <span class="receipt-label">Nutritionist:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($consult_info['nutritionist_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Appointment Date:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($consult_info['date'] ?? 'N/A'); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Time:</span>
                <span class="receipt-value">
                    <?php 
                    if (isset($consult_info['start_time']) && isset($consult_info['end_time'])) {
                        echo htmlspecialchars($consult_info['start_time'] . ' - ' . $consult_info['end_time']);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Payment Method:</span>
                <span class="receipt-value"><?php echo htmlspecialchars($payment_info['payment_method'] ?? 'N/A'); ?></span>
            </div>

            <div class="receipt-row receipt-total">
                <span class="receipt-label">Total Amount:</span>
                <span class="receipt-value">RM <?php echo number_format($payment_info['payment_amount'] ?? 0, 2); ?></span>
            </div>
        </div>

        <div class="receipt-footer">
            <p>Thank you for choosing Huan Fitness Center!</p>
            <p>For any inquiries, please contact us at support@huanfitness.com</p>
        </div>
    </div>

    <button class="print-button" onclick="window.print()">Print Receipt</button>
    <a href="consultation_category.php" class="print-button" style="background-color: #f44336;">Back</a>
</body>
</html>