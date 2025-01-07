<style>
    .receipt-container {
    max-width: 600px;
    margin: 100px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.next-button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.next-button:hover {
    background-color: #2980b9;
}

</style>


<?php
require_once('db_conn.php');
session_start();

// Retrieve and sanitize inputs
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
$payment_amount = '20.00'; // Fixed payment amount without "RM" for compatibility with decimal types
$payment_date = date('Y-m-d');
$payment_status = 'paid'; // Set payment status to 'paid'
$payment_category = 'renew'; // Define the payment category

// Check if user_id and payment_method are provided
if (!$user_id || !$payment_method) {
    die("User ID and Payment Method are required.");
}

// Fetch user details from the database
$query = "SELECT username, email_address, phone_no, member_id FROM huan_fitness_users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_details = $result->fetch_assoc();

if (!$user_details) {
    die("User not found.");
}

// Extract user details
$customer_name = $user_details['username'];
$email = $user_details['email_address'];
$phone_no = $user_details['phone_no'];
$member_id = $user_details['member_id']; // Retrieve member_id for updating status

if (!$member_id) {
    die("Error: No membership found for this user.");
}

// Generate Invoice Number
$invoice_number = "INV-" . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

// Insert payment record into the database
$insert_payment_query = "INSERT INTO payment_information (user_id, payment_amount, payment_method, payment_date, payment_category, invoice_number) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($insert_payment_query);

// Bind parameters and execute the statement
$stmt_insert->bind_param("idssss", $user_id, $payment_amount, $payment_method, $payment_date, $payment_category, $invoice_number);
$stmt_insert->execute();

if ($stmt_insert->error) {
    die("Error inserting payment record: " . $stmt_insert->error);
}

// Update the membership status and payment status
$update_status_query = "UPDATE huan_fitness_members 
                        SET status = 'active', payment_status = 'paid'
                        WHERE member_id = ?";
$stmt_update = $conn->prepare($update_status_query);
$stmt_update->bind_param("i", $member_id);
$stmt_update->execute();

if ($stmt_update->error) {
    die("Error updating membership status: " . $stmt_update->error);
}

// Close statements
$stmt->close();
$stmt_insert->close();
$stmt_update->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="css/receipt.css">
</head>
<body>
    <div class="receipt-container">
        <h2>Membership Registration Receipt</h2>
        <p>Huan Fitness Center</p>
        <hr>

            <p><strong>Invoice Number:</strong> <?php echo $invoice_number; ?></p>
            <p><strong>Payment Date:</strong> <?php echo $payment_date; ?></p>
            <p><strong>Customer Name:</strong> <?php echo $customer_name; ?></p>
            <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Phone Number:</strong> <?php echo $phone_no; ?></p>
            <p><strong>Payment Method:</strong> <?php echo ucfirst($payment_method); ?></p>
            <div class="total-price">Payment Amount: <?php echo $payment_amount; ?></div>

        <button onclick="window.location.href='dashboard.php'" class="next-button">Next</button>
    </div>
</body>
</html>