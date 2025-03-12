<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
        $user_id = trim($_POST['delete_user_id']); // User ID is a string, so use trim()

        // First, delete related attendance logs
        $stmt_logs = $conn->prepare("DELETE FROM attendance_logs WHERE user_id = ?");
        $stmt_logs->bind_param("s", $user_id); // User ID is a string, so use "s"
        $stmt_logs->execute();
        $stmt_logs->close();

        // Now, delete the user
        $stmt_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt_user->bind_param("s", $user_id);
        
        if ($stmt_user->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt_user->error;
        }

        $stmt_user->close();
    }
    $conn->close();
?>