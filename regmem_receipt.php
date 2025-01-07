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

if (isset($_GET['member_id'], $_GET['user_id'])) {
    $member_id = $_GET['member_id'];
    $user_id = $_GET['user_id'];

    // Query to retrieve membership details
    $query = "SELECT * FROM huan_fitness_members WHERE member_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $membership = $result->fetch_assoc();
    $stmt->close();

    // Function to generate a UUID invoice number
    function generateUUIDInvoiceNumber() {
        return 'INV-' . bin2hex(random_bytes(16)); // Generates a random unique invoice number
    }
    
    // Generate a new invoice number
    $newInvoiceNumber = generateUUIDInvoiceNumber();

    // SQL update query
    $updateQuery = "UPDATE payment_information SET 
                        invoice_number = ?, 
                        payment_method = ?, 
                        payment_date = ?, 
                        payment_category = ?, 
                        payment_amount = ?
                    WHERE user_id = ?";
    
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssiss", $newInvoiceNumber, $payment_method, $payment_date, $payment_category, $payment_amount, $user_id);

    $updateStmt->close();
} else {
    echo "<script>alert('Invalid access.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

// Close the database connection
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

        <p><strong>Invoice number:</strong><?php echo $newInvoiceNumber ?></p>
        <p><strong>Member ID:</strong> <?php echo $membership['member_id']; ?></p>
        <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
        <p><strong>Registration Date:</strong> <?php echo $membership['regDate']; ?></p>
        <p><strong>Expiration Date:</strong> <?php echo $membership['exprDate']; ?></p>
        <p><strong>Status:</strong> <?php echo $membership['status']; ?></p>

        <button onclick="window.location.href='dashboard.php'" class="next-button">Next</button>
    </div>
</body>
</html>