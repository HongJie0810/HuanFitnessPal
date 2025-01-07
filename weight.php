<?php
include('db_conn.php');

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id === null) {
    header('Location: login.php');
    exit();
}

// Fetch user's height
$user_sql = "SELECT height FROM huan_fitness_users WHERE user_id = $user_id";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();

if (!$user_data) {
    // Handle the case where the user data is not found
    echo "User not found.";
    exit();
}

$height = $user_data['height']; // Fetch user's height

$records_per_page = 5; // Number of records to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $records_per_page; 

// Fetch total records count for the current user
$total_records_query = "SELECT COUNT(*) FROM weights WHERE user_id = $user_id";
$total_records_result = $conn->query($total_records_query);
$total_records = $total_records_result->fetch_row()[0];

$weights_query = "SELECT * FROM weights WHERE user_id = $user_id ORDER BY date DESC LIMIT $offset, $records_per_page";
$weights_result = $conn->query($weights_query);

$weights = [];
if ($weights_result) {
    while ($row = $weights_result->fetch_assoc()) {
        // Calculate BMI for each record
        if ($height > 0) {
            $row['bmi'] = $row['weight'] / ($height/100 * $height/100);
        } else {
            $row['bmi'] = 0; 
        }
        $weights[] = $row;
    }
}

// Calculate statistics
$highest = $lowest = $totalWeight = $averageWeight = $bmi = 0;
$latestWeight = 0;

if (count($weights) > 0) {
    $weightsArray = array_column($weights, 'weight');
    $highest = max($weightsArray);
    $lowest = min($weightsArray);
    $totalWeight = array_sum($weightsArray);
    $averageWeight = $totalWeight / count($weightsArray);
    $latestWeight = $weights[0]['weight']; // The first entry is the latest due to ORDER BY date DESC

    // Ensure height is not zero
    if ($height > 0) {
        $bmi = $latestWeight / (($height / 100) * ($height / 100)); 
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/weight.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <div class="container add" id="container">
        <div class="brand">
            <h3>Menu</h3>
            <a href="#" id="toggle"><i class="bi bi-list"></i></a>
        </div>
        <div class="user">
            <img src="css/img/dumbbell.png" alt="HuanFitness Logo">
            <div class="name">
                <h3 style = "color:white">HuanFitness</h3>
            </div>
        </div>
        <div class="navbar">
            <ul>
                <li><a href="dashboard.php"><i class="bi bi-house"></i><span>DashBoard</span></a></li>
                <li><a href="user_profile.php"><i class="bi bi-person-circle"></i><span>User Profile</span></a></li>
                <li><a href="weight.php"><i class="bi bi-calendar2-fill"></i><span>Body Weight Record</span></a></li>
                <li><a href="MainWaterCon.php"><i class="bi bi-droplet-fill"></i><span>Water Consumption Record</span></a></li>
                <li><a href="exercise_index.php"><i class="bi bi-radar"></i><span>Exercise Record</span></a></li>
                <li><a href="consultation_category.php"><i class="bi bi-journal-medical"></i><span>Dietary Consultation</span></a></li>
                <li><a href="fitness_class.php"><i class="bi bi-universal-access-circle"></i><span>Fitness Class Registration</span></a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <h1>Body Weight Management System</h1>
        <br>

        <div class="bmi-status" style="background-color: <?= ($bmi < 18.5) ? 'yellow' : (($bmi >= 18.5 && $bmi <= 24.9) ? 'green' : (($bmi >= 25 && $bmi < 30) ? 'yellow' : 'red')) ?>;">
            <p>Your latest BMI: <?= number_format($bmi, 2) ?></p>
            <p>Status: 
                <?= ($bmi < 18.5) ? 'Underweight' : (($bmi >= 18.5 && $bmi <= 24.9) ? 'Normal' : (($bmi >= 25 && $bmi < 30) ? 'Overweight' : 'Obese')) ?>
            </p>
        </div>

    <!-- Form for entering weight -->
    <div class="container1">

        <form id="weightForm" action="insert_weight.php" method="POST">
            <div class="weight-input">
                <label for="weight">Enter Weight (kg):</label>
                <input type="number" name="weight" step="0.1" style="width: 300px;" required>
            
                <label for="date">Select Date:</label>
                <input type="date" name="date" style="width: 300px;" required>
            
                <button type="submit" id="submitWeight">Submit</button>
            </div>
        </form>

        <div class="stats">
            <div class="stats-row">
                <div class="box">Highest Weight: <?= htmlspecialchars($highest) ?> kg</div>
                <div class="box">Lowest Weight: <?= htmlspecialchars($lowest) ?> kg</div>
            </div>
            <div class="average">
                <div class="box">Average Weight: <?= number_format($averageWeight, 2) ?> kg</div>
            </div>
        </div>
    </div>

    <h2>Weight History</h2>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Weight</th>
                <th>BMI</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($weights as $weight): ?>
            <tr>
                <td><?= htmlspecialchars($weight['date']) ?></td>
                <td><?= htmlspecialchars($weight['weight']) ?> kg</td>
                <td><?= number_format($weight['bmi'], 2) ?></td>
                <td>
                    <!-- Edit Button -->
                    <a href="#" class="edit-weight" data-id="<?= htmlspecialchars($weight['id']) ?>" data-weight="<?= htmlspecialchars($weight['weight']) ?>" data-date="<?= htmlspecialchars($weight['date']) ?>"><i class="bi bi-pencil-fill"></i></a>
                </td>
                <td>
                    <!-- Delete Button -->
                    <a href="delete_weight.php?id=<?= htmlspecialchars($weight['id']) ?>" onclick="return confirm('Are you sure you want to delete this record?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination located here, after the table -->
        <div class="pagination">
            <?php
            $total_pages = ceil($total_records / $records_per_page);
            if ($page > 1): ?>
                <a href="?page=<?= $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i; ?>" <?= $i == $page ? 'class="active"' : ''; ?>><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>


   <!-- Modal HTML -->
    <div id="editWeightModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h5>Edit Weight Record</h5>
            <form id="editWeightForm">
                <input type="hidden" name="id" id="weightId">
                <div class="form-group">
                    <label for="weight">Weight (kg):</label>
                    <input type="number" name="weight" id="weight" required>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" required>
                </div>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
    
    <script>
        // Handle form submission for weight entry
    document.getElementById('weightForm').onsubmit = function(e) {
        e.preventDefault(); // Prevent default form submission
        const formData = new FormData(this);
        
        // Perform AJAX request to insert weight record
        fetch('insert_weight.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Weight record added successfully.'); 
                location.reload();
            } else {
                alert('Failed to add weight record.'); 
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the weight record.');
        });
    };
        // Get modal elements
        const modal = document.getElementById('editWeightModal');
        const closeModal = document.getElementById('closeModal');

        // Function to open modal
        function openModal(weightId, weight, date) {
            document.getElementById('weightId').value = weightId;
            document.getElementById('weight').value = weight;
            document.getElementById('date').value = date;
            modal.style.display = 'block'; // Show the modal
        }

        // Function to close modal
        closeModal.onclick = function() {
            modal.style.display = 'none'; // Hide the modal
        }

        // Close modal when clicking outside of modal content
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none'; // Hide the modal
            }
        }

        // Handle form submission
        document.getElementById('editWeightForm').onsubmit = function(e) {
            e.preventDefault(); // Prevent default form submission
            const formData = new FormData(this);
    
            // Perform AJAX request to update the weight record
            fetch('edit_weight.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Weight record updated successfully.');
                    modal.style.display = 'none'; // Hide the modal
                    location.reload(); // Reload to see updated data
                } else {
                    alert('Failed to update weight record.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the weight record.');
            });
        };

        // Attach click event to edit buttons
        document.querySelectorAll('.edit-weight').forEach(button => {
            button.onclick = function() {
                const weightId = this.dataset.id;
                const weight = this.dataset.weight;
                const date = this.dataset.date;
                openModal(weightId, weight, date); // Open the modal with data
            };
        });
    </script>
    <script>
        var toggle = document.getElementById("toggle");
        var container = document.getElementById("container");

        toggle.onclick = function() {
            container.classList.toggle('active');
        }
    </script>
</body>
</html>
