<?php
    require 'db.php';

    $sql = "SELECT al.user_id, u.full_name, 
            CONCAT(al.year_record, '-', LPAD(al.month_record, 2, '0'), '-', LPAD(al.day_record, 2, '0')) AS date_record,
            TIME_FORMAT(al.time_in, '%h:%i:%s %p') AS formatted_time_in,
            TIME_FORMAT(al.time_out, '%h:%i:%s %p') AS formatted_time_out
            FROM attendance_logs al
            JOIN users u ON al.user_id = u.user_id
            ORDER BY al.year_record DESC, al.month_record DESC, al.day_record DESC, al.time_in DESC";

    $result = $conn->query($sql);
    $attendance = [];

    while ($row = $result->fetch_assoc()) {
        $attendance[] = [
            'user_id' => $row['user_id'],
            'full_name' => $row['full_name'],
            'date_record' => $row['date_record'],
            'time_in' => $row['formatted_time_in'] ?? '',
            'time_out' => $row['formatted_time_out'] ?? ''
        ];
    }

    echo json_encode($attendance);
    $conn->close();
?>