<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
         body {
            background: linear-gradient(to right, #ff4de2, #9953ed);
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 80%;
            max-width: 900px;
        }

        .logo {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            padding: 20px;
        }

        .logo img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .contact-form {
            padding: 20px;
        }

        .contact-form h1 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
            color: #333;
        }

        .contact-form label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
            color: #555;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .contact-form textarea {
            resize: none;
            height: 100px;
        }

        .contact-form button {
            background: pink;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .contact-form button:hover {
            background: linear-gradient(to right, #9953ed, #ff4de2);
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        /* Back Home Link Styling */
        .logo a {
            text-decoration: none;
            color: #9953ed;
            font-weight: bold;
            font-size: 16px;
            display: flex;
            align-items: center;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .logo a:hover {
            color: #ff4de2;
        }

        .logo a i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Left Column: Logo -->
        <div class="logo">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i> Back Home
            </a>
            <div style="text-align: center; margin-bottom: 0;">
                <h1 style="font-size: 24px; color: #333;">Gender and Development Hub</h1>
            </div>
            <img src="assets/img/logo.png" alt="Company Logo">
            <div style="text-align: center; margin-bottom: 0;">
                <p style="font-size: 15px; color: #333;">Empowering Equality, Building Futures</p>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="contact-form">
            <h1>Forgot Password</h1>
            


            <form method="post" action="send-password-reset.php">

            <label for="email">email</label>
            <input type="email" name="email" id="email">

                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</body>

</html>
