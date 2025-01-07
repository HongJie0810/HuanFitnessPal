<?php
// Database connection
require_once('database.php');

if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Prepare and execute the delete query
    $delete_query = "DELETE FROM huan_fitness_nutritionist WHERE Nutritionist_ID = '$user_id'";
    
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('User deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($con) . "');</script>";
    }
    
    // Redirect back to the User Information page
    echo "<script>window.location.href = 'Nutritionist_Information.php';</script>";
    exit(); 
}

// Define how many results you want per page
$results_per_page = 5;

// Initialize search query
$search_query = "";

if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($con, $_GET['search']);
}

if ($search_query != "") {
    $query = "SELECT COUNT(*) AS total FROM huan_fitness_nutritionist 
              WHERE Nutritionist_ID LIKE '%$search_query%' 
              OR Name LIKE '%$search_query%' 
              OR Gender LIKE '%$search_query%' 
              OR PhoneNo LIKE '%$search_query%' 
              OR Email_address LIKE '%$search_query%' 
              OR Category LIKE '%$search_query%' 
              OR Date_of_birth LIKE '%$search_query%'";
} else {
    $query = "SELECT COUNT(*) AS total FROM huan_fitness_nutritionist";
}

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$total_records = $row['total'];

if ($total_records == 0 && $search_query != "") {
    echo "<script>alert('No results found for the search query. Please try again.');</script>";
    echo "<script>window.location.href = 'Nutritionist_Information.php';</script>";
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

// Calculate the starting row for the query based on the current page
$start_from = ($current_page - 1) * $results_per_page;

if ($search_query != "") {
    $query = "SELECT * FROM huan_fitness_nutritionist 
              WHERE Nutritionist_ID LIKE '%$search_query%' 
              OR Name LIKE '%$search_query%' 
              OR Gender LIKE '%$search_query%' 
              OR PhoneNo LIKE '%$search_query%' 
              OR Email_address LIKE '%$search_query%' 
              OR Category LIKE '%$search_query%' 
              OR Date_of_birth LIKE '%$search_query%' 
              LIMIT $start_from, $results_per_page";
} else {
    $query = "SELECT * FROM huan_fitness_nutritionist LIMIT $start_from, $results_per_page";
}

$result = mysqli_query($con, $query);
?>  


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HTML</title>
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
                    <li><a href="Fitness_Class_Member.php"><i class="bi bi-people-fill"></i></i><span>Fitness Class Members</span></a></li>
                    <li><a href="dietary_consultation_details.php"><i class="bi bi-journal-medical"></i><span>Dietary Consultation Details</span></a></li>
                    <li><a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-in-right"></i><span>Log Out</span></a></li>
                </ul>
            </div>
        </div>
        <div class="main-content">
            <h1>Nutritionist Information</h1>
            <div class="general__box">
                <div class="search-container">
                <form method="GET" action="Nutritionist_Information.php">
                    <button type="button" id="add_nutritionist" class="add-icon" onclick="openAddUser()">
                        <i class="bi bi-person-fill-add"></i>
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
                    <a href="Nutritionist_Information.php?page=<?php echo $current_page - 1; ?>" class="arrow-btn" style="text-decoration:none">
                    <button class="arrow-btn"><i class="bi bi-arrow-left-circle-fill"></i></button>
                    </a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                    <a href="Nutritionist_Information.php?page=<?php echo $current_page + 1; ?>" class="arrow-btn" style="text-decoration:none">
                    <button class="arrow-btn"><i class="bi bi-arrow-right-circle-fill"></i></button>    
                    </a>
                    <?php endif; ?>

                    <span class="result-text">Showing page <?php echo $current_page; ?> out of <?php echo $total_pages; ?></span>
                </div>
                
            </div>
            <div class="containers">
                <div class = "row mt-5">
                    <div class="col">
                        <div class= "card mt-5">
                            <div class="card-body">
                                <table class="table table-bordered text-center">
                                    <tr class="bg-dark text-white">
                                        <td>Nutri_ID</td>
                                        <td>Name</td>
                                        <td>Gender</td>
                                        <td>Phone_No</td>
                                        <td>Email Address</td>
                                        <td>Category</td>
                                        <td>Date of Birth</td>
                                        <td>Edit</td>
                                        <td>Delete</td>
                                    </tr>
                                    <tr>

                                    <?php

                                        while($row = mysqli_fetch_assoc($result))
                                        {

                                    ?>

                                        <td><?php echo $row['Nutritionist_ID'] ?></td>
                                        <td><?php echo $row['Name'] ?></td>
                                        <td><?php echo $row['Gender'] ?></td>
                                        <td><?php echo $row['PhoneNo'] ?></td>
                                        <td><?php echo $row['Email_address'] ?></td>
                                        <td><?php echo $row['Category'] ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['Date_of_birth'])); ?></td>
                                        <td>
                                        <a href="javascript:void(0)" 
                                            onclick="openEdit('<?php echo $row['Nutritionist_ID']; ?>', 
                                                                '<?php echo $row['Name']; ?>', 
                                                                '<?php echo $row['Gender']; ?>', 
                                                                '<?php echo $row['PhoneNo']; ?>', 
                                                                '<?php echo $row['Email_address']; ?>', 
                                                                '<?php echo date('Y-m-d', strtotime($row['Date_of_birth'])); ?>')" 
                                            class="edit-btn" 
                                            style="text-decoration:none">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        </td><!-- Edit button -->
                                        <td>
                                            <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $row['Nutritionist_ID']; ?>')" class="edit-btn" style="text-decoration:none">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td> <!-- Delete button -->

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
    </div>

    <!-- Add Nutritionist Modal -->
    <div id="addUserContainer" class="edit-container" style="display: none;">
            <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
                <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeAddUser()">×</span>
                <h2>Add New Nutritionist</h2>
                <form id="addUserForm" method="POST" action="add_nutritionist.php" style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="Name" style="width: 100%;">Username:</label>
                        <input type="text" id="addUsername" name="Name" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="gender">Gender</label>
                        <select name="Gender" id="addGender">
                            <option value="Male" style="width: 100%;">Male</option>
                            <option value="Female" style="width: 100%;">Female</option>
                        </select>
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="PhoneNo" style="width: 100%;">Phone Number:</label>
                        <input type="text" id="addPhoneNo" name="PhoneNo" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="Email_address" style="width: 100%;">Email Address:</label>
                        <input type="email" id="addEmailAddress" name="Email_address" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="Category">Category</label>
                        <select name="Category" id="addcategory">
                            <option value="Dietitian" style="width: 100%;">Dietitian</option>
                            <option value="Sports Nutritionist" style="width: 100%;">Sports Nutritionist</option>
                            <option value="Clinical Nutritionist" style="width: 100%;">Clinical Nutritionist</option>
                            <option value="Pediatric Nutritionist" style="width: 100%;">Pediatric Nutritionist</option>
                        </select>
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="Date_of_birth" style="width: 100%;">Date of Birth:</label>
                        <input type="date" id="addDOB" name="Date_of_birth" required style="width: 100%;">
                    </div>
                    
                    <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                    <button type="submit" style="font-size:15px; align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 40px; cursor: pointer;">Add User</button>
                </form>
            </div>
        </div>

    <!-- Edit Nutritionist Modal -->
    <div id="editContainer" class="edit-container" style="display: none;">
        <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
            <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeEdit()">×</span>
            <h2>Edit Nutritionist</h2>
            <form id="editForm" method="POST" action="Nutritionist_user_edit.php" style="display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="Nutritionist_ID" id="editUserId">
                
                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="Name" style="width: 100%;">Username:</label>
                    <input type="text" id="editUsername" name="Name" required style="width: 100%;">
                </div>
                
                
                <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                <label for="Gender">Gender</label>
                    <select name="Gender" id="editGender">
                        <option value="Male" style="width: 100%;">Male</option>
                        <option value="Female" id="editGender" name="Gender" required style="width: 100%;">Female</option>
                    </select>
                </div>
                
                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="PhoneNo" style="width: 100%;">Phone Number:</label>
                    <input type="text" id="editPhoneNo" name="PhoneNo" required style="width: 100%;">
                </div>
                
                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="Email_address" style="width: 100%;">Email Address:</label>
                    <input type="email" id="editEmailAddress" name="Email_address" required style="width: 100%;">
                </div>

                <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                <label for="Category">Category</label>
                    <select name="Category" id="editCategory">
                        <option value="Dietitian" style="width: 100%;">Dietitian</option>
                        <option value="Sports Nutritionist"style="width: 100%;">Sports Nutritionist</option>
                        <option value="Clinical Nutritionist"style="width: 100%;">Clinical Nutritionist</option>
                        <option value="Pediatric Nutritionist"style="width: 100%;">Pediatric Nutritionist</option>
                    </select>
                </div>
                
                <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                    <label for="Date_of_birth" style="width: 100%;">Date of Birth:</label>
                    <input type="date" id="editDOB" name="Date_of_birth" required style="width: 100%;">
                </div>
                
                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                <button type="submit" style="align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 30px; cursor: pointer;">Save</button>
            </form>
        </div>
    </div>

        <script>
            function openEdit(userId, username, gender, phoneNo, emailAddress, dob) {

            document.getElementById('editUserId').value = userId;
            document.getElementById('editUsername').value = username;
            document.getElementById('editGender').value = gender;
            document.getElementById('editPhoneNo').value = phoneNo;
            document.getElementById('editEmailAddress').value = emailAddress;
            document.getElementById('editDOB').value = dob;
            document.getElementById('editContainer').style.display = 'block';
            }

            function closeEdit() {
                document.getElementById('editContainer').style.display = 'none';
            }
        </script>

        <script>
            function openAddUser() {
                document.getElementById('addUserContainer').style.display = 'block';
            }

            function closeAddUser() {
                document.getElementById('addUserContainer').style.display = 'none';
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const clearButton = document.getElementById('clear-search');
            const searchInput = document.getElementById('search-input');


            if (clearButton) {
                clearButton.addEventListener('click', function () {
                    searchInput.value = '';
                    window.location.href = 'Nutritionist_Information.php'; 
                });
            }
        });
        </script>

        <script>
            function getNextUserId($con) {
            $query = "SELECT MAX(Nutritionist_ID) AS max_id FROM huan_fitness_nutritionist";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);

            // Get the highest current user_id
            $max_id = $row['max_id'];

            // If there are no records, start from 2307001
            if ($max_id === null) {
                return 'N011';
            }

            // Increment the highest user_id by 1
            $next_id = (int)substr($max_id, 2) + 1; // Remove the first two characters and increment
            return '23' . str_pad($next_id, 5, '0', STR_PAD_LEFT); // Pad with zeros to maintain the format
        }

        </script>

        <script>
            function confirmDelete(userId) {
                // Display a confirmation dialog
                if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
                    // If confirmed, redirect to the delete action
                    window.location.href = 'Nutritionist_Information.php?id=' + userId;
                }
            }
        </script>

        <script>
            var toggle = document.getElementById("toggle");
            var container = document.getElementById("container");

            toggle.onclick = function() {
                container.classList.toggle('active');
            }
        </script>

        <script>
            function confirmLogout() {
                if (confirm("Are you sure you want to log out?")) {
                    window.location.href = "logout.php"; // Redirect to the logout script
                }
            }
        </script>
</body>
</html>