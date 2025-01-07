<?php
include('db_conn.php');

session_start(); 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure either username or user_id is set in session
if (!isset($_SESSION['username']) && !isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit();
}


$sql = "SELECT u.user_id, u.username, u.gender, u.phone_no, u.email_address, 
               u.profile_photo, u.date_of_birth, u.height, m.regDate, m.exprDate,
               IFNULL(m.member_id, 'inactive') AS member_id, 
               IFNULL(m.status, 'inactive') AS status 
        FROM huan_fitness_users u 
        LEFT JOIN huan_fitness_members m ON u.member_id = m.member_id 
        WHERE ";


if (isset($_SESSION['user_id'])) {
    $sql .= "u.user_id = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']); 
} 

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_info = $result->fetch_assoc();

    $photo = !empty($user_info['profile_photo']) 
             ? 'uploads/' . htmlspecialchars($user_info['profile_photo'])
             : 'css/img/default-profile.png'; 

    
    $member_id = htmlspecialchars($user_info['member_id']);
    $status = htmlspecialchars($user_info['status']);
} else {
    echo "No user information found."; 
    exit();
}


$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/navBar.css">
    <link rel="stylesheet" href="css/user_profile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .readonly-field {
            background-color: #f0f8ff; 
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid black;
            background-color: #f0f0f0;
            margin: 20px auto;
            background-position: center;
            background-size: cover;
        }

        .upload-container {
            display: flex;                
            justify-content: center;      
            align-items: center;          
            flex-direction: column;       
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            text-align:center;
        }

        .upload-btn-wrapper button {
            border: none;
            padding: 10px;
            border-radius: 8px;
            background-color: #333;
            color: white;
            cursor: pointer;
            width: 150px; 
            margin: 0 auto; 
        }

        .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
        }

        .button {
            margin-top: 20px;
            text-align:center;
        }

        .button input {
            width: 50%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button input:hover {
            background: linear-gradient(-229deg, #cf91ff, #5782f5);
        }

        .button input:active {
            background-color: #777;
        }

        #uploadSection {
            display: none; 
            text-align: center; 
        }

        html, body {
            height: 100%;
            margin: 0; 
            padding: 0; 
        }

        label {
            display: block;
            margin-bottom: 10px;
            font: bold;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            height: 100%;
            overflow-y: auto;
            background: linear-gradient(45deg, #f3e5f5, #e1f5fe);
            display: flex;
            flex-direction: column;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: auto;
            padding: 10px;
            margin: 0 auto;
            border-radius: 8px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
        }

        .profile-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .profile-header button {
            padding: 10px 20px;
            font-size: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .profile-header button:hover {
            background-color: #0056b3;
        }

        .profile-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        input[type="number"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .button-container button.update {
            background-color: #007bff;
            color: white;
        }

        .button-container button.update:hover {
            background-color: #0056b3;
        }

        .button-container button.edit-cancel {
            background-color: #dc3545;
            color: white;
        }

        .button-container button.edit-cancel:hover {
            background-color: #c82333;
        }

        label {
            display: flex;
            align-items: center;
        }

        #togglePassword {
            cursor: pointer;
            color: #666;
            font-size: 1.2rem;
            margin-left: 10px;
        }

        .password-container {
            margin-top: 5px;
        }

        #password {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .readonly-field {
            background-color: lightgray;
            border: 0px solid black;
        }

        .editable {
            background-color: #white; /* Default background color when not in edit mode */
            color: #white; /* Text color */
        }

        .edit-mode .editable {
            background-color: #e0f7fa; /* Highlight color during edit */
        }


        .button-container {
            margin-top: 20px;
        }

        .Profile-photo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>


</head>
<body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

    <div class="main-content" style = "background: linear-gradient(45deg, #f3e5f5, #e1f5fe);">
        <div class="profile-container">
            <h1 style="text-align:center">User Profile</h1>
            <br><br><br>
            
            <form id="userProfileForm" method="POST" action="edit_profile.php"  enctype="multipart/form-data">
            <div class="Profile-photo">

            <div class="profile-image" id="profileImagePreview" style="background-image: url('<?php echo 'uploads/' . htmlspecialchars($user_info['profile_photo']); ?>');"></div>

            </div>
            <div id="uploadSection" class="uploadSection">
                <div class="upload-container">
                 <div class="upload-btn-wrapper">
                      <button>Choose Profile Photo</button>
                      <input type="file" id="fileUpload" name="fileUpload" accept="image/*" onchange="previewImage(event)">
                 </div>
                 </div>
             </div>

            
            <br><br>
                <label for="user_id">User ID</label>
                <input type="text" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_info['user_id']); ?>" class="readonly-field" readonly>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_info['username']); ?>" class="editable readonly-field" readonly>

                <label for="member_id">Member ID</label>
                <input type="text" id="member_id" name="member_id" value="<?php echo htmlspecialchars($user_info['member_id']); ?>" class="readonly-field" readonly>

                <label for="status">Member Status</label>
                <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($user_info['status']); ?>" class="readonly-field" readonly>

                <label for="regDate">Member Registration Date</label>
                <input type="text" id="regDate" name="regDate" value="<?php echo htmlspecialchars($user_info['regDate']); ?>" class="readonly-field" readonly>

                <label for="exprDate">Member Expired Date</label>
                <input type="text" id="exprDate" name="exprDate" value="<?php echo htmlspecialchars($user_info['exprDate']); ?>" class="readonly-field" readonly>

                <label for="phone_no">Phone Number</label>
                <input type="tel" id="phone_no" name="phone_no" value="<?php echo htmlspecialchars($user_info['phone_no']); ?>" class="editable readonly-field">

                <label for="email_address">Email Address</label>
                <input type="email" id="email_address" name="email_address" value="<?php echo htmlspecialchars($user_info['email_address']); ?>" class="editable readonly-field">

                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="editable readonly-field" disabled>
                    <option value="Male" <?php echo htmlspecialchars($user_info['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo htmlspecialchars($user_info['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>

                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user_info['date_of_birth']); ?>" class="editable readonly-field">

                <label for="height">Height (cm)</label>
                <input type="number" id="height" name="height" value="<?php echo htmlspecialchars($user_info['height']); ?>" class="editable readonly-field">

                <label for="password">
                    Password
                    <span id="togglePassword" class="bi bi-eye" onclick="togglePasswordVisibility()"></span>
                </label>
                <div class="password-container">
                    <input type="password" id="password" name="password" value="" class="editable readonly-field" placeholder="Enter new password if you want to change it">
                </div>

                <div class="button-container">
                    <button type="button" class="edit-cancel" onclick="toggleEdit()">Edit</button>
                    <button id="updateProfile" type="submit" class="update" value="Upload File" style="display: none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const togglePasswordIcon = document.getElementById('togglePassword');

            if (passwordField.type === 'password') {
                passwordField.type = 'text'; 
                togglePasswordIcon.classList.remove('bi-eye'); 
                togglePasswordIcon.classList.add('bi-eye-slash'); 
            } else {
                passwordField.type = 'password'; 
                togglePasswordIcon.classList.remove('bi-eye-slash'); 
                togglePasswordIcon.classList.add('bi-eye');
            }



            setTimeout(() => {
                if (passwordField.type === 'text') {
                    passwordField.type = 'password';
                    togglePasswordIcon.classList.remove('bi-eye-slash');
                    togglePasswordIcon.classList.add('bi-eye');
                }
            }, 3000); 
        }


        let isEditing = false;
        function toggleEdit() {
            const form = document.getElementById('userProfileForm');
            const fields = document.querySelectorAll('.editable');
            const updateBtn = document.getElementById('updateProfile');
            const editBtn = document.querySelector('.edit-cancel');
            const uploadSection = document.getElementById('uploadSection');

            form.classList.toggle('edit-mode');

            fields.forEach((field) => {
                if (form.classList.contains('edit-mode')) {
                    field.removeAttribute('readonly');
                    field.removeAttribute('disabled');
                } else {
                    field.setAttribute('readonly', true);
                    if (field.tagName === 'SELECT') {
                        field.setAttribute('disabled', true);
                    }
                }
            });


            if (updateBtn.style.display === 'none' || updateBtn.style.display === '') {
                updateBtn.style.display = 'block';
                editBtn.textContent = 'Cancel';
                // Ensure the upload section is visible when editing
                uploadSection.style.display = 'block';
            } else {
                updateBtn.style.display = 'none';
                editBtn.textContent = 'Edit';
                // Hide the upload section when canceling
                uploadSection.style.display = 'none'; 
            }
        }





        document.querySelectorAll('.editable').forEach(field => {
            field.setAttribute('readonly', 'readonly'); 
        });

        document.querySelectorAll('.editable').forEach(field => {
            field.addEventListener('input', function () {
                if (isEditing) {
                    document.querySelector('.update').style.display = 'inline-block'; 
                }
            });
        });

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('profileImagePreview');
                output.style.backgroundImage = 'url(' + reader.result + ')';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        var toggle = document.getElementById("toggle");
        var container = document.getElementById("container");

        toggle.onclick = function() {
            container.classList.toggle('active');
        }

        var profilePhoto = document.querySelector(".Profile-photo");
            var userInfo = document.getElementById("user-info");

            profilePhoto.onclick = function() {
                userInfo.style.display = userInfo.style.display === 'block' ? 'none' : 'block';
            }
            
    </script>

    <script>
        document.getElementById('profile_photo').onchange = function (event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.querySelector('.Profile-photo img');
                if (output) {
                    output.src = reader.result; 
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        };
    </script>

</body>
</html>
