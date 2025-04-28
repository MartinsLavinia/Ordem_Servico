<?php
// connection.php

$host = "localhost";
$username = "root";          
$password = "";              
$database = "oscd_lamanna";  

$connection = mysqli_connect($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

?>