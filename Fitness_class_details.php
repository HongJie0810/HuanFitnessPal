<?php

require_once('database.php');

if (isset($_GET['id'])) {
    // Get the user ID 
    $class_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Prepare and execute the delete query
    $delete_query = "DELETE FROM fitness_class_details WHERE fitness_class_id = '$class_id'";
    
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Fitness class deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting class: " . mysqli_error($con) . "');</script>";
    }
    
    // Redirect back to the User Information page
    echo "<script>window.location.href = 'Fitness_class_details.php';</script>";
    exit(); 
}

// Define how many results you want per page
$results_per_page = 5;

// Initialize search query
$search_query = "";

// If a search query is provided via GET
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($con, $_GET['search']);
}

if ($search_query != "") {
    $query = "SELECT COUNT(*) AS total FROM fitness_class_details WHERE fitness_class_id LIKE '%$search_query%' OR fitness_class_category LIKE '%$search_query%' OR day LIKE '%$search_query%' OR start_time LIKE '%$search_query%' OR end_time LIKE '%$search_query%'";
} else {
    $query = "SELECT COUNT(*) AS total FROM fitness_class_details";
}

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$total_records = $row['total'];

if ($total_records == 0 && $search_query != "") {
    echo "<script>alert('No results found for the search query. Please try again.');</script>";
    echo "<script>window.location.href = 'Fitness_Class_Details.php';</script>";
    exit(); 
}

// Calculate the total number of pages
$total_pages = ceil($total_records / $results_per_page);

if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = (int) $_GET['page'];
} else {
    $current_page = 1;
}

if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages;
}

$start_from = ($current_page - 1) * $results_per_page;

if ($search_query != "") {
    $query = "SELECT * FROM fitness_class_details WHERE fitness_class_id LIKE '%$search_query%' OR fitness_class_category LIKE '%$search_query%' OR day LIKE '%$search_query%' OR start_time LIKE '%$search_query%' OR end_time LIKE '%$search_query%' LIMIT $start_from, $results_per_page";
} else {
    $query = "SELECT * FROM fitness_class_details LIMIT $start_from, $results_per_page";
}

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fitness Class Details</title>
    <link rel="stylesheet" href="css/Menu.css">
    <link rel="stylesheet" href="css/User_Information.css">
</head>

<body>
    <div class="layout">
        <div class="container add" id="container">
            <div class="brand">
                <h3>Main_Menu</h3>
                <a href="#" id="toggle"><i class="bi bi-list"></i></a>
            </div>
            <div class="user">
                <img src="css/img/dumbbell.png" alt="">
                <div class="name">
                    <h3>HuanFitness</h3>
                </div>
            </div>
            <div class="navbar">
                <ul>
                    <li><a href="home_page.php"><i class="bi bi-house"></i><span>DashBoard</span></a></li>
                    <li><a href="User_Information.php"><i class="bi bi-person-circle"></i><span>User Information</span></a></li>
                    <li><a href="Nutritionist_Information.php"><i class="bi bi-person-badge"></i><span>Nutritionist Information</span></a></li>
                    <li><a href="Member_Information.php"><i class="bi bi-person-vcard-fill"></i><span>Member Information</span></a></li>
                    <li><a href="Fitness_class_details.php"><i class="bi bi-folder"></i><span>Fitness Class Details</span></a></li>
                    <li><a href="Fitness_Class_Member.php"><i class="bi bi-people-fill"></i><span>Fitness Class Members</span></a></li>
                    <li><a href="dietary_consultation_details.php"><i class="bi bi-journal-medical"></i><span>Dietary Consultation Details</span></a></li>
                    <li><a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <h1>Fitness Class Details</h1>
            <div class="general__box">
                <div class="search-container">
                    <form method="GET" action="Fitness_Class_Details.php">
                        <button type="button" id="add_fitness_class" class="add-icon" onclick="openAddClass()">
                            <i class="bi bi-plus-circle-fill"></i>
                        </button>

                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <button type="button" id="clear-search" class="clear-icon">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        <?php endif; ?>

                        <input type="text" class="search-input" name="search" id="search-input" placeholder="Type to search" required>
                        <button type="submit" class="search-icon">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <?php if ($current_page > 1): ?>
                        <a href="Fitness_Class_Details.php?page=<?php echo $current_page - 1; ?>" class="arrow-btn" style="text-decoration:none">
                            <button class="arrow-btn"><i class="bi bi-arrow-left-circle-fill"></i></button>
                        </a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="Fitness_Class_Details.php?page=<?php echo $current_page + 1; ?>" class="arrow-btn" style="text-decoration:none">
                            <button class="arrow-btn"><i class="bi bi-arrow-right-circle-fill"></i></button>
                        </a>
                    <?php endif; ?>

                    <span class="result-text">Showing page <?php echo $current_page; ?> out of <?php echo $total_pages; ?></span>
                </div>
            </div>

            <div class="containers">
                <div class="row mt-5">
                    <div class="col">
                        <div class="card mt-5">
                            <div class="card-body">
                                <table class="table table-bordered text-center">
                                    <tr class="bg-dark text-white">
                                        <td>Fitness Class ID</td>
                                        <td>Category</td>
                                        <td>Day</td>
                                        <td>Start Time</td>
                                        <td>End Time</td>
                                        <td>Edit</td>
                                        <td>Delete</td>
                                    </tr>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['fitness_class_id'] ?></td>
                                            <td><?php echo $row['fitness_class_category'] ?></td>
                                            <td><?php echo $row['day'] ?></td>
                                            <td><?php echo $row['start_time'] ?></td>
                                            <td><?php echo $row['end_time'] ?></td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="openEdit('<?php echo $row['fitness_class_id']; ?>', '<?php echo $row['fitness_class_category']; ?>', '<?php echo $row['day']; ?>', '<?php echo $row['start_time']; ?>', '<?php echo $row['end_time']; ?>')" class="edit-btn" style="text-decoration:none">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $row['fitness_class_id']; ?>')" class="edit-btn" style="text-decoration:none">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div id="addClassContainer" class="edit-container" style="display: none;">
            <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
                <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeAddClass()">×</span>
                <h2>Add Fitness Class</h2>
                <form id="addClassForm" method="POST" action="add_fitness_class.php" style="display: flex; flex-direction: column; gap: 20px;">

                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="fitness_class_category" style="width: 100%;">Category:</label>
                        <input type="text" id="addCategory" name="fitness_class_category" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="day">Day</label>
                        <select name="day" id="addDay" style="width: 100%;">
                            <option value="Sunday">Sunday</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                        </select>
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="start_time" style="width: 100%;">Start Time:</label>
                        <input type="time" id="addStartTime" name="start_time" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="end_time" style="width: 100%;">End Time:</label>
                        <input type="time" id="addEndTime" name="end_time" required style="width: 100%;">
                    </div>

                    <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                    <button type="submit" style="font-size: 15px; align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 40px; cursor: pointer;">Add Class</button>
                </form>
            </div>
        </div>

        <!-- Edit Classes Modal -->
        <div id="editContainer" class="edit-container" style="display: none;">
            <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left: 550px; top: 50px;">
                <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeEdit()">×</span>
                <h2>Edit Fitness Class</h2>
                <form id="editClassForm" method="POST" action="edit_fitness_class.php" style="display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="fitness_class_id" id="editFitnessClassId">

                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="fitness_class_category" style="width: 100%;">Category:</label>
                        <input type="text" id="editCategory" name="fitness_class_category" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="day">Day</label>
                        <select name="day" id="editDay" style="width: 100%;">
                            <option value="Sunday">Sunday</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                        </select>
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="start_time" style="width: 100%;">Start Time:</label>
                        <input type="time" id="editStartTime" name="start_time" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="end_time" style="width: 100%;">End Time:</label>
                        <input type="time" id="editEndTime" name="end_time" required style="width: 100%;">
                    </div>

                    <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                    <button type="submit" style="font-size: 15px; align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 40px; cursor: pointer;">Edit Class</button>
                </form>
            </div>
        </div>
    </div>

        <script>
            function openEdit(fitnessClassId, category, day, startTime, endTime) {
                // Set the values for the edit fields in the modal
                document.getElementById('editFitnessClassId').value = fitnessClassId; 
                document.getElementById('editCategory').value = category;
                document.getElementById('editDay').value = day;
                document.getElementById('editStartTime').value = startTime;
                document.getElementById('editEndTime').value = endTime;

                // Show the edit container
                document.getElementById('editContainer').style.display = 'block';
            }

            function closeEdit() {
                // Hide the edit container
                document.getElementById('editContainer').style.display = 'none';
            }

            function openAddClass() {
                document.getElementById('addClassContainer').style.display = 'block';
            }

            function closeAddClass() {
                document.getElementById('addClassContainer').style.display = 'none';
            }
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const clearButton = document.getElementById('clear-search');
            const searchInput = document.getElementById('search-input');


            if (clearButton) {
                clearButton.addEventListener('click', function () {
                    searchInput.value = '';
                    window.location.href = 'Fitness_class_details.php'; 
                });
            }
        });
        </script>

        <script>
            var toggle = document.getElementById("toggle");
            var container = document.getElementById("container");

            toggle.onclick = function() {
                container.classList.toggle('active');
            }
        </script>

        <script>
            function confirmDelete(fitnessClassId) {
                // Display a confirmation dialog
                if (confirm("Are you sure you want to delete this class? This action cannot be undone.")) {
                    // If confirmed, redirect to the delete action
                    window.location.href = 'Fitness_class_details.php?id=' + fitnessClassId;
                }
            }
        </script>

        <script>
            function confirmLogout() {
                if (confirm("Are you sure you want to log out?")) {
                    window.location.href = "logout.php"; 
                }
            }
        </script>


</body>
</html>
