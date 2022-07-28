<?php
define('USE_MYSQL', 1);
$host = getenv('MYSQL_HOST');
$user = "admin";
$pass = "123";
$banco = "bancotodo";

// Create connection
$conn = new mysqli($host, $user, $pass, $banco);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
