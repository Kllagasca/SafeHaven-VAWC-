<?php

define('DB_SERVER',"localhost");
define('DB_USER',"root");
define('DB_PASSWORD',"");
define('DB_DATABASE',"gender_dev");

$conn = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);

if(!$conn){
    die("Connection Failed: ".mysqli_connect_error());
}

?>