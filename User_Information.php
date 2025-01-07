<?php
// Database connection
require_once('database.php');

if (isset($_GET['id'])) {
    // Get the user ID from the URL
    $user_id = mysqli_real_escape_string($con, $_GET['id']);
    
    $member_delete_query = "DELETE FROM huan_fitness_members WHERE member_id = (SELECT member_id FROM huan_fitness_users WHERE user_id = '$user_id')";
    
    // Execute member deletion if needed
    if (mysqli_query($con, $member_delete_query)) {
        $delete_query = "DELETE FROM huan_fitness_users WHERE user_id = '$user_id'";
        
        if (mysqli_query($con, $delete_query)) {
            echo "<script>alert('User and associated member record deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting user: " . mysqli_error($con) . "');</script>";
        }
    } else {
        $delete_query = "DELETE FROM huan_fitness_users WHERE user_id = '$user_id'";
        
        if (mysqli_query($con, $delete_query)) {
            echo "<script>alert('User deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting user: " . mysqli_error($con) . "');</script>";
        }
    }
    
    // Redirect back to the User Information page
    echo "<script>window.location.href = 'User_Information.php';</script>";
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
    $query = "SELECT COUNT(*) AS total FROM huan_fitness_users WHERE user_id LIKE '%$search_query%' OR member_id LIKE '%$search_query%' OR username LIKE '%$search_query%' OR gender LIKE '%$search_query%' OR phone_no LIKE '%$search_query%' OR email_address LIKE '%$search_query%' OR date_of_birth LIKE '%$search_query%'";
} else {
    $query = "SELECT COUNT(*) AS total FROM huan_fitness_users";
}

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$total_records = $row['total'];

if ($total_records == 0 && $search_query != "") {
    echo "<script>alert('No results found for the search query. Please try again.');</script>";
    echo "<script>window.location.href = 'User_Information.php';</script>";
    exit(); // Stop further execution of the code
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
    $query = "SELECT * FROM huan_fitness_users WHERE user_id LIKE '%$search_query%' OR member_id LIKE '%$search_query%' OR username LIKE '%$search_query%' OR gender LIKE '%$search_query%' OR phone_no LIKE '%$search_query%' OR email_address LIKE '%$search_query%' OR date_of_birth LIKE '%$search_query%' LIMIT $start_from, $results_per_page";
} else {
    $query = "SELECT * FROM huan_fitness_users LIMIT $start_from, $results_per_page";
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
            <h1>Huan Fitness Users Information</h1>
            <div class="general__box">
                <div class="search-container">
                    <form method="GET" action="User_Information.php">
                        <button type="button" id="add-user" class="add-icon" onclick="openRegisterUser()">
                            <i class="bi bi-person-fill-add"></i>
                        </button>

                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <button type="button" id="clear-search" class="clear-icon">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        <?php endif; ?>

                        <input type="text" class="search-input" name="search" id="search-input" placeholder="Type to search " onkeyup="searchUsers()" required>
                        <button type="submit" class="search-icon">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <?php if ($current_page > 1): ?>
                        <a href="User_Information.php?page=<?php echo $current_page - 1; ?>" class="arrow-btn" style="text-decoration:none">
                            <button class="arrow-btn"><i class="bi bi-arrow-left-circle-fill"></i></button>
                        </a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="User_Information.php?page=<?php echo $current_page + 1; ?>" class="arrow-btn" style="text-decoration:none">
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
                                        <td>User ID</td>
                                        <td>Member ID</td>
                                        <td>Username </td>
                                        <td>Gender</td>
                                        <td>Phone_No</td>
                                        <td>Email Address</td>
                                        <td>Date of Birth</td>
                                        <td>Edit</td>
                                        <td>Delete</td>
                                    </tr>
                                    <tr>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <td><?php echo $row['user_id'] ?></td>
                                            <td><?php echo $row['member_id'] ?></td>
                                            <td><?php echo $row['username'] ?></td>
                                            <td><?php echo $row['gender'] ?></td>
                                            <td><?php echo $row['phone_no'] ?></td>
                                            <td><?php echo $row['email_address'] ?></td>
                                            <td><?php echo $row['date_of_birth'] ?></td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="openEdit('<?php echo $row['user_id']; ?>', '<?php echo $row['username']; ?>', '<?php echo $row['gender']; ?>', '<?php echo $row['phone_no']; ?>', '<?php echo $row['email_address']; ?>', '<?php echo $row['date_of_birth']; ?>')" class="edit-btn" style="text-decoration:none">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $row['user_id']; ?>')" class="edit-btn" style="text-decoration:none">
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

        <!-- Registration User Modal -->
        <div id="registerUserContainer" class="edit-container" style="display: none;">
            <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
                <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeRegisterUser()">×</span>
                <h2>Register New User</h2>
                <form id="registerUserForm" method="POST" action="add_register_user.php" style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="registerUsername" style="width: 100%;">Username:</label>
                        <input type="text" id="registerUsername" name="registerUsername" required style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="registerPassword" style="width: 100%;">Password:</label>
                        <input type="password" id="registerPassword" name="registerPassword" required style="width: 100%;">
                    </div>

                    <button type="button" onclick="validateRegister()" style="font-size:15px; align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 40px; cursor: pointer;">Register</button>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editContainer" class="edit-container" style="display: none;">
            <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
                <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeEdit()">×</span>
                <h2>Edit User</h2>
                <form id="editForm" method="POST" action="edit_user.php" style="display: flex; flex-direction: column; gap: 20px;">
                    <input type="hidden" name="user_id" id="editUserId">
                    
                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="username" style="width: 100%;">Username:</label>
                        <input type="text" id="editUsername" name="username" required style="width: 100%;">
                    </div>
                    
                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                    <label for="gender">Gender</label>
                        <select name="gender" id="editGender">
                            <option value="Male" style="width: 100%;">Male</option>
                            <option value="Female" id="editGender" name="gender" required style="width: 100%;">Female</option>
                        </select>
                    </div>
                    
                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="phone_no" style="width: 100%;">Phone Number:</label>
                        <input type="text" id="editPhoneNo" name="phone_no" required style="width: 100%;">
                    </div>
                    
                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="email_address" style="width: 100%;">Email Address:</label>
                        <input type="email" id="editEmailAddress" name="email_address" required style="width: 100%;">
                    </div>
                    
                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="date_of_birth" style="width: 100%;">Date of Birth:</label>
                        <input type="date" id="editDOB" name="date_of_birth" required style="width: 100%;">
                    </div>
                    
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
            function openRegisterUser() {
                document.getElementById('registerUserContainer').style.display = 'block';
            }

            function closeRegisterUser() {
                document.getElementById('registerUserContainer').style.display = 'none';
            }
        </script>

        <script>
            function validateRegister() {
                const username = document.getElementById('registerUsername').value;
                const password = document.getElementById('registerPassword').value;

                if (username === '' || password === '') {
                    alert('Please fill out all fields.');
                    return;
                }

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'add_register_user.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (this.status === 200) {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            alert('Registration successful! Redirecting to the new user page.');

                            // Redirect to the page where the new user is located
                            window.location.href = 'User_Information.php?page=' + response.page;
                        } else {
                            alert(response.message); // e.g., "Username already exists"
                        }
                    } else {
                        alert('Server error. Please try again.');
                    }
                };

                const data = `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
                xhr.send(data);
            }
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const clearButton = document.getElementById('clear-search');
            const searchInput = document.getElementById('search-input');


            if (clearButton) {
                clearButton.addEventListener('click', function () {
                    searchInput.value = '';
                    window.location.href = 'User_Information.php'; 
                });
            }
        });
        </script>

        <script>
            function getNextUserId($con) {
                $query = "SELECT MAX(user_id) AS max_id FROM huan_fitness_users";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);

                // Get the highest current user_id
                $max_id = $row['max_id'];

                // If there are no records, start from 2307001 (adjust based on your convention)
                if ($max_id === null) {
                    return 2303021;  
                }

                // Increment the highest user_id by 1
                $next_id = (int)$max_id + 1; 

                return $next_id;
            }


        </script>

        <script>
            function confirmDelete(userId) {
                const confirmation = confirm('Are you sure you want to delete this user? The member record may also will be deleted as well.');
                
                if (confirmation) {
                    window.location.href = `User_Information.php?id=${userId}`;
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
    </div>
</body>
</html>
