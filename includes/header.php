<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if(isset($pageTitle)) {echo $pageTitle; }else{echo 'SafeHaven';} ?></title>
        <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/logo.png">
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" href="../assets/img/logo.png" type="image/png">">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<title>
    SafeHaven
</title>
<style>
    body {
    font-family: 'Poppins', sans-serif;
}
</style>
<body>

<?php require 'config/function.php';?>

<?php include('navbar.php');?>

