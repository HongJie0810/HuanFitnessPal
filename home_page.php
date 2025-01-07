<?php
session_start();


if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HTML</title>
  <link rel="stylesheet" href="css/Menu.css">
  <link rel="stylesheet" href="css/Admin_Container.css">
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

    <div class="main-content" style="position: relative; height: 100%;">
      <h1 style="text-align: center; margin: 0;">Huan Fitness Admin System</h1>
      <div class="general__content">
          <div class="element__photos element__1">
              <div class="title__element" style="transform: translateX(10px) translateY(40px);">User Information</div>
          </div>
          <div class="element__photos element__2">
              <div class="title__element" style="transform: translateX(10px) translateY(40px);">Nutritionist Information</div>
          </div>
          <div class="element__photos element__4">
              <div class="title__element" style="transform: translateX(10px)  translateY(60px);">Dietary Consultation Details</div>
          </div>
          <div class="element__photos element__3">
              <div class="initial__title"><span>Services</span></div>
              <div class="title__element" style="transform: translateX(10px) translateY(40px);">Member Information</div>
          </div>
          <div class="element__photos element__5">
              <div class="title__element" style="transform: translateX(10px) translateY(40px);">Fitness Class Members</div>
          </div>
          <div class="element__photos element__6">
              <div class="title__element" style="transform: translateX(10px) translateY(40px);">Fitness Class Details</div>
          </div>
          <div class="element__photos element__7">
              <div class="title__element" style="transform: translateX(26px) translateY(20px);">Log Out</div>
          </div>
      </div>
  </div>

  <script>
    var toggle = document.getElementById("toggle");
    var container = document.getElementById("container");

    toggle.onclick = function () {
      container.classList.toggle('active');
    }


    document.querySelector('.element__1').onclick = function() {
        window.location.href = 'User_Information.php'; 
    };

    document.querySelector('.element__2').onclick = function() {
        window.location.href = 'Nutritionist_Information.php'; 
    };

    document.querySelector('.element__3').onclick = function() {
        window.location.href = 'Member_Information.php'; 
    };

    document.querySelector('.element__4').onclick = function() {
        window.location.href = 'dietary_consultation_details.php'; 
    };

    document.querySelector('.element__6').onclick = function() {
        window.location.href = 'Fitness_Class_Details.php';
    };

    document.querySelector('.element__5').onclick = function() {
        window.location.href = 'Fitness_Class_Member.php'; 
    };

    document.querySelector('.element__7').onclick = function() {
      confirmLogout(); // Call the confirmLogout function
    };
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
