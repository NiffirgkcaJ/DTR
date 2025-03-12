<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = trim($_POST['user_id']); // Collect user_id as a string
        $full_name = trim($_POST['full_name']);
        $floor_assigned = trim($_POST['floor_assigned']);

        if (!empty($user_id) && !empty($full_name) && !empty($floor_assigned)) {
            $stmt = $conn->prepare("INSERT INTO users (user_id, full_name, floor_assigned) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_id, $full_name, $floor_assigned); // Change to "sss" for string data

            if ($stmt->execute()) {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error: All fields are required.";
        }
    }
    $conn->close();
?>