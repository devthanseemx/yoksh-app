<?php
include '../../config/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

try {
    $sub_id = mysqli_real_escape_string($conn, $_POST['sub_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']); // 1 or 0

    // Get sub-topic name for the activity log
    $res = mysqli_query($conn, "SELECT sub_title FROM sub_chapters WHERE id = '$sub_id'");
    $row = mysqli_fetch_assoc($res);
    $title = $row['sub_title'] ?? 'Unknown Topic';

    $sql = "UPDATE sub_chapters SET is_completed = '$status' WHERE id = '$sub_id'";

    if (mysqli_query($conn, $sql)) {
        $msg = ($status == 1) ? "Completed topic: $title" : "Marked topic as incomplete: $title";
        recordActivity($conn, $msg);
        
        echo json_encode(['status' => 'success', 'message' => 'Progress updated']);
    } else {
        throw new Exception(mysqli_error($conn));
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}