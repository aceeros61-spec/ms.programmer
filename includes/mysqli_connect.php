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


if (!defined('BASE_URL')) {
    define("BASE_URL", "http://localhost/Kind_step");
}
if (!defined('ADMIN_URL')) {
    define("ADMIN_URL", "http://localhost/Kind_step/admin");
}
?>