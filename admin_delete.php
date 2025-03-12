<?php
    session_start();
    require 'db.php';

    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_admin_id"])) {
        $deleteAdminId = $_POST["delete_admin_id"];

        // Check the total number of admins
        $admin_count_sql = "SELECT COUNT(*) AS total FROM admin";
        $admin_count_result = $conn->query($admin_count_sql);
        $admin_count = $admin_count_result->fetch_assoc()['total'];

        if ($admin_count > 1) {
            $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
            $stmt->bind_param("i", $deleteAdminId);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Admin deleted successfully.";
            } else {
                $_SESSION['message'] = "Failed to delete admin.";
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Cannot delete the last admin.";
        }
    }

    header("Location: admin_dashboard.php");
    exit();
?>
