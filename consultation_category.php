<?php

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id === null) {
   
   header('Location: login.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <title>Nutritionist</title>
   <link rel="stylesheet" href="css/navBar.css">
   <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.min.css'>
   <link rel='stylesheet' href='https://static.fontawesome.com/css/fontawesome-app.css'>
   <link rel='stylesheet' href='https://pro.fontawesome.com/releases/v5.2.0/css/all.css'>
   <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:400,700'>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
   <link rel="stylesheet" href="css/consultation_category.css">

   <style>
      .main-content h1 {
         color:white;
         text-shadow: 0px 0px 10px cyan,
                  0px 0px 20px cyan,
                  0px 0px 40px cyan,
                  0px 0px 80px cyan;
         text-align: center;
         font-size:45px; 
         text-shadow: 10ch;
         margin:5px;

      }
   </style>
</head>  

<body style = "background: linear-gradient(45deg, #f3e5f5, #e1f5fe);">
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
      <div class="main-content"  >
      <h2 style= " text-align: center;">Nutritionist Category</h2><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
      <div class="justin">
         <div class="options">
            <div class="option active" style="--optionBackground:url(https://static.vecteezy.com/system/resources/previews/015/205/476/non_2x/sport-food-pack-icon-flat-protein-nutrition-vector.jpg);">
               <div class="label">
                  <div class="icon">
                     <img src="https://cdn0.iconfinder.com/data/icons/sports-and-fitness/512/protein_powder_bottle_jar_bodybuilding_power_strength_gain_gainer_muscle_nutrition_energy_container_supplement_vitamin_athletic_food_strong_gym_training_sport_calories_flat_design_icon-512.png" />
                  </div>
                  <div class="info">
                     <div class="main">Sports Nutritionist</div>
                     <button class="choose-btn" onclick="window.location.href='sport_appointment.php'">Choose</button>
                  </div>
               </div>
            </div>

            <div class="option" style="--optionBackground:url(https://img.freepik.com/premium-vector/nutritionist-dietician-counselor-doctor-holds-healthy-unhealthy-food-hands-illustration_477760-104.jpg);">
               <div class="label">
                  <div class="icon">
                     <img src="https://cdn-icons-png.freepik.com/512/7251/7251812.png" />
                  </div>
                  <div class="info">
                     <div class="main">Pediatric Nutritionist</div>
                     <button class="choose-btn" onclick="window.location.href='pediatric_appointment.php'">Choose</button>
                  </div>
               </div>
            </div>

            <div class="option" style="--optionBackground:url(https://i.pinimg.com/originals/a8/a6/42/a8a6423b4809c1150b52563d9b0734f6.png)">
               <div class="label">
                  <div class="icon">
                     <img src="https://cdn-icons-png.flaticon.com/512/12106/12106266.png" />
                  </div>
                  <div class="info">
                     <div class="main">Clinical Dietitian</div>
                     <button class="choose-btn" onclick="window.location.href='clinical_appointment.php'">Choose</button>
                  </div>
               </div>
            </div>

            <div class="option" style="--optionBackground:url(https://img.freepik.com/premium-vector/woman-visiting-nutritionist-illustration-doctor-explaining-healthy-harmful-food-ingredients-cartoon-characters-dietitian-offering-fresh-vegetables-dairy-product-daily-meals_151150-2282.jpg);">
               <div class="label">
                  <div class="icon">
                     <img src="https://cdn-icons-png.flaticon.com/512/3463/3463779.png" />
                  </div>
                  <div class="info">
                     <div class="main">Dietitian</div>
                     <button class="choose-btn" onclick="window.location.href='dietitian_appointment.php'">Choose</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
      <script>
         $(".option").click(function () {
            $(".option").removeClass("active");
            $(this).addClass("active");
         });


       
    var toggle = document.getElementById("toggle");
    var container = document.getElementById("container");

    toggle.onclick = function () {
      container.classList.toggle('active');
    }


      </script>
   </div>
</body>

</html>