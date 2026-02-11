<?php
include '../../config/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';

    // --- DELETE GROUP ---
    if ($action === 'delete') {
        $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
        
        $res = mysqli_query($conn, "SELECT group_name FROM study_groups WHERE id = '$group_id'");
        $group = mysqli_fetch_assoc($res);
        $name = $group['group_name'] ?? 'Unknown Group';

        if (mysqli_query($conn, "DELETE FROM study_groups WHERE id = '$group_id'")) {
            recordActivity($conn, "Deleted $name");
            echo json_encode(['status' => 'success', 'title' => 'Group Removed', 'description' => "$name has been deleted."]);
        } else {
            throw new Exception(mysqli_error($conn));
        }
        exit;
    }

    // --- CREATE GROUP ---
    if ($action === 'save') {
        $group_name = $_POST['group_name'];
        $member_ids = $_POST['member_ids'] ?? [];

        if (empty($member_ids)) {
            echo json_encode(['status' => 'error', 'title' => 'No Members', 'description' => 'Please select at least one member.']);
            exit;
        }

        mysqli_begin_transaction($conn);

        // Insert Group
        $sqlGroup = "INSERT INTO study_groups (group_name) VALUES ('$group_name')";
        mysqli_query($conn, $sqlGroup);
        $group_id = mysqli_insert_id($conn);

        // Insert Members
        foreach ($member_ids as $u_id) {
            $u_id = mysqli_real_escape_string($conn, $u_id);
            mysqli_query($conn, "INSERT INTO group_members (group_id, user_id) VALUES ('$group_id', '$u_id')");
        }

        recordActivity($conn, "Created new group: $group_name with " . count($member_ids) . " members");
        
        mysqli_commit($conn);
        echo json_encode(['status' => 'success', 'title' => 'Group Created', 'description' => "$group_name is now active."]);
    }

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'title' => 'System Error', 'description' => $e->getMessage()]);
}