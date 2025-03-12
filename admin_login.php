<?php
    session_start();
    require 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $query = "SELECT id, password_hash FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
?>