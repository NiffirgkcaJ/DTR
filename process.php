<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require 'db.php';

    if (isset($_POST['record'])) {
        $user_id = trim($_POST['user_id']);

        if (empty($user_id)) {
            header("Location: index.php?message=" . urlencode("Please enter a valid ID."));
            exit();
        }

        // Set timezone and get current time values
        date_default_timezone_set('Asia/Manila');
        $current_time = date('H:i:s'); 
        $current_hour = date('H');
        $current_year = date('Y');
        $current_month = date('m');
        $current_day = date('d');
        $period = ($current_hour < 12) ? 'AM' : 'PM';

        // Check if user exists and get full_name
        $check_user = "SELECT full_name FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($check_user);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            header("Location: index.php?message=" . urlencode("User ID not found."));
            exit();
        }

        $full_name = $user['full_name']; // Get user's full name

        // Check for an unfinished time-in (time_out IS NULL) regardless of period
        $check_unfinished = "SELECT id, year_record, month_record, day_record FROM attendance_logs 
                             WHERE user_id = ? AND time_out IS NULL 
                             ORDER BY year_record DESC, month_record DESC, day_record DESC, time_in DESC LIMIT 1";
        $stmt = $conn->prepare($check_unfinished);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // If the unfinished record is from today, complete the time-out
            if ($row['year_record'] == $current_year && 
                $row['month_record'] == $current_month && 
                $row['day_record'] == $current_day) {
                
                $update_query = "UPDATE attendance_logs SET time_out = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("si", $current_time, $row['id']);
                $stmt->execute();
                $message = "$period time-out recorded for $full_name.";

            } else {
                // If the last unfinished time-in was from a previous day, just insert a new time-in
                $insert_query = "INSERT INTO attendance_logs (user_id, year_record, month_record, day_record, period, time_in) 
                                 VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("siiiss", $user_id, $current_year, $current_month, $current_day, $period, $current_time);
                $stmt->execute();
                $message = "New day detected: $period time-in recorded for $full_name.";
            }
        } else {
            // If no unfinished time-in exists, insert a new time-in as usual
            $insert_query = "INSERT INTO attendance_logs (user_id, year_record, month_record, day_record, period, time_in) 
                             VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("siiiss", $user_id, $current_year, $current_month, $current_day, $period, $current_time);
            $stmt->execute();
            $message = "$period time-in recorded for $full_name.";
        }

        header("Location: index.php?message=" . urlencode($message));
        exit();
    }
?>