<?php
session_start(); // Start the session to check if user data is set
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="css/img/dumbbell.png" type="image/icon type">
    <title>Welcome</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(-229deg, #cf91ff, #5782f5);
            background-size: cover;
            position: relative;
        }

        .logo-title-container {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
        }

        .heart-logo {
            height: 50px;
            margin-right: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            color: #fff;
        }

        .container {
            text-align: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 200%;
            margin-bottom: 20px;
        }

        .button {
            margin-top: 20px;
        }

        .button input {
            padding: 10px 20px;
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
    </style>
</head>
<body>

    <div class="logo-title-container">
        <img class="heart-logo" src="css/img/dumbbell.png" alt="heart-logo">
        <p class="title">HuanFitness</p>
    </div>

    <div class="container">
        <h1>Successfully Registered!</h1>
        <div class="button">
            <a href="dashboard.php">
                <input type="button" value="Welcome, Explore now">
            </a>
        </div>
    </div>

</body>
</html>
