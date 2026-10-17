<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kind_steps_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");


?>