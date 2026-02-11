<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam-scheduler-app";

try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>