<?php
include '../../config/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';

    // --- DELETE USER ---
    if ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        // Get name for logging
        $res = mysqli_query($conn, "SELECT user_name FROM users WHERE id = '$id'");
        $user = mysqli_fetch_assoc($res);
        $name = $user['user_name'] ?? 'Unknown';

        if (mysqli_query($conn, "DELETE FROM users WHERE id = '$id'")) {
            recordActivity($conn, "Deleted User: $name");
            echo json_encode(['status' => 'success', 'title' => 'User Removed', 'description' => "$name has been deleted."]);
        } else {
            throw new Exception(mysqli_error($conn));
        }
        exit;
    }

    // --- SAVE / UPDATE USER ---
    $id = $_POST['user_id'] ?? ''; // If exists, we are updating
    $name = trim($_POST['user_name'] ?? '');
    $phone = trim($_POST['user_phone'] ?? '');

    if (empty($name) || empty($phone)) {
        echo json_encode(['status' => 'error', 'title' => 'Validation Error', 'description' => 'Name and Phone are required.']);
        exit;
    }

    if (!strpos($phone, ' ')) {
        $clean = str_replace('+94', '', $phone);
        $clean = ltrim($clean, '0'); 
        $phone = "+94 " . substr($clean, 0, 2) . " " . substr($clean, 2, 3) . " " . substr($clean, 5, 4);
    }

    $safe_name = mysqli_real_escape_string($conn, $name);
    $safe_phone = mysqli_real_escape_string($conn, $phone);

    if ($id) {
        // Update existing user
        $sql = "UPDATE users SET user_name = '$safe_name', user_phone = '$safe_phone' WHERE id = '$id'";
        $logMsg = "Updated User: $name";
        $title = "User Updated";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (user_name, user_phone) VALUES ('$safe_name', '$safe_phone')";
        $logMsg = "Added New User: $name";
        $title = "User Created";
    }

    if (mysqli_query($conn, $sql)) {
        recordActivity($conn, $logMsg);
        echo json_encode(['status' => 'success', 'title' => $title, 'description' => "$name's details saved."]);
    } else {
        throw new Exception(mysqli_error($conn));
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'title' => 'System Error', 'description' => $e->getMessage()]);
}
