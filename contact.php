<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/logo.png">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="shortcut icon" href="assets/img/logo.png" type="image/png">
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
                <h1 style="font-size: 24px; color: #333;">SafeHaven</h1>
            </div>
            <img src="assets/img/logo.png" alt="Company Logo">
            <div style="text-align: center; margin-bottom: 0;">
                <p style="font-size: 15px; color: #333;">Your Safe Space Against Violence</p>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="contact-form">
            <h1>Contact Us</h1>
            <?php
include('config/supabase_connect.php'); // Include your PDO connection

// Start session for storing messages
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $message = trim(htmlspecialchars($_POST['message']));

    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['message'] = "<p class='message' style='color: red;'>All fields are required.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "<p class='message' style='color: red;'>Invalid email address.</p>";
    } else {
        try {
            // Insert contact form data into the database
            $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindValue(':message', $message, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['message'] = "<p class='message' style='color: green;'>Message sent successfully! We will get back to you soon.</p>";
            } else {
                $_SESSION['message'] = "<p class='message' style='color: red;'>Failed to send message. Please try again later.</p>";
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "<p class='message' style='color: red;'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Display message if set
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}
?>
            <form id="contactForm" method="POST" action="">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>

                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>

    <script>
        // Add simple confirmation before form submission
        const contactForm = document.getElementById('contactForm');
        contactForm.addEventListener('submit', function(e) {
            const confirmed = confirm("Do you want to submit the form?");
            if (!confirmed) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
