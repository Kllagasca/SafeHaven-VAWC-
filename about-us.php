<?php
$pageTitle = "About Us";
include('includes/navbar.php');
?>


<style>
    ::-webkit-scrollbar {
        display: none;
    }


    .bg-image {
        background: url('assets/img/bg-about.png') no-repeat center center;
        background-size: cover;
        height: 805px; /* Default height */
        width: 100%;
    }


    .header-text {
        font-family: 'Canva Sans', sans-serif;
        font-size: 70px;
        font-weight: bold;
        line-height: 1.4;
        text-align: left;
        color: white;
        margin: 0;
    }


    .sub-text {
        font-family: 'Canva Sans', sans-serif;
        font-size: 35px;
        text-align: left;
        color: white;
        margin-top: 0;
    }


    .bg-custom2 {
        background: radial-gradient(circle at 0% 0%, #004aad, #cb6ce6);
    }


    /* Media Queries */
    @media (max-width: 991px) {
        .bg-image {
            height: auto;
            padding: 20px;
        }


        .header-text {
            font-size: 50px;
            text-align: center;
        }


        .sub-text {
            font-size: 25px;
            text-align: center;
        }


        .col-md-7, .col-md-5 {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: center;
            padding: 0;
        }


        .col-md-5 {
            margin-top: 20px;
        }


        .team {
            font-size: 16px;
        }


        .header {
            font-size: 40px;
        }


        img {
            width: 350px;
        }
    }


    @media (max-width: 576px) {
        .header-text {
            font-size: 40px;
        }


        .sub-text {
            font-size: 20px;
        }


        img {
            width: 300px;
        }
    }
</style>


<div>
    <div class="bg-image d-flex flex-wrap align-items-center">
        <!-- Text Section -->
        <div class="col-md-7 mb-0 px-4">
            <div class="d-flex flex-column justify-content-center" style="height: 100%;">
                <h1 class="header-text">Honey Bunch Company</h1>
                <p class="sub-text">Empowering Equality, Building Futures</p>
                <p style="font-size: 20px; color: white;">
                    A fascinating and complex topic that encompasses various social, cultural, and biological factors.
                    It involves understanding how gender identity and roles are formed, how they evolve over time, and
                    the impact of gender on individual experiences and societal structures.
                </p>
            </div>
        </div>


        <!-- Image Section -->
        <div class="col-md-5 mb-0 d-flex justify-content-center align-items-center">
            <div style="width: 450px; height: 450px; border: 35px solid #554fb0; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                <img src="assets/img/logo.png" alt="Honey Bunch Logo" class="img-fluid rounded-circle">
            </div>
        </div>
    </div>


    <div class="text-center mt-4 px-3">
        <h6 class="team mt-5 text-uppercase" style="color: #5e17eb; letter-spacing: 1.5px;">
            MEET OUR TEAM
        </h6>
        <h1 class="header" style="margin: 0 0 15px;">
            Experts Behind This Website
        </h1>
        <div class="mx-auto" style="max-width: 1200px;">
            <p style="font-size: 16px; line-height: 1.6; color: black;">
                The Gender and Development Online Database was created by a multidisciplinary team of
                gender specialists, data analysts, software developers, and policy advisors. Together,
                they ensured the platform is inclusive, accurate, user-friendly, and aligned with global
                standards, providing a valuable resource for advancing gender equity and informed decision-making.
            </p>
        </div>
    </div>
</div>


<?php include_once('about.php'); ?>


<div class="py-4 bg-custom2 w-100 mt-3">
</div>
