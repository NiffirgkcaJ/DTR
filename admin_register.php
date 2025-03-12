<?php
    session_start();
    require 'db.php';

    // Check if at least one admin exists
    $query = "SELECT COUNT(*) as count FROM admin";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        die("Registration is disabled until an admin exists.");
    }

    // Only logged-in admins can register new admins
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        // Check if username already exists
        $check_query = "SELECT COUNT(*) as count FROM admin WHERE username = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $check_result = $stmt->get_result();
        $check_row = $check_result->fetch_assoc();

        if ($check_row['count'] > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // Insert new admin
            $insert = "INSERT INTO admin (username, password_hash) VALUES (?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();

            header("Location: admin_dashboard.php?message=New admin added!");
            exit();
        }
    }
?>