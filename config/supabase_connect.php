<?php
$host = 'aws-0-ap-southeast-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.flxospozrbyrbitljilf';
$password = 'MfTFYtkorCmAZ3ug';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If connection is successful
    // echo "Connected to the Supabase database successfully!";
} catch (PDOException $e) {
    // If connection fails
    echo "Connection failed: " . $e->getMessage();
}
?>