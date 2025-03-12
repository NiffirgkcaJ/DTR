<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = $_POST['user_id'] ?? '';
        $field = $_POST['field'] ?? '';
        $value = trim($_POST['value'] ?? '');

        if (empty($user_id) || empty($field) || empty($value)) {
            die("Error: Missing required parameters");
        }

        // Prevent SQL injection by allowing only specific fields to be updated
        $allowed_fields = ['full_name', 'floor_assigned'];
        if (in_array($field, $allowed_fields)) {
            $stmt = $conn->prepare("UPDATE users SET $field = ? WHERE user_id = ?");
            $stmt->bind_param("ss", $value, $user_id); // Use "ss" since both are strings

            if ($stmt->execute()) {
                echo "Success";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            die("Error: Invalid field");
        }
    }
    $conn->close();
?>