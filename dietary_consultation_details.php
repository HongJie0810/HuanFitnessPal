<?php

require_once('database.php');


if (isset($_GET['id'])) {
    $consult_id = mysqli_real_escape_string($con, $_GET['id']);
    $delete_query = "DELETE FROM dietary_consultations_details WHERE consult_id = '$consult_id'";
    
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('User deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($con) . "');</script>";
    }
    
    echo "<script>window.location.href = 'dietary_consultation_details.php';</script>";
    exit();
}

// Define results per page
$results_per_page = 5;
$search_query = "";

// Handle search functionality
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($con, $_GET['search']);
}

// Calculate the total number of records 
if ($search_query != "") {
    $count_query = "SELECT COUNT(*) AS total FROM dietary_consultations_details 
                    WHERE request_status IN ('Approved', 'Rejected') 
                    AND (consult_id LIKE '%$search_query%' OR Nutritionist_ID LIKE '%$search_query%' 
                    OR nutritionist_category LIKE '%$search_query%' OR member_id LIKE '%$search_query%' 
                    OR request_status LIKE '%$search_query%')";
} else {
    $count_query = "SELECT COUNT(*) AS total FROM dietary_consultations_details 
                    WHERE request_status IN ('approved', 'rejected')";
}

$count_result = mysqli_query($con, $count_query);
$row = mysqli_fetch_assoc($count_result);
$total_records = $row['total'];

//if no records found
if ($total_records == 0 && $search_query != "") {
    echo "<script>alert('No results found for the search query.');</script>";
    echo "<script>window.location.href = 'dietary_consultation_details.php';</script>";
    exit();
}

// Calculate total pages
$total_pages = ceil($total_records / $results_per_page);
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($total_pages, $current_page));
$start_from = ($current_page - 1) * $results_per_page;

// Main query to fetch paginated records based on search and filter criteria
if ($search_query != "") {
    $query = "SELECT consult_id, Nutritionist_ID, nutritionist_category, member_id, date, start_time, end_time, request_status 
              FROM dietary_consultations_details 
              WHERE request_status IN ('Approved', 'Rejected')
              AND (consult_id LIKE '%$search_query%' OR Nutritionist_ID LIKE '%$search_query%' 
              OR nutritionist_category LIKE '%$search_query%' OR member_id LIKE '%$search_query%' 
              OR request_status LIKE '%$search_query%')
              LIMIT $start_from, $results_per_page";
} else {
    $query = "SELECT consult_id, Nutritionist_ID, nutritionist_category, member_id, date, start_time, end_time, request_status 
              FROM dietary_consultations_details 
              WHERE request_status IN ('Approved', 'Rejected')
              LIMIT $start_from, $results_per_page";
}

$result = mysqli_query($con, $query);

// Fetch pending consultation requests for notifications
$pending_result = mysqli_query($con, "SELECT * FROM dietary_consultations_details WHERE request_status = 'Pending'");
$pending_count = mysqli_num_rows($pending_result);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fitness Class Members</title>
  <link rel="stylesheet" href="css/Menu.css">
  <link rel="stylesheet" href="css/User_Information.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
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
            <h1 style="text-align: center; margin: 0;">Dietary Consultation Details</h1>
            
            <button type = "button" id="notification-btn" style="background: none; border: none; cursor: pointer; position: absolute; right: 30px; top: 20px; box-shadow: none;" onclick="openNotificationDetails()">
                <i class="fas fa-bell" style="font-size: 40px; color: black;"></i>
                <span id="notification-badge" style="position: absolute; top: 0px; right: -5px; background-color: red; color: white; border-radius: 50%; padding: 3px 6px; font-size: 12px;">
                    <?php echo $pending_count; ?>
                </span>
            </button>

            <div class="general__box" style="left: 64%; top: 20%">
                <div class="search-container">
                    <form method="GET" action="dietary_consultation_details.php">
                        <button type="button" id="add-user" class="add-icon" onclick="openAddDetails()">
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
                        <a href="dietary_consultation_details.php?page=<?php echo $current_page - 1; ?>" class="arrow-btn" style="text-decoration:none">
                            <button class="arrow-btn"><i class="bi bi-arrow-left-circle-fill"></i></button>
                        </a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="dietary_consultation_details.php?page=<?php echo $current_page + 1; ?>" class="arrow-btn" style="text-decoration:none">
                            <button class="arrow-btn"><i class="bi bi-arrow-right-circle-fill"></i></button>    
                        </a>
                    <?php endif; ?>
     
                    <span class="result-text">Showing page <?php echo $current_page; ?> out of <?php echo $total_pages; ?></span>
                </div>    
            </div>
            <div class="containers" style="padding: 110px;">
                <div class="row mt-5">
                    <div class="col">
                        <div class="card mt-5">
                            <div class="card-body">
                                <table class="table table-bordered text-center">
                                    <tr class="bg-dark text-white">
                                        <td>Consult ID</td>
                                        <td>Nutritionist ID</td>
                                        <td>Nutritionist Category</td>
                                        <td>Member ID</td>
                                        <td>Date</td>
                                        <td>Start Time</td>
                                        <td>End Time</td>
                                        <td>Request Status</td>
                                        <td>Edit</td>
                                        <td>Delete</td>
                                    </tr>
                                    
                                    <?php
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        if ($row['request_status'] == "Approved" || $row['request_status'] == "Rejected") { 
                                    ?>
                                    <tr>
                                        <td><?php echo $row['consult_id'] ?></td>
                                        <td><?php echo $row['Nutritionist_ID'] ?></td>
                                        <td><?php echo $row['nutritionist_category'] ?></td>
                                        <td><?php echo $row['member_id'] ?></td>
                                        <td><?php echo $row['date'] ?></td>
                                        <td><?php echo $row['start_time'] ?></td>
                                        <td><?php echo $row['end_time'] ?></td>
                                        <td><?php echo $row['request_status'] ?></td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="openEdit('<?php echo $row['consult_id']; ?>', '<?php echo $row['Nutritionist_ID']; ?>', '<?php echo $row['nutritionist_category']; ?>', '<?php echo $row['member_id']; ?>', '<?php echo $row['date']; ?>', '<?php echo $row['start_time']; ?>', '<?php echo $row['end_time']; ?>', '<?php echo $row['request_status']; ?>')" class="edit-btn" style="text-decoration:none">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="confirmDelete('<?php echo $row['consult_id']; ?>')" class="edit-btn" style="text-decoration:none">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
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

    <!-- Add User Modal -->
    <div id="addDetailsContainer" class="edit-container" style="display: none;">
            <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:10px;">
                <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeAddDetails()">×</span>
                <h2>Add New Consultation Details</h2>
                <form id="addUserForm" method="POST" action="add_dietary_consultations_details.php" style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="Nutritionist_ID" style="width: 100%;">Nutritionist ID:</label>
                        <input type="text" id="addNutritionistID" name="Nutritionist_ID" style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                    <label for="category">Category</label>
                        <select name="category" id="addCategory">
                            <option value="Dietitian" style="width: 100%;">Dietitian</option>
                            <option value="Sports Nutritionist"style="width: 100%;">Sports Nutritionist</option>
                            <option value="Clinical Nutritionist"style="width: 100%;">Clinical Nutritionist</option>
                            <option value="Pediatric Nutritionist"style="width: 100%;">Pediatric Nutritionist</option>
                        </select>
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column;">
                        <label for="member_id" style="width: 100%;">Member ID:</label>
                        <input type="text" id="addMemberId" name="member_id" style="width: 100%;">
                    </div>

                    <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                        <label for="date" style="width: 100%;">Date:</label>
                        <input type="date" id="addDate" name="date" required style="width: 100%;">
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
                    <button type="submit" style="font-size:15px; align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 40px; cursor: pointer;">Add User</button>
                </form>
            </div>
        </div>

    <!-- edit Member Modal -->
    <div id="editContainer" class="edit-container" style="display: none;">
        <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 400px; width: 100%; left:550px; top:50px;">
            <span class="close-btn" style="position: absolute; top: 10px; right: 20px; cursor: pointer;" onclick="closeEdit()">×</span>
            <h2>Edit Details</h2>
            <form id="editForm" method="POST" action="edit_dietary_consultations_details.php" style="display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="consult_id" id="editConsultId">
                
                <div class="form-field" style="display: flex; flex-direction: column;">
                    <label for="Nutritionist_ID" style="width: 100%;">Nutritionist ID:</label>
                    <input type="text" id="editNutritionistID" name="Nutritionist_ID" style="width: 100%;">
                </div>

                <div class="form-field" style="display: flex; flex-direction: column; width: 65%;">
                <label for="category">Category</label>
                    <select name="category" id="editCategory">
                        <option value="Dietitian" style="width: 100%;">Dietitian</option>
                        <option value="Sports Nutritionist"style="width: 100%;">Sports Nutritionist</option>
                        <option value="Clinical Nutritionist"style="width: 100%;">Clinical Nutritionist</option>
                        <option value="Pediatric Nutritionist"style="width: 100%;">Pediatric Nutritionist</option>
                    </select>
                </div>
                
                <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>">
                <button type="submit" style="align-self: center; margin-top: 20px; background-color: #f06727; color: white; border: none; border-radius: 5px; padding: 10px 30px; cursor: pointer;">Renew</button>
            </form>
        </div>
    </div>


<!-- notification Modal -->
<div id="notificationContainer" class="edit-container" style="display: none;">
    <div class="edit-content" style="background-color: white; padding: 50px 70px; border-radius: 10px; max-width: 1000px; width: 100%; left:280px; top:100px;">
        <span class="close-btn" onclick="closeNotificationDetails()" style="cursor: pointer; font-size: 20px; float: right;">×</span>
        <h2 style="text-align: center; margin-top: 20px;">Consultation Request</h2>
        <table class="table table-bordered text-center" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <tr class="bg-dark text-white">
                <td>Consult ID</td>
                <td>Nutritionist ID</td>
                <td>Nutritionist Category</td>
                <td>Member ID</td>
                <td>Date</td>
                <td>Start Time</td>
                <td>End Time</td>
                <td>Request Status</td>
                <td>Approved</td>
                <td>Rejected</td>
            </tr>
            <?php
                
                while ($pending_row = mysqli_fetch_assoc($pending_result)) {
                    echo "<tr>";
                    echo "<td>{$pending_row['consult_id']}</td>";
                    echo "<td>{$pending_row['Nutritionist_ID']}</td>";
                    echo "<td>{$pending_row['nutritionist_category']}</td>";
                    echo "<td>{$pending_row['member_id']}</td>";
                    echo "<td>{$pending_row['date']}</td>";
                    echo "<td>{$pending_row['start_time']}</td>";
                    echo "<td>{$pending_row['end_time']}</td>";
                    echo "<td>{$pending_row['request_status']}</td>";
                    echo "<td>
                            <button onclick=\"updateRequestStatus('{$pending_row['consult_id']}', 'Approved')\" style='box-shadow: none; border: none; background: none; color: #90EE90; font-size: 20px;'>✓</button>
                          </td>";
                    echo "<td>
                            <button onclick=\"updateRequestStatus('{$pending_row['consult_id']}', 'Rejected')\" style='box-shadow: none; border: none; background: none; color: red; font-size: 20px;'>✗</button>
                          </td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
</div>





    <script>
        var toggle = document.getElementById("toggle");
        var container = document.getElementById("container");

         toggle.onclick = function() {
            container.classList.toggle('active');
        }
	</script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const clearButton = document.getElementById('clear-search');
        const searchInput = document.getElementById('search-input');


        if (clearButton) {
            clearButton.addEventListener('click', function () {
                searchInput.value = '';
                window.location.href = 'dietary_consultation_details.php'; 
            });
        }
    });

    function confirmDelete(consult_id) {
        if (confirm("Are you sure you want to delete this details?")) {
            window.location.href = "dietary_consultation_details.php?id=" + consult_id;
        }
    }

    function openEdit(consult_id, Nutritionist_ID, category) {
            document.getElementById("editConsultId").value = consult_id;
            document.getElementById("editNutritionistID").value = Nutritionist_ID;
            document.getElementById("editCategory").value = category;
            document.getElementById("editContainer").style.display = "block";
        }

    function closeEdit() {
        document.getElementById('editContainer').style.display = 'none';
    }

    function openAddDetails() {
        document.getElementById('addDetailsContainer').style.display = 'block';
    }

    function closeAddDetails() {
        document.getElementById('addDetailsContainer').style.display = 'none';
    }

    function openNotificationDetails(){
        document.getElementById('notificationContainer').style.display = 'block';
    }

    function closeNotificationDetails(){
        document.getElementById('notificationContainer').style.display = 'none';
    }
    </script>
    
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php"; 
            }
        }

        function updateRequestStatus(consult_id, status) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_consultation_request_status.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText.includes("Success")) {
                        alert("You have already " + status + " the consultation request");
                        location.reload();
                    } else {
                        alert("Failed to update request status: " + xhr.responseText);
                    }
                }
            };
            xhr.send("consult_id=" + consult_id + "&request_status=" + status); // Changed "status" to "request_status"
        }

    </script>


</body>
</html>