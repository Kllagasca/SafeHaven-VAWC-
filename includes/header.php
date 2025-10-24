<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if(isset($pageTitle)) {echo $pageTitle; }else{echo 'Gender and Development Online Database';} ?></title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<style>
    body {
    font-family: 'Poppins', sans-serif;
}
</style>
<body>

<?php require 'config/function.php';?>

<?php include('navbar.php');?>

