<head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<style>
    /* Apply background image and style to the body container */
    #body {
        background-image: url('assets/img/bg-footer.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        padding: 50px 0;
    }

    /* Footer section headings */
    .footer-heading {
        font-size: 22px;
        font-weight: bold;
        color: white;
    }

    /* Social media links styling */
    ul {
        list-style: none;
        padding: 0;
    }

    ul li {
        margin-bottom: 10px;
    }

    ul li a {
        text-decoration: none;
        color: white;
        font-weight: bold;
    }

    ul li a:hover {
        color: #7c2aa6; /* Change color on hover */
        text-decoration: underline;
    }

    /* General text styling */
    p {
        color: white;
        font-size: 16px;
        line-height: 1.6;
    }

    hr {
        border: 1px solid #ccc;
        width: 90%;
    }
</style>

<div id="body" class="py-5">
    <div class="container">
        <div class="row">

            <!-- Gender and Development Section -->
            <div class="col-md-4 justify-content text-white">
            <div style="display: flex; align-items: flex-start;">
                <img src="assets/img/logo.png" alt="Logo" style="width: 60px; margin-right: 10px;">
                <div>
                    <h2 class="footer-heading" style="margin: 0;margin-top:3px;">SafeHaven</h2>
                    <p style="margin: 0px 0 0;">Your Safe Space Against Violence</p>
                </div>
            </div>


                <hr style="border-color: white; margin: 10px 0;">
                <p>
                An online resource center on Violence Against Women and Children in Barangay Dolores, San Pablo City.
                </p>
            </div>


            <!-- Follow Us Section -->
            <div class="col-md-4 text-white">
            <div style="display: flex; align-items: center;">
                <i class="fa-solid fa-globe" style="color:white; margin-right: 10px; font-size: 54px;"></i>
                <h4 class="footer-heading" style="margin: 0;">Follow us at:</h4>
            </div>

                <hr>
                <ul>
                <?php
                    try {
                        $socialMediaQuery = "SELECT * FROM social_medias";
                        $stmt = $pdo->query($socialMediaQuery); // Execute the query using PDO
                        $socialMedia = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array

                        if ($socialMedia) {
                            if (count($socialMedia) > 0) {
                                foreach ($socialMedia as $socialItem) {
                                    ?>
                                    <li>
                                        <a href="<?= htmlspecialchars($socialItem['url']) ?>" target="_blank">
                                            <?= htmlspecialchars($socialItem['name']) ?>
                                        </a>
                                    </li>
                                    <?php
                                }
                            } else {
                                echo "<li>No Social Media Added</li>";
                            }
                        } else {
                            echo "<li>Something Went Wrong</li>";
                        }
                    } catch (PDOException $e) {
                        echo "<li>Something Went Wrong: " . htmlspecialchars($e->getMessage()) . "</li>";
                    }
                    ?>

                </ul>
            </div>

            <!-- Contact Information Section -->
            <div class="col-md-4 text-white">
            <div style="display: flex; align-items: center;">
                <i class="fa-solid fa-message" style="color:white; margin-right: 10px; font-size: 54px;"></i>
                <h4 class="footer-heading" style="margin: 0;">Contact Information:</h4>
            </div>
    <hr>
    <p>
        <strong><i class="fas fa-map-marker-alt"></i> Address:</strong> Pamantasan ng Lungsod ng San Pablo <br>
        <strong><i class="fas fa-envelope"></i> Email:</strong> honeybunchcompany@gmail.com <br>
        <strong><i class="fas fa-phone"></i> Phone:</strong> 09-123-456-789
    </p>
</div>

        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/0ed8b91907.js" crossorigin="anonymous"></script>