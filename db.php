<?php
    $servername = "localhost";
    $username = "root";
    $password = "CatLover123"; // Replace with your actual password
    $dbname = "attendance_system";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>