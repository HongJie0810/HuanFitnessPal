<?php
// Database connection
require_once('database.php');

if (isset($_GET['id'])) {
    $member_id = mysqli_real_escape_string($con, $_GET['id']);
    
    mysqli_begin_transaction($con);
    
    try {
        $update_query = "UPDATE huan_fitness_users SET member_id = NULL WHERE member_id = ?";
        if ($stmt = mysqli_prepare($con, $update_query)) {
            mysqli_stmt_bind_param($stmt, 's', $member_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        $delete_query = "DELETE FROM huan_fitness_members WHERE member_id = ?";
        if ($stmt = mysqli_prepare($con, $delete_query)) {
            mysqli_stmt_bind_param($stmt, 's', $member_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        mysqli_commit($con);
        
        echo "<script>alert('Member deleted successfully.');</script>";
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo "<script>alert('Error deleting member: " . $e->getMessage() . "');</script>";
    }
    
    echo "<script>window.location.href = 'Member_Information.php';</script>";
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
    $query = "SELECT COUNT(*) AS total FROM huan_fitness_members WHERE member_id LIKE '%$search_query%' OR regDate LIKE '%$search_query%' OR exprDate LIKE '%$search_query%' OR status LIKE '%$search_query%'";
} else {
    $query = "SELECT COUNT(*) AS total FROM huan_fitness_members";
}

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$total_records = $row['total'];

if ($total_records == 0 && $search_query != "") {
    echo "<script>alert('No results found for the search query. Please try again.');</script>";
    echo "<script>window.location.href = 'Member_Information.php';</script>";
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
   $query = "SELECT * FROM huan_fitness_members WHERE member_id LIKE '%$search_query%' OR regDate LIKE '%$search_query%' OR exprDate LIKE '%$search_query%' OR status LIKE '%$search_query%' LIMIT $start_from, $results_per_page";
} else {
    $query = "SELECT * FROM huan_fitness_members LIMIT $start_from, $results_per_page";
}

$result = mysqli_query($con, $query);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Huan Fitness Members</title>
  <link rel="stylesheet" href="css/Menu.css">
  <link rel="stylesheet" href="css/User_Information.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome for icons -->
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
                    <li><a href="home_page.php"><i class="bi bi-house"></i><span>Dashboard</span></a></li>
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
        <div class="main-content" style="position: relative; height: 100%;">
            <h1 style="text-align: center; margin: 0;">Huan Fitness Members Information </h1>
            <div class="general__box" style="left: 64%">
                <div class="search-container">
                <form method="GET" action="Member_Information.php">   
                    <button type="button" id="add-user" class="add-icon" onclick="openAddUser()">
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
                    <a href="Member_Information.php?page=<?php echo $current_page - 1; ?>" class="arrow-btn" style="text-decoration:none">
                    <button class="arrow-btn"><i class="bi bi-arrow-left-circle-fill"></i></button>
                    </a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                    <a href="Member_Information.php?page=<?php echo $current_page + 1; ?>" class="arrow-btn" style="text-decoration:none">
                    <button class="arrow-btn"><i class="bi bi-arrow-right-circle-fill"></i></button>    
                    </a>
                    <?php endif; ?>
     
                    <span class="result-text">Showing page <?php echo $current_page; ?> out of <?php echo $total_pages; ?></span>
                </div>    
            </div>
            <div class="containers" style="padding: 110px;">
                <div class = "row mt-5">
                    <div class="col">
                        <div class= "card mt-5">
                            <div class="card-body">
                                <table class="table table-bordered text-center">
                                    <tr class="bg-dark text-white">
                                        <td>Member ID</td>
                                        <td>Registration Date</td>
                                        <td>Expiration Date</td>
                                        <td>Member Status</td>
                                        <td>Renew</td>
                                        <td>Delete</td>
                                    </tr>
                                    <tr>

                                    <?php

                                        while($row = mysqli_fetch_assoc($result))
                                        {

                                    ?>

                                        <td><?php echo $row['member_id'] ?></td>
                                        <td><?php echo $row['regDate'] ?></td>
                                        <td><?php echo $row['exprDate'] ?></td>
                                        <td><?php echo $row['status'] ?></td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="openEdit('<?php echo $row['member_id']; ?>', '<?php echo $row['regDate']; ?>')" class="edit-btn" style="text-decoration:none">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </a>
                                        </td><!-- Edit button -->
                                        <td>
                                            <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $row['member_id']; ?>')" class="edit-btn" style="text-decoration:none">
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


    <!-- Add Member Modal -->
    <div id="addUserContainer" class="edit-container" style="display: none;">
        <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
            <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeAddUser()">×</span>
            <h2>Add New Member</h2>
            <form id="addUserForm" method="POST" action="add_member.php" style="display: flex; flex-direction: column; gap: 20px;">
                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="Name" style="width: 100%;">Username:</label>
                    <input type="text" id="addUsername" name="username" required style="width: 100%;">
                </div>

                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="PhoneNo" style="width: 100%;">Phone Number:</label>
                    <input type="text" id="addPhoneNo" name="phone_no" required style="width: 100%;">
                </div>

                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="Email_address" style="width: 100%;">Email Address:</label>
                    <input type="email" id="addEmailAddress" name="email_address" required style="width: 100%;">
                </div>

                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                <button type="submit" style="font-size:15px; align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 40px; cursor: pointer;">Add Member</button>
            </form>
        </div>
    </div>

    
    <!-- renew Member Modal -->
    <div id="editContainer" class="edit-container" style="display: none;">
        <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
            <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeEdit()">×</span>
            <h2>Renew Membership</h2>
            <form id="editForm" method="POST" action="renew_member.php" style="display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="member_id" id="editMemberId">
                
                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="regDate" style="width: 100%;">Registration Date:</label>
                    <input type="date" id="editRegDate" name="regDate" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%;">
                </div>
                
                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                <button type="submit" style="align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 30px; cursor: pointer;">Renew</button>
            </form>
        </div>
    </div>

    <!-- Delete confirmation -->
    <script>
        function confirmDelete(member_id) {
            if (confirm("Are you sure you want to delete this member?")) {
                window.location.href = "Member_Information.php?id=" + member_id;
            }
        }

        // Open edit modal and populate fields with selected data
        function openEdit(memberId, regDate, exprDate) {
            document.getElementById("editMemberId").value = memberId;
            document.getElementById("editRegDate").value = regDate;
            document.getElementById("editContainer").style.display = "block";
        }

        function closeEdit() {
            document.getElementById('editContainer').style.display = 'none';
        }

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
                window.location.href = 'Member_Information.php'; 
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
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php"; // Redirect to the logout script
            }
        }
    </script>


</body>
</html>
