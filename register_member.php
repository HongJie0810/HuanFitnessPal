<style>
    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        font-size: 24px;
    }

    .loading-animation {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<?php
require_once('db_conn.php');

session_start();

// Function to get the next member ID
function getNextMemberId($conn) {
    $query = "SELECT MAX(member_id) AS max_id FROM huan_fitness_members";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        $numeric_part = (int)substr($max_id, 1);
        return 'M' . str_pad($numeric_part + 1, 2, '0', STR_PAD_LEFT);
    }
    return 'M16';
}

// Function to generate a UUID invoice number
function generateUUIDInvoiceNumber() {
    return 'INV-' . bin2hex(random_bytes(16)); // Generates a random unique invoice number
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['reg_date']) && !empty($_POST['reg_date'])) {
    $user_id = $_POST['user_id'];
    $reg_date = $_POST['reg_date'];

    // Calculate the expiration date (one year after registration date)
    $expr_date = date('Y-m-d', strtotime('+1 year', strtotime($reg_date)));
    $status = "active";

    // Generate the new member ID
    $member_id = getNextMemberId($conn);

    // Prepare an insert query to add to huan_fitness_members
    $insert_query = "INSERT INTO huan_fitness_members (member_id, regDate, exprDate, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);

    if (!$stmt) {
        die("Error preparing insert query: " . $conn->error);
    }

    $stmt->bind_param("ssss", $member_id, $reg_date, $expr_date, $status);

    if ($stmt->execute()) {
        $update_query = "UPDATE huan_fitness_users SET member_id = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);

        if ($update_stmt) {
            $update_stmt->bind_param("ss", $member_id, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Generate a new invoice number
            $newInvoiceNumber = generateUUIDInvoiceNumber();
            $payment_date = date('Y-m-d H:i:s');
            $payment_category = "Register membership";
            $payment_amount = 50;

            // Prepare the SQL insert statement for the payments table
            $insertPaymentQuery = "INSERT INTO payment_information (invoice_number, payment_method, payment_date, payment_category, payment_amount, user_id) VALUES (?, ?, ?, ?, ?, ?)";
            $paymentStmt = $conn->prepare($insertPaymentQuery);
            $paymentStmt->bind_param("ssssis", $newInvoiceNumber, $_POST['payment_method'], $payment_date, $payment_category, $payment_amount, $user_id);

            // Execute the insert statement for payments
            if ($paymentStmt->execute()) {
                // Display the loading screen and redirect to receipt.php after 3 seconds
                echo "
                <div class='loading-container'>
                    <div class='loading-animation'></div>
                    <p>Processing Receipt...</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'regmem_receipt.php?member_id=$member_id&user_id=$user_id';
                    }, 3000);
                </script>";
            } else {
                echo "Error recording payment: " . $conn->error;
            }

            $paymentStmt->close();
        } else {
            echo "Error updating huan_fitness_users: " . $conn->error;
        }
    } else {
        echo "Error inserting membership details: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "<script>alert('Please select a registration date.'); window.history.back();</script>";
}

// Close the database connection
$conn->close();
?>