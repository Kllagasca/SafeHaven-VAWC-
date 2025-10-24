<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar {
            display: none;
        }
        html {
            scrollbar-width: none;
        }
        body {
            background-image: url('assets/img/survey-bg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Montserrat, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .logo-container img {
            width: 200px;
            height: 200px;
        }
        .survey-container {
            background: rgba(0, 0, 0, 0.51);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #333;
        }
        button {
            background: #007BFF;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        p {
            font-weight: normal;
        }
    </style>
</head>
<body>
<?php include('includes/navbar.php'); ?>

    <!-- Main Container -->
    <div class="container my-5">
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="Logo">
        </div>

        <!-- Survey Title -->
        <div class="text-center mb-4 text-white">
            <h2>Violence Against Women and Children</h2>
        </div>

        <!-- Purpose Section -->
        <div class="mx-auto text-center text-white">
            <p>
                Purpose: To assess the awareness, participation, and experiences of individuals.
            </p>
        </div>

        <!-- Survey Form -->
        <div class="survey-container mx-auto mt-4 text-white text-center">
            <h1>Your response has been recorded. Thank you for your participation!</h1>

            <div class="text-end">
            <a href="index.php" class="btn btn-primary d-inline-flex align-items-center" style="background-color: #9953ed; border-color: #9953ed;">Back Home</a>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
