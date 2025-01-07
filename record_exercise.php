<?php
// Include the database connection
include 'db_conn.php';

session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Get the selected date from the URL or set to today
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Initialize a message variable
$message = "";

// Handle form submissions for adding, editing, and deleting exercises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adding a new exercise
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $type = $_POST['type'];
        $name = $_POST['name'];

        if ($type == 'strength') {
            $sets = $_POST['sets'];
            $reps = $_POST['reps'];
            $sql = "INSERT INTO exercises (user_id, date, type, name, sets, reps) VALUES ('$user_id', '$date', '$type', '$name', '$sets', '$reps')";
        } elseif ($type == 'cardio') {
            $minutes = $_POST['minutes'];
            $distance = $_POST['distance'];
            $sql = "INSERT INTO exercises (user_id, date, type, name, minutes, distance) VALUES ('$user_id', '$date', '$type', '$name', '$minutes', '$distance')";
        }

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='message success'>New exercise added successfully!</div>";
        } else {
            $message = "<div class='message error'>Error: " . $conn->error . "</div>";
        }
    }

    // Editing an existing exercise
    elseif (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $name = $_POST['name'];

        if ($type == 'strength') {
            $sets = $_POST['sets'];
            $reps = $_POST['reps'];
            $sql = "UPDATE exercises SET name='$name', sets='$sets', reps='$reps' WHERE id='$id' AND user_id='$user_id'";
        } elseif ($type == 'cardio') {
            $minutes = $_POST['minutes'];
            $distance = $_POST['distance'];
            $sql = "UPDATE exercises SET name='$name', minutes='$minutes', distance='$distance' WHERE id='$id' AND user_id='$user_id'";
        }

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='message success'>Exercise updated successfully!</div>";
        } else {
            $message = "<div class='message error'>Error updating exercise: " . $conn->error . "</div>";
        }
    }

    // Deleting an exercise
    elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM exercises WHERE id='$id' AND user_id='$user_id'";

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='message success'>Exercise deleted successfully!</div>";
        } else {
            $message = "<div class='message error'>Error deleting exercise: " . $conn->error . "</div>";
        }
    }
}

// Retrieve exercises for the selected date and specific to the logged-in user
$sql = "SELECT * FROM exercises WHERE user_id='$user_id' AND date='$date'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Exercise</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/record_exercise.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="record-exercise">

<div class="container add" id="container">
    <div class="brand">
        <h3>Menu</h3>
        <a href="#" id="toggle"><i class="bi bi-list"></i></a>
    </div>
    <div class="user">
        <img src="css/img/dumbbell.png" alt="">
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
    <div class="container1">
        <h2>Record Exercise for Date: <?= htmlspecialchars($date); ?></h2>
        <?= $message; ?>
        
        <!-- Add Exercise Form -->
        <form method="POST" action="" class="exercise-form">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="type">Exercise Type:</label>
                <select name="type" id="type" required>
                    <option value="" disabled selected>Select Type</option>
                    <option value="strength">Strength</option>
                    <option value="cardio">Cardio</option>
                </select>
            </div>

            <div class="form-group">
                <label for="name">Exercise Name:</label>
                <input type="text" name="name" required>
            </div>

            <div id="strength-fields" class="extra-fields" style="display: none;">
                <div class="form-group">
                    <label for="sets">Sets:</label>
                    <input type="number" name="sets" min="1">
                </div>

                <div class="form-group">
                    <label for="reps">Reps:</label>
                    <input type="number" name="reps" min="1">
                </div>
            </div>

            <div id="cardio-fields" class="extra-fields" style="display: none;">
                <div class="form-group">
                    <label for="minutes">Minutes:</label>
                    <input type="number" name="minutes" min="1">
                </div>

                <div class="form-group">
                    <label for="distance">Distance (km):</label>
                    <input type="number" step="0.1" name="distance" min="0">
                </div>
            </div>

            <button type="submit">Add Exercise</button>
        </form>

        <!-- Filter by Exercise Type -->
<form method="GET" action="">
    <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>"> <!-- Preserve selected date -->
    <label for="exercise_type">Filter by Type:</label>
    <select name="exercise_type" id="exercise_type">
        <option value="">All</option>
        <option value="strength" <?= isset($_GET['exercise_type']) && $_GET['exercise_type'] == 'strength' ? 'selected' : ''; ?>>Strength</option>
        <option value="cardio" <?= isset($_GET['exercise_type']) && $_GET['exercise_type'] == 'cardio' ? 'selected' : ''; ?>>Cardio</option>
    </select>
    <button type="submit" class="filter-btn">Filter</button>
</form>

        <!-- List of Added Exercises -->
        <br>
        <h3>Exercises for <?= htmlspecialchars($date); ?>:</h3>
        <div class="exercises-list">
            <?php
            // Determine filter type
            $filterType = isset($_GET['exercise_type']) ? $_GET['exercise_type'] : '';

            // Modify SQL query to include filtering by exercise type
            $sql = "SELECT * FROM exercises WHERE user_id='$user_id' AND date = '$date'";

            if ($filterType) {
                $sql .= " AND type = '$filterType'";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <div class="exercise-item">
                        <p><strong>(<?= htmlspecialchars($row['type']); ?>)</strong></p>

                        <!-- Edit Exercise Form -->
                        <form method="POST" action="" class="edit-exercise-form">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="type" value="<?= htmlspecialchars($row['type']); ?>">
                            <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">

                            <div class="edit-fields">
                                <label for="edit_name_<?= $row['id']; ?>">Exercise Name:</label>
                                <input type="text" name="name" id="edit_name_<?= $row['id']; ?>" class="ename" value="<?= htmlspecialchars($row['name']); ?>" required>

                                <?php if ($row['type'] == 'strength'): ?>
                                    <label for="edit_sets_<?= $row['id']; ?>">Sets:</label>
                                    <input type="number" name="sets" id="edit_sets_<?= $row['id']; ?>" class="ename" value="<?= htmlspecialchars($row['sets']); ?>" min="1">
                                    <label for="edit_reps_<?= $row['id']; ?>">Reps:</label>
                                    <input type="number" name="reps" id="edit_reps_<?= $row['id']; ?>" class="ename" value="<?= htmlspecialchars($row['reps']); ?>" min="1">
                                <?php else: ?>
                                    <label for="edit_minutes_<?= $row['id']; ?>">Minutes:</label>
                                    <input type="number" name="minutes" id="edit_minutes_<?= $row['id']; ?>" class="ename" value="<?= htmlspecialchars($row['minutes']); ?>" min="1">
                                    <label for="edit_distance_<?= $row['id']; ?>">Distance (km):</label>
                                    <input type="number" step="0.1" name="distance" id="edit_distance_<?= $row['id']; ?>" class="ename" value="<?= htmlspecialchars($row['distance']); ?>" min="0">
                                <?php endif; ?>
                            </div>

                            <button type="submit">Update</button>
                            
                        </form>

                        <!-- Delete Exercise Form -->
                        <form method="POST" action="" class="delete-exercise-form">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this exercise?');">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No exercises added for this date.</p>
            <?php endif; ?>
        </div>

        <div class="button-container">
            <a href="exercise_index.php" class="return-button">Return to Calendar</a>
        </div>

        
    </div>
</div>

<script>
// Show/hide fields based on selected exercise type
document.getElementById('type').addEventListener('change', function() {
    var value = this.value;
    document.getElementById('strength-fields').style.display = value === 'strength' ? 'block' : 'none';
    document.getElementById('cardio-fields').style.display = value === 'cardio' ? 'block' : 'none';
});
</script>
</body>
</html>