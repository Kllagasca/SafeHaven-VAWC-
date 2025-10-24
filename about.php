<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Our Team</title>
</head>

<style>
.container1 {
    text-align: center;
}

.sub-container1 {
    max-width: 1200px;
    margin: auto;
    padding: 30px 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}


.teams {
    margin: 10px;
    padding: 22px;
    max-width: 30%;
    cursor: pointer;
    transition: 0.4s;
    box-sizing: border-box;
}


.teams:hover {
    background: violet;
    border-radius: 12px;
}


.teams img {
    width: 15vw; /* 15% of the viewport width */
    max-width: 200px; /* Limit the maximum size */
    height: auto; /* Automatically adjust height to maintain aspect ratio */
    aspect-ratio: 1 / 1; /* Ensure a perfect square */
    border-radius: 50%; /* Circular appearance */
    object-fit: cover; /* Center and crop the image */
    object-position: center; /* Focus on the center of the image */
}

.name {
    padding: 12px;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
}


.desig {
    font-weight: bold;
    font-style: italic;
    color: #888;
}


.about {
    margin: 20px 0;
    font-weight: lighter;
    color: #4e4e4e;
}


.social-links {
    margin: 14px;
}


.social-links a {
    display: inline-block;
    height: 30px;
    width: 30px;
    transition: .4s;
}


.social-links a:hover {
    transform: scale(1.5);
}


.social-links a i {
    color: #444;
}


@media screen and (max-width: 600px) {
    .teams {
        max-width: 100%;
    }
}
</style>

<body>
    <div id="team-section" class="container1">
        <div class="sub-container1">
            <div class="teams">
                <img src="assets/img/2.png" alt="">
                <div class="name">Kevin Lawrence Lagasca</div>
                <div class="desig">Programmer</div>
                <div class="about">
                    He is responsible for writing, testing, and maintaining the code that powers our platform. He ensures
                    the software runs smoothly and implements all necessary features. His expertise transforms ideas into
                    functional and efficient systems.
                </div>
                <div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-github"></i></a>
                </div>
            </div>
            <div class="teams">
                <img src="assets/img/6.png" alt="">
                <div class="name">Honey Fay Gomez </div>
                <div class="desig">Project Manager</div>
                <div class="about">She oversees the entire project, ensuring that tasks are completed on time.
                    She coordinate the team, set goals, and keep everyone aligned with the project's objectives. (eme naman)</div>


                <div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-github"></i></a>
                </div>
            </div>


            <div class="teams">
                <img src="assets/img/3.png" alt="">
                <div class="name">Glenn Ford Ariola </div>
                <div class="desig">Database Manager</div>
                <div class="about">He designs, manages, and secures the database systems.
                    He ensure that data is organized, accessible, and protected, supporting the platform’s functionality and reliability. </div>


                <div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-github"></i></a>
                </div>
            </div>


            <div class="teams">
                <img src="assets/img/5.png" alt="">
                <div class="name">Keith De Leon </div>
                <div class="desig">Technical Writer</div>
                <div class="about">She creates clear and concise documentation, guides, and tutorials for our platform.
                    She help users and team members understand how to navigate and use our system effectively, bridging the gap between technical details and user understanding. </div>


                <div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-github"></i></a>
                </div>
            </div>


            <div class="teams">
                <img src="assets/img/1.png" alt="">
                <div class="name">Raymar Manalo </div>
                <div class="desig">UI Designer</div>
                <div class="about">He create visually appealing and user-friendly interfaces. He focus on ensuring that our platform is intuitive and engaging for users.
                    His designs bring our vision to life and make user experiences seamless. </div>


                <div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-github"></i></a>
                </div>
            </div>


            <div class="teams">
                <img src="assets/img/4.png" alt="">
                <div class="name">Cristine Pacamara </div>
                <div class="desig">UI Designer</div>
                <div class="about">She focus on crafting the visual and interactive elements of our platform.
                    She ensure that every detail—from colors and typography to buttons and animations—enhances the user experience. </div>


                <div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-github"></i></a>
                </div>
        </div>
    </div>
</body>

</html>
