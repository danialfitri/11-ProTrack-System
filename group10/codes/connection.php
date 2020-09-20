<?php
    $server = "localhost";
    $user = "root";
    $password = "";
    $database = "opendb";

    $conn = new mysqli($server, $user, $password, $database);

    if($conn->connect_error){
        die("Database connection error ! ".$conn->connect_error);
    }

?>