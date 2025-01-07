<?php
include('db_conn.php');

session_start(); 

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$is_member = false;          
$is_expired_member = false; 

if ($user_id) {
    // Check if there's an associated member_id for this user in huan_fitness_users
    $member_query = "SELECT member_id FROM huan_fitness_users WHERE user_id = ?";
    $stmt = $conn->prepare($member_query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    // If a member_id exists, the user may be a member or expired member
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($member_id);
        $stmt->fetch();
        $stmt->close(); 

        if (!empty($member_id)) {
            // Check the status of the member
            $status_query = "SELECT status FROM huan_fitness_members WHERE member_id = ?";
            $status_stmt = $conn->prepare($status_query);
            if (!$status_stmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $status_stmt->bind_param("s", $member_id); 
            $status_stmt->execute();
            $status_stmt->store_result();
            
            if ($status_stmt->num_rows > 0) {
                $status_stmt->bind_result($status);
                $status_stmt->fetch();
                
                // Determine if the member is active or expired
                if ($status === 'expired') {
                    $is_expired_member = true;
                } else {
                    $is_member = true; // Active member
                }
            }
            $status_stmt->close(); 
        }
    }
}

// Fetch the latest 5 weight records for the user
$weight_query = "SELECT weight, date FROM weights WHERE user_id = ? ORDER BY date DESC LIMIT 5";
$weight_stmt = $conn->prepare($weight_query);
$weight_stmt->bind_param("i", $user_id);
$weight_stmt->execute();
$weight_result = $weight_stmt->get_result();

// Initialize arrays to hold weight data
$weight_values = [];
$weight_dates = [];

// Fetch data into arrays
while ($row = $weight_result->fetch_assoc()) {
    $weight_values[] = $row['weight'];
    $weight_dates[] = $row['date'];
}

$current_date = date('Y-m-d'); // Format: YYYY-MM-DD

// Fetch today's total water consumption
$total_water_query = "SELECT SUM(amount) AS total_consumed FROM water_consumption WHERE user_id = ? AND DATE(date) = ?";
$total_water_stmt = $conn->prepare($total_water_query);
$total_water_stmt->bind_param("is", $user_id, $current_date);
$total_water_stmt->execute();
$total_water_result = $total_water_stmt->get_result();

$water_consumed = 0; // Default to 0 if no records are found

if ($total_water_result) {
    $total_water_row = $total_water_result->fetch_assoc();
    $water_consumed = $total_water_row['total_consumed'] ?? 0; // Default to 0 if null
}

// Fetch the user's daily goal from the water_consumption table
$goal_query = "SELECT goal_amount FROM water_consumption WHERE user_id = ? LIMIT 1"; 
$goal_stmt = $conn->prepare($goal_query);
$goal_stmt->bind_param("i", $user_id);
$goal_stmt->execute();
$goal_result = $goal_stmt->get_result();

$goal_amount = 0; // Default to 0 if no records are found

if ($goal_result) {
    $goal_row = $goal_result->fetch_assoc();
    $goal_amount = $goal_row['goal_amount'] ?? 0; // Default to 0 if null
}

// Prepare the data for the chart (fetch the last 5 days with summed amounts)
$water_intake_query = "
    SELECT DATE(date) AS date, SUM(amount) AS total_amount 
FROM water_consumption 
WHERE user_id = ? 
GROUP BY DATE(date) 
ORDER BY DATE(date) ASC;
";

$water_intake_stmt = $conn->prepare($water_intake_query);
$water_intake_stmt->bind_param("i", $user_id);
$water_intake_stmt->execute();
$water_intake_result = $water_intake_stmt->get_result();

$water_dates = [];
$water_intake_values = [];

while ($row = $water_intake_result->fetch_assoc()) {
    $water_dates[] = $row['date'];
    $water_intake_values[] = $row['total_amount'];
}

// If the user did not input water for the last 5 days, fill the graph accordingly
if (count($water_dates) < 5) {
    $today = new DateTime();
    $interval = new DateInterval('P1D'); // 1 day interval

    // Start from today and go back for 5 days
    for ($i = 0; $i < 5; $i++) {
        $date = $today->modify('-1 day')->format('Y-m-d');
        // Only add to arrays if date exists in fetched results
        if (in_array($date, $water_dates)) {
            $index = array_search($date, $water_dates);
            $water_intake_values[] = $water_intake_values[$index];
        } else {
            // If there are no entries for that date, we can just add 0
            $water_intake_values[] = 0; // Default for empty days
        }
        $water_dates[] = $date;
    }
}

// Reverse the arrays to show the latest dates first
$water_dates = array_reverse($water_dates);
$water_intake_values = array_reverse($water_intake_values);

// Remove empty dates: only keep actual entries
$final_dates = [];
$final_values = [];

foreach ($water_dates as $index => $date) {
    if ($water_intake_values[$index] > 0) {
        $final_dates[] = $date;
        $final_values[] = $water_intake_values[$index];
    }
}

// Fetch today's exercise records
$current_date = date('Y-m-d'); // Get the current date in Y-m-d format
$exercise_query = "SELECT name, minutes, sets FROM exercises WHERE user_id = ? AND DATE(date) = ?";
$exercise_stmt = $conn->prepare($exercise_query);
$exercise_stmt->bind_param("is", $user_id, $current_date);
$exercise_stmt->execute();
$exercise_result = $exercise_stmt->get_result();

// Initialize array to hold today's exercise records
$today_exercises = [];

// Fetch data into array
while ($row = $exercise_result->fetch_assoc()) {
    $today_exercises[] = $row; // Store the whole row (name and date)
}

// Query to get the member ID from huan_fitness_users table
$query_member_id = "SELECT member_id FROM huan_fitness_users WHERE user_id = ?";
$stmt_member = $conn->prepare($query_member_id);
$stmt_member->bind_param("i", $user_id);
$stmt_member->execute();
$result_member = $stmt_member->get_result();
$member = $result_member->fetch_assoc();

// Step 1: Query to get the member ID from huan_fitness_users table
$query_member_id = "SELECT member_id FROM huan_fitness_users WHERE user_id = ?";
$stmt_member = $conn->prepare($query_member_id);
$stmt_member->bind_param("i", $user_id);
$stmt_member->execute();
$result_member = $stmt_member->get_result();
$member = $result_member->fetch_assoc();

// Step 2: Query to get upcoming nutritionist meetings
$query_meetings = "SELECT date, start_time, end_time, nutritionist_category, request_status 
                   FROM dietary_consultations_details 
                   WHERE member_id = ? AND date >= CURDATE() 
                   ORDER BY date ASC";
$stmt_meetings = $conn->prepare($query_meetings);
$stmt_meetings->bind_param("s", $member_id); // Ensure member_id is treated as string if it's not an integer
$stmt_meetings->execute();
$result_meetings = $stmt_meetings->get_result();

// Fetching the meetings
$meetings = [];
while ($row = $result_meetings->fetch_assoc()) {
    $meetings[] = $row; // Store each meeting record
}

// Get Joined Fitness Classes Data
$fitness_query = "
    SELECT fcm.category, fcm.request_status, fcd.day, fcd.start_time, fcd.end_time 
    FROM fitness_class_member fcm
    JOIN fitness_class_details fcd ON fcm.fitness_class_id = fcd.fitness_class_id
    WHERE fcm.member_id = ?";  // Assuming you have the $member_id

$fitness_stmt = $conn->prepare($fitness_query);
$fitness_stmt->bind_param("s", $member_id);
$fitness_stmt->execute();
$fitness_result = $fitness_stmt->get_result();

// Fetching the fitness class data
$fitness_classes = [];
while ($row = $fitness_result->fetch_assoc()) {
    $fitness_classes[] = $row;
}


// Close the statements
$weight_stmt->close();
$exercise_stmt->close();
$total_water_stmt->close();
$goal_stmt->close();
$stmt_member->close();
$stmt_meetings->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/dashboard1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>
</head>
<body style = "background: linear-gradient(45deg, #f3e5f5, #e1f5fe);">
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
                    
                <!-- Dietary Consultation link -->
                <li><a href="<?php
                        if (!$is_member && !$is_expired_member) {
                            echo 'membership.php';
                        } elseif ($is_expired_member) {
                            echo 'user_renew_membership.php';
                        } else {
                             echo 'consultation_category.php';
                        }
                    ?>">
                        <i class="bi bi-journal-medical"></i><span>Dietary Consultation</span>
                    </a>
                </li>

                <!-- Fitness Class Registration link -->
                <li>
                    <a href="<?php
                        if (!$is_member && !$is_expired_member) {
                            echo 'membership.php';
                        } elseif ($is_expired_member) {
                            echo 'user_renew_membership.php';
                        } else {
                            echo 'fitness_class.php';
                        }
                    ?>">
                        <i class="bi bi-journal-medical"></i><span>Fitness Class Registration</span>
                    </a>
                </li>

                    <li><a href="logout.php"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h2>Huan Fitness Dashboard</h2>
        <div class="first-row">
        <div class="card current-weight">
            <h2 style="font-size:20px;">Current Weight</h2>
            <p id="weight-value"><?= !empty($weight_values) ? htmlspecialchars($weight_values[0]) : 'No weight data available' ?> kg</p>
        </div>
            
        <div class="card daily-water-intake">
            <h2 style="font-size:20px;">Daily Water Intake</h2>
            <div>
                <p>Goal: <span id="goal"><?= htmlspecialchars($goal_amount) ?></span> ml</p>
                <p>Consumed: <span id="consumed"><?= htmlspecialchars($water_consumed) ?></span> ml</p>
                <div class="progress-bar">
                    <div class="progress" id="progress" style="width: <?= $goal_amount > 0 ? ($water_consumed / $goal_amount) * 100 : 0 ?>%;"></div>
                </div>
            </div>
        </div>

    <div class="card exercise-records">
        <h2 style="font-size:20px;" >Today's Exercise Records</h2>
        <ul>
            <?php if (!empty($today_exercises)): ?>
                <?php foreach ($today_exercises as $exercise): ?>
                    <li>
                        <span><?= htmlspecialchars($exercise['name']) ?></span>
                        <span class="date">
                            <?php if (!empty($exercise['minutes'])): ?>
                                <?= htmlspecialchars($exercise['minutes']) ?> minutes
                            <?php elseif (!empty($exercise['sets'])): ?>
                                <?= htmlspecialchars($exercise['sets']) ?> sets
                            <?php else: ?>
                                No data
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No exercises recorded for today.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="card member">
        <?php if (!$is_member && !$is_expired_member):?>    
            <div class="membership-request">
                <h2 style="font-size:20px;">Become a Member!</h2>
                    <form action="membership.php" method="get">
                        <button class="open-modal-button" type="submit">Join Now</button>
                    </form>
                </div>
            <?php elseif ($is_expired_member):  ?>
                <div class="membership-request">
                <h2 style="font-size:20px;">Renew Membership!</h2>
                    <form action="user_renew_membership.php" method="get">
                        <button class="open-modal-button" type="submit">Renew Now</button>
                    </form>
                </div>
            <?php endif;  ?>
        </div>
    </div>
    
        
    <!-- Second Row: Charts -->
    <div class="second-row">
        <div class="chart-box">
            <canvas id="weightChart"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="waterChart"></canvas>
        </div>
    </div>
        
    <!-- Third Row: Modal Trigger Button -->
    <div class="third-row">
        <button class="open-modal-button" onclick="openNutritionModal()">Upcoming Meetings</button>
        <button class="open-modal-button" style="padding-left: 20px;" onclick="openFitnessModal()">Joined Fitness Class</button>
    </div>
</div>


<!-- Meeting Modal Structure -->
<div id="nutritionModal" class="edit-container" style="display: none;">
    <div class="edit-content" style="background-color: white; padding: 20px 30px; border-radius: 10px; max-width: 600px; width: 100%; left: 550px; top: 50px; max-height: 80vh; overflow-y: auto;">
        <span class="close-btn" style="position: absolute; font-size: 40px; top: 10px; right: 20px; cursor: pointer;" onclick="closeModal('nutritionModal')">×</span>
        <h2>Upcoming Nutritionist Meetings</h2><br><br>
        <div class="meeting-cards-container">
            <?php if (count($meetings) > 0): ?>
                <?php foreach ($meetings as $meeting): ?>
                    <div class="meeting-card">
                        <h3><?php echo htmlspecialchars($meeting['date']); ?></h3>
                        <p><strong>Start Time:</strong> <?php echo htmlspecialchars($meeting['start_time']); ?></p>
                        <p><strong>End Time:</strong> <?php echo htmlspecialchars($meeting['end_time']); ?></p>
                        <p><strong>Dietary Category:</strong> <?php echo htmlspecialchars($meeting['nutritionist_category']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="<?php echo 'status-' . strtolower(htmlspecialchars($meeting['request_status'])); ?>">
                                <?php echo htmlspecialchars($meeting['request_status']); ?>
                            </span>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming nutritionist meetings scheduled.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Fitness Classes Modal Structure -->
<div id="fitnessModal" class="edit-container" style="display: none;">
    <div class="edit-content" style="background-color: white; padding: 20px 30px; border-radius: 10px; max-width: 600px; width: 100%; left: 550px; top: 50px; max-height: 80vh; overflow-y: auto;">
        <span class="close-btn" style="position: absolute; font-size: 40px; top: 10px; right: 20px; cursor: pointer;" onclick="closeModal('fitnessModal')">×</span>
        <h2>Joined Fitness Classes</h2><br><br>
        <div class="meeting-cards-container">
            <?php if (count($fitness_classes) > 0): ?>
                <?php foreach ($fitness_classes as $class): ?>
                    <div class="meeting-card">
                        <h3><?php echo htmlspecialchars($class['day']); ?></h3>
                        <p><strong>Start Time:</strong> <?php echo htmlspecialchars($class['start_time']); ?></p>
                        <p><strong>End Time:</strong> <?php echo htmlspecialchars($class['end_time']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($class['category']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="<?php echo 'status-' . strtolower(htmlspecialchars($class['request_status'])); ?>">
                                <?php echo htmlspecialchars($class['request_status']); ?>
                            </span>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No joined fitness classes scheduled.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

    </div>
</div>



        </div>
    </div>

    <script>
        var toggle = document.getElementById("toggle");
		var container = document.getElementById("container");

		toggle.onclick = function() {
			container.classList.toggle('active');
		}

        // Chart.js for Weight Chart Left
const ctxWeight = document.getElementById('weightChart').getContext('2d');
const weightChart = new Chart(ctxWeight, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_reverse($weight_dates)) ?>,
        datasets: [{
            label: 'Weight (kg)',
            data: <?= json_encode(array_reverse($weight_values)) ?>,
            borderColor: 'rgba(0, 123, 255, 1)',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointBackgroundColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 20,
                    padding: 15
                }
            },
            tooltip: {
                enabled: true,
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: false,
                grid: {
                    color: 'rgba(0, 123, 255, 0.2)',
                    lineWidth: 1
                },
                title: {
                    display: true,
                    text: 'Weight (kg)',
                    font: {
                        size: 16
                    }
                }
            }
        }
    }
});

const ctxWater = document.getElementById('waterChart').getContext('2d');
    const waterChart = new Chart(ctxWater, {
        type: 'bar',
        data: {
            labels: <?= json_encode($final_dates) ?>,
            datasets: [{
                label: 'Water Intake (mL)',
                data: <?= json_encode($final_values) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)', 
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5, 
                hoverBackgroundColor: 'rgba(54, 162, 235, 0.8)',
                hoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 20,
                        padding: 15
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(54, 162, 235, 0.2)',
                        lineWidth: 1
                    },
                    title: {
                        display: true,
                        text: 'Water Intake (mL)',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        }
    });
    function openNutritionModal() {
    document.getElementById('nutritionModal').style.display = 'block';
}

function openFitnessModal() {
    document.getElementById('fitnessModal').style.display = 'block';
}

function closeModal(modalId) {
    console.log("closeModal called for:", modalId); // Debugging line
    document.getElementById(modalId).style.display = 'none';
}


// Close the modal when clicking outside of it
window.onclick = function(event) {
    const nutritionModal = document.getElementById('nutritionModal');
    const fitnessModal = document.getElementById('fitnessModal');

    if (event.target === nutritionModal) {
        nutritionModal.style.display = "none";
    }
    if (event.target === fitnessModal) {
        fitnessModal.style.display = "none";
    }
}




    </script>
</body>
</html>
