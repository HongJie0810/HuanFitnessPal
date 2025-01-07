<?php
require_once('db_conn.php');

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Renewal</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/membership.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .main-content {
            height: calc(100vh - 100px);
        }
        
        .payment-button {
            border: none;
            background: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 5px;
        }

        .payment-button.selected {
            border: 2px solid #007bff; /* Highlight color */
            background-color: black; /* Background color when selected */
            border-radius: 5px;
        }

        .payment-icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="container add" id="container">
        <div class="brand">
            <h3>Menu</h3>
            <a href="#" id="toggle"><i class="bi bi-list"></i></a>
        </div>
        <div class="user">
            <img src="css/img/dumbbell.png" alt="">
            <div class="name">
                <h3>HuanFitnessPal</h3>
            </div>
        </div>
        <div class="navbar">
            <ul>
                <li><a href="dashboard.php"><i class="bi bi-house"></i><span>DashBoard</span></a></li>
                <li><a href="user_profile.php"><i class="bi bi-person-circle"></i><span>User Profile</span></a></li>
                <li><a href="weight.php"><i class="bi bi-3-square-fill"></i><span>Body Weight Record</span></a></li>
                <li><a href="#"><i class="bi bi-person-fill"></i><span>Water Consumption Record</span></a></li>
                <li><a href="exercise_index.php"><i class="bi bi-folder"></i><span>Exercise Record</span></a></li>
                <li><a href="#"><i class="bi bi-journal-medical"></i><span>Dietary Consultation</span></a></li>
                <li><a href="#"><i class="bi bi-journal-medical"></i><span>Fitness Class Registration</span></a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="membership-section">  
            <span class="close-btn" style="font-size:30px; cursor: pointer;" onclick="closeEdit()">×</span>
            <h2>Renew Membership</h2>

            <!-- Display Membership Benefits -->
            <div class="membership-benefits">
                <h3>Membership Benefits:</h3><br>
                <ul>
                    <li>Access to nutritionist appointments</li><br>
                    <li>Join exclusive fitness classes</li><br>
                    <li>Memberships are valid for a one-year period from the date of registration.</li><br>
                    <li>Huan Fitness renew membership per month: RM20</li>
                </ul>
            </div>

            <!-- Membership Registration Form -->
            <form id="membershipForm" method="POST" action="renmem_receipt.php" onsubmit="return confirmRenewal();">
                <label for="user_id">User ID</label>
                <input type="text" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" readonly>

                <label for="reg_date">Renew Date</label>
                <input type="text" id="reg_date" name="reg_date" placeholder="Select a date">

                <!-- Payment Options -->
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="" disabled selected>Select a payment method</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="online_banking">Online Banking</option>
                    <option value="ewallet">eWallet</option>
                </select>

                <div class="total-price" style="margin-top: 1em; font-weight: bold; color: #333;">
                    Total Price: RM20
                </div>

                <button type="submit" class="membership-button">Renew Membership</button>
            </form>

        <!-- Flatpickr Date Picker Script -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr("#reg_date", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                minDate: "today"
            });

            function closeEdit() {
                var editContainer = document.getElementById('editContainer');
                if (editContainer) {
                    editContainer.style.display = 'none'; 
                }
                window.location.href = 'dashboard.php';  
            }

            function confirmRenewal() {
            const paymentMethod = document.getElementById('payment_method').value; // Get selected payment method
            if (!paymentMethod) {
                alert("Please select a payment method."); // Alert if no payment method is selected
                return false; // Prevent form submission
            }

            const userConfirmed = window.confirm("Renew membership price: RM20. \nAre you sure you want to renew your membership?"
                
            );
            return userConfirmed; // Proceed if user confirms
        }

        function selectPayment(method, button) {
            // Highlight the selected payment option
            const buttons = document.querySelectorAll('.payment-button');
            buttons.forEach(btn => {
                btn.classList.remove('selected'); // Remove highlight from all buttons
            });

            button.classList.add('selected'); // Add highlight to the selected button

            document.getElementById('payment_method').value = method; // Set the selected payment method
            console.log("Payment method selected:", method); // Debugging to confirm selection
        }

        </script>

                </div>  
            </div>
        </body>
     </html>