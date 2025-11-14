<?php
include ('config/supabase_connect.php');
$pageTitle = "Home";
include('includes/navbar.php'); // Include the navbar
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeHaven</title>
    <style>
        ::-webkit-scrollbar {
            display: none;
        }


        html {
            scrollbar-width: smooth;
        }


        body {
            overflow: -moz-scrollbars-none;
            -ms-overflow-style: none;
            overflow-y: scroll;
        }


        .h1{
            font-size: 300px;
        }


        .bg-image {
        background: url('assets/img/bg-main.png') no-repeat center center;
        background-size: cover;
        height: 90vh; /* Full viewport height */
        width: 100%;
    }


        .bg-custom{
            background: #7c2aa6;
        }


        .bg-custom2 {
            background: #7c2aa6;
        }




        .responsive-img {
            width: 100%; /* Adjust to container width */
            max-width: 1400px; /* Maximum width for the image */
            height: 300px; /* Fixed height */
            object-fit: cover; /* Ensures the image is cropped properly */
            margin: 0; /* Centers the image inside its container */
        }


        .card {
            height: 300px; /* Set a fixed height for all cards */
        }


        .card1 {
            width: 400px;
            height: 200px; /* Set a fixed height for all cards */
        }


        .card-body {
            padding: 40px; /* Adjust padding for spacing */
        }


        .card .btn {
            align-self: end; /* Keep the button aligned to the left */
            background-color: #7c2aa6;
        }


        .header-text {
    font-family: 'Canva Sans', sans-serif;
    font-size: 60px;
    font-weight: bold;
    color: black;
    margin-bottom: -40px; /* Reduce spacing below header */
}

.sub-text {
    font-family: 'Canva Sans', sans-serif;
    font-size: 50px;
    font-weight: bold;
    color:  #7c2aa6;
    margin-top: 10px; /* Remove space above subtext */
}

.description {
    font-family: 'Canva Sans', sans-serif;
    font-size: 30px; /* Adjust size as needed */
    color:  #7c2aa6; /* Make text black */
    margin-top: 20px; /* Add space above description */
    text-align: center; /* Center the description text */
}

/* Optional: move both upward by targeting their container */
.text-container {
    margin-top: 0px; /* Move the whole block upward */
    text-align: center; /* Center the text */
}

    /* Staggered fade-in for hero texts */
    .stagger-item {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 420ms cubic-bezier(.2,.9,.2,1), transform 420ms cubic-bezier(.2,.9,.2,1);
    }

    .stagger-item.visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
        .stagger-item {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
    }

    /* Fade out the whole hero when the user scrolls down; reappear on scroll up */
    .text-container {
        transition: opacity 360ms ease, transform 360ms ease;
        will-change: opacity, transform;
    }

    .text-container.hidden {
        opacity: 0;
        transform: translateY(-6px);
        pointer-events: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .text-container {
            transition: none !important;
        }
    }

.custom-button {
    margin-top: 20px;
    padding: 12px 30px;
    font-size: 18px;
    font-family: 'Canva Sans', sans-serif;
    font-weight: bold;
    color: white;
    background-color: #7c2aa6; /* Purple background */
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.custom-button:hover {
    background-color: #7c2aa6; /* Darker purple on hover */
}

    @media (min-width: 576px) {
        .header-text {
            font-size: 3rem; /* Larger text for medium screens */
        }


        .sub-text {
            font-size: 1.5rem; /* Larger subtext for medium screens */
        }
    }


    @media (min-width: 992px) {
        .header-text {
            font-size: 95px; /* Larger text for large screens */
        }


        .sub-text {
            font-size: 70px; /* Larger subtext for large screens */
        }
    }

    </style>
</head>
<body>


<!-- Background Section -->
<div style="position: relative; width: 100%; height: 87vh;"> <!-- Full viewport height -->
    <div class="bg-image" style="position: relative; z-index: 1; display: flex; justify-content: center; align-items: center; flex-direction: column; height: 100%;">
        <div class="text-container">
        <div class="header-text stagger-item">Welcome to SafeHaven!</div>
        <div class="sub-text stagger-item">Your Safe Space Against Violence</div>
        <div class="description stagger-item">A comprehensive online platform dedicated to combating violence <br>
            against women and children through awareness, support, and <br>
            community action.</div>
            <button class="custom-button stagger-item" onclick="window.location.href='index.php#posts';">Explore More</button>


        </div>
    </div>
</div>
<!-- Alert Message -->


<div class="py-4 bg-custom2" style="width:100%;">
</div>


<?php
try {
    $query = "SELECT * FROM carousel WHERE CAST(status AS INTEGER) = 0"; // Cast status to integer
 // Fetch only visible carousel items
    $stmt = $pdo->query($query); // Execute the query using PDO
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array


    if (count($result) === 0) {
        throw new Exception("No carousel items found.");
    }
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
?>
<div id="carouselExampleCaptions" class="carousel slide mt-5" data-bs-ride="carousel" style="margin: 0 50px; border-radius: 15px; overflow: hidden; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
    <!-- Indicators -->
    <div class="carousel-indicators">
        <?php
        $totalSlides = count($result); // Get the total number of slides
        foreach ($result as $index => $row): ?>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?= $index; ?>"
                    class="<?= $index === 0 ? 'active' : ''; ?>" aria-current="<?= $index === 0 ? 'true' : 'false'; ?>"
                    aria-label="Slide <?= $index + 1; ?>"></button>
        <?php endforeach; ?>
    </div>


    <!-- Carousel Items -->
    <div class="carousel-inner">
        <?php
        if ($totalSlides > 0) {
            $isActive = true;
            foreach ($result as $row): ?>
                <div class="carousel-item <?= $isActive ? 'active' : ''; ?>" style="height: 655px; position: relative;">
                    <img src="<?= htmlspecialchars($row['image'] ?: 'assets/img/no-image.png'); ?>"
                         class="d-block w-100"
                         style="height: 100%; object-fit: cover;"
                         alt="<?= htmlspecialchars($row['title']); ?>">
                </div>
                <?php $isActive = false; ?>
            <?php endforeach;
        } else { ?>
            <div class="carousel-item active" style="height: 655px; position: relative;">
                <img src="assets/img/no-image.png" class="d-block w-100" style="height: 100%; object-fit: cover;" alt="No Image">
            </div>
        <?php } ?>


            <!-- Navigation Buttons -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
    </div>
</div>


    </div>
</div>

<section id="posts">
<div class="py-5">
    <div class="container">
        <div class="row gx-lg-5"> <!-- Added horizontal gutter for large screens -->


            <!-- Posts Section -->
            <div class="col-12 col-lg-8 mb-4 order-1"> <!-- Default order on large screens -->
    <div class="py-3 bg-custom mt-3 mb-3" style="border-radius: 10px;">
        <div class="container">
            <h2 class="text-white fw-semi-bold">Posts</h2>
        </div>
    </div>
    <div class="row">
        <?php
        try {
            $serviceQuery = "SELECT * FROM services WHERE status = FALSE AND approval_status = 'approved' ORDER BY created_at DESC";
            $stmt = $pdo->query($serviceQuery);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (count($result) > 0) {
                $maxVisiblePosts = 3;


                foreach ($result as $index => $row):
                    if ($index >= $maxVisiblePosts) break; // Show only the first 3 posts
        ?>
                    <div class="col-12 mb-4 post-item">
                        <div class="card shadow-sm h-100">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <?php if ($row['image'] != ''): ?>
                                        <img src="<?= htmlspecialchars($row['image']); ?>"
                                             class="img-fluid h-100"
                                             alt="Image"
                                             style="object-fit: cover;">
                                    <?php else: ?>
                                        <img src="assets/img/no-image.png"
                                             class="img-fluid h-100"
                                             alt="No Image"
                                             style="object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body d-flex flex-column">
                                        <?php
                                        // Format created_at to show date and hour:minute (no seconds)
                                        $created = '';
                                        if (!empty($row['created_at'])) {
                                            try {
                                                $dt = new DateTime($row['created_at']);
                                                $created = $dt->format('F j, Y g:i A');
                                            } catch (Exception $e) {
                                                $created = $row['created_at'];
                                            }
                                        }
                                        ?>
                                        <h5 class="card-title text-uppercase" style="color: #7c2aa6;">
                                            <span style="font-weight: bold;"><?= htmlspecialchars($row['name']); ?></span>
                                            <span style="font-weight: normal;">(<?= htmlspecialchars($created); ?>)</span>
                                        </h5>
                                        <p class="card-text text-dark">
                                            <?= htmlspecialchars(substr(strip_tags($row['long_description']), 0, 100)) . '...'; ?>
                                        </p>


                                        <a href="post.php?slug=<?= htmlspecialchars($row['slug']); ?>" class="btn mt-auto text-white" style="background-color: #7c2aa6;">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        <?php
                endforeach;
                if (count($result) > $maxVisiblePosts): // Show the "Show More" button if there are more than 3 posts
        ?>
                <div class="col-12 text-center">
                    <a href="post-all.php" class="btn text-white" style="background-color: #7c2aa6; margin-top: 15px;">Show More</a>
                </div>
        <?php
                endif;
            } else { ?>
                <div class="col-12">
                    <h5 class="text-center">No approved posts to display.</h5>
                </div>
            <?php }
        } catch (PDOException $e) {
            die("Query Failed: " . $e->getMessage());
        }
        ?>
    </div>
</div>

            <!-- Articles Section -->
            <div class="col-12 col-lg-4 mb-4 order-0 order-lg-2"> <!-- Default position for large screens -->
                <div class="py-3">
                    <div class="container text-start">
                        <h1 class="fw-semi-bold" style="color: #7c2aa6;">ARTICLES</h1>
                        <div style="width: 60%; height: 3px; background-color: #7c2aa6; margin: 10px auto text-start;"></div>
                    </div>
                </div>
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" style="margin: 0 auto; width: 100%; border-radius: 10px; overflow: hidden;">
                    <div class="carousel-inner">
                        <?php
                        try {
                            $query = "SELECT * FROM news WHERE status = FALSE";
                            $stmt = $pdo->query($query);
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


                            $totalSlides = count($result);
                            if ($totalSlides > 0) {
                                $isActive = true;
                                foreach ($result as $row): ?>
                                    <div class="carousel-item <?= $isActive ? 'active' : ''; ?>" style="text-align: center;">
                                        <div style="width: 100%; height: 300px; margin: 0 auto; overflow: hidden;">
                                            <img src="<?= htmlspecialchars($row['image'] ?: 'assets/img/no-image.png'); ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;"
                                                 alt="<?= htmlspecialchars($row['title']); ?>">
                                        </div>
                                        <div class="p-2">
                                            <h6 style="font-weight: bold; color: #333;"><?= htmlspecialchars($row['name']); ?></h6>
                                            <p style="color: #555; font-size: 0.9rem;">
    <?= htmlspecialchars(strip_tags($row['long_description'] ?: 'No description available.')); ?>
</p>

                                        </div>
                                    </div>
                                    <?php $isActive = false; ?>
                                <?php endforeach;
                            } else { ?>
                                <div class="carousel-item active" style="text-align: center;">
                                    <div style="width: 100%; height: 300px; margin: 0 auto; overflow: hidden;">
                                        <img src="assets/img/no-image.png"
                                             style="width: auto; height: 100%; object-fit: cover;"
                                             alt="No Image">
                                    </div>
                                    <div class="p-2" style="background-color: #f9f9f9;">
                                        <h6 style="font-weight: bold; color: #333;">No Title</h6>
                                        <p style="color: #555; font-size: 0.9rem;">No description available.</p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php
                        } catch (PDOException $e) {
                            die("Query Failed: " . $e->getMessage());
                        }
                        ?>
                    </div>
                    <div class="carousel-indicators" style="position: static; margin-top: 10px; text-align: center;">
                        <?php for ($i = 0; $i < $totalSlides; $i++): ?>
                            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="<?= $i; ?>"
                                    class="<?= $i === 0 ? 'active' : ''; ?>" aria-label="Slide <?= $i + 1; ?>"
                                    style="background-color: #7c2aa6; width: 10px; height: 10px; border-radius: 50%;"></button>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
</section>








<div>
    <div class="container mb-5">
        <div class="py-3 bg-custom mb-5" style="border-radius: 10px;">
            <div class="container">
                <h2 class="text-white fw-semi-bold">Documents</h2>
            </div>
        </div>
        <div class="row">
            <?php
            try {
                $documentQuery = "SELECT * FROM documents WHERE status = FALSE AND approval_status = 'approved'";
                $stmt = $pdo->query($documentQuery); // Execute the query using PDO
                $documents = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all documents


                if (count($documents) > 0):
                    foreach ($documents as $row):
            ?>
                        <div class="col-12 col-sm-6 col-md-4 mb-4">
                            <div class="card shadow-lg h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-center"><?= htmlspecialchars($row['name']); ?></h5>
                                    <?php if ($row['file'] != '') : ?>
                                        <div class="mt-auto text-center">
                                            <a href="<?= $row['file']; ?>"
                                               class="btn text-white" style="margin-top: 10px;"
                                               target="_blank" rel="noopener noreferrer">
                                                <i class="bi bi-eye align-center"></i> View File
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No file available</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
            <?php
                    endforeach; // Close foreach
                else: // Close if (count($documents) > 0)
            ?>
                    <div class="col-12">
                        <h5 class="text-center">No approved documents to display.</h5>
                    </div>
            <?php
                endif; // Close else
            } catch (PDOException $e) {
                die("Query Failed: " . $e->getMessage());
            }
            ?>
        </div>
    </div>
</div>

<div>
    <div class="container mb-5">
        <div class="py-3 bg-custom mb-5" style="border-radius: 10px;">
            <div class="container">
                <h2 class="text-white fw-semi-bold">Surveys</h2>
            </div>
        </div>
        <div class="row">
            <?php
            include 'config/db_connect.php';
            // Fetch all surveys with the status approved
            $surveyQuery = "SELECT * FROM surveys";
            $result = mysqli_query($conn, $surveyQuery);

            if ($result && mysqli_num_rows($result) > 0):
                foreach ($result as $row):
            ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-4">
                        <div class="card shadow-lg h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-center"><?= htmlspecialchars($row['name']); ?></h5>
                                <?php
                                // Get the number of questions in the survey
                                $survey_id = $row['id'];
                                $questionQuery = "SELECT COUNT(*) as question_count FROM questions WHERE survey_id = $survey_id";
                                $questionResult = mysqli_query($conn, $questionQuery);
                                $questionCount = mysqli_fetch_assoc($questionResult)['question_count'];
                                ?>
                                <p class="text-center"><?= $questionCount ?> question(s) in this survey</p>
                                <div class="mt-auto text-center">
                                    <a href="survey.php?id=<?= $row['id']; ?>" class="btn text-white">
                                        <i class="bi bi-eye align-center"></i> View Survey
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            else:
            ?>
                <div class="col-12">
                    <h5 class="text-center">No approved surveys to display.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>





<div class="py-4 bg-custom2" style="width:100%; margin-top: 10px;">
</div>


<script>
    // Sequentially add the `visible` class to each .post-item so they fade in one-by-one.
    // Uses a small stagger delay. If you prefer animation on scroll, we can switch to IntersectionObserver.
    document.addEventListener('DOMContentLoaded', function () {
        // First animate hero/stagger items in order (header, subtitle, description, button)
        const staggerItems = document.querySelectorAll('.stagger-item');
        staggerItems.forEach((el, i) => {
            const delay = i * 300; // 300ms between hero lines
            setTimeout(() => el.classList.add('visible'), delay);
        });

        // Then animate post items after a short offset so header animations feel complete
        const posts = document.querySelectorAll('.post-item');
        const postsStartDelay = Math.max(600, staggerItems.length * 300 + 150); // ensure posts start after hero
        posts.forEach((el, i) => {
            const delay = postsStartDelay + i * 120;
            setTimeout(() => el.classList.add('visible'), delay);
        });

        // -- Hide/reveal hero on scroll (fade out when scrolling down, reappear on scroll up) --
        // Respect user's reduced-motion preference
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!prefersReduced) {
            let lastY = window.scrollY;
            const deltaThreshold = 8; // minimal delta to trigger
            const hero = document.querySelector('.text-container');

            if (hero) {
                let scheduled = false;
                window.addEventListener('scroll', function () {
                    // Throttle via requestAnimationFrame for smoother behavior
                    if (scheduled) return;
                    scheduled = true;
                    window.requestAnimationFrame(() => {
                        const y = window.scrollY || window.pageYOffset;
                        const dy = y - lastY;

                        // If scrolled down enough and we're beyond the top area, hide hero
                        if (dy > deltaThreshold && y > 60) {
                            hero.classList.add('hidden');
                        }

                        // If scrolled up enough, show hero again
                        if (lastY - y > deltaThreshold || y <= 60) {
                            hero.classList.remove('hidden');
                        }

                        lastY = y;
                        scheduled = false;
                    });
                }, { passive: true });
            }
        }
    });
</script>
<?php include('includes/footer.php'); ?>
</body>
</html>