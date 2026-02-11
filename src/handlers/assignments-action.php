<?php
include '../../config/db.php';
include '../includes/functions.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'save') {
    $group_id = $_POST['group_id'];
    $chapter_name = $_POST['chapter_name'];
    $topics_text = $_POST['topics_text'];
    $topic_ids = $_POST['topic_ids']; 
    $m_code = $_POST['module_code'];

    mysqli_begin_transaction($conn);
    try {
        $ids_str = implode(',', array_map('intval', $topic_ids));
        mysqli_query($conn, "UPDATE sub_chapters SET is_completed = 2 WHERE id IN ($ids_str)");

        $sql = "INSERT INTO assignments (group_id, chapter_name, module_code, topic_names, topic_ids) 
                VALUES ('$group_id', '$chapter_name', '$m_code', '$topics_text', '$ids_str')";
        mysqli_query($conn, $sql);

        recordActivity($conn, "Assigned $chapter_name to group ID: $group_id");
        mysqli_commit($conn);
        echo json_encode(['status' => 'success', 'title' => 'Deployed', 'description' => 'Assignment created successfully.']);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'description' => $e->getMessage()]);
    }
}

if ($action === 'delete') {
    $aid = $_POST['assign_id'];
    $res = mysqli_query($conn, "SELECT topic_ids FROM assignments WHERE id = '$aid'");
    $row = mysqli_fetch_assoc($res);
    if($row) {
        $ids = $row['topic_ids'];
        mysqli_query($conn, "UPDATE sub_chapters SET is_completed = 0 WHERE id IN ($ids)");
    }
    mysqli_query($conn, "DELETE FROM assignments WHERE id = '$aid'");
    echo json_encode(['status' => 'success']);
}