<?php
include '../../config/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? 'save';

    // --- DELETE ACTION ---
    if ($action === 'delete') {
        $module_id = $_POST['module_id'] ?? 0;

        if (!$module_id) throw new Exception("Invalid Module ID");

        $res = mysqli_query($conn, "SELECT module_code FROM modules WHERE id = $module_id");
        $row = mysqli_fetch_assoc($res);
        $m_code = $row['module_code'] ?? 'Unknown';

        $sql = "DELETE FROM modules WHERE id = $module_id";

        if (mysqli_query($conn, $sql)) {
            recordActivity($conn, "Deleted Module: EPU-$m_code");
            echo json_encode([
                'status' => 'success',
                'title' => 'Module Deleted',
                'description' => "EPU-$m_code has been removed permanently."
            ]);
        } else {
            throw new Exception(mysqli_error($conn));
        }
        exit;
    }

    // --- SAVE ACTION ---
    $module_code = trim($_POST['module_code'] ?? '');
    $module_title = trim($_POST['module_title'] ?? '');
    $chapters = $_POST['chapters'] ?? [];

    if (empty($module_code) || empty($module_title)) {
        echo json_encode(['status' => 'error', 'title' => 'Missing Info', 'description' => 'Code and Title are required.']);
        exit;
    }

    $sql = "INSERT INTO modules (module_code, module_title) VALUES ('" . mysqli_real_escape_string($conn, $module_code) . "', '" . mysqli_real_escape_string($conn, $module_title) . "')";

    if (mysqli_query($conn, $sql)) {
        $module_id = mysqli_insert_id($conn);
        foreach ($chapters as $ch) {
            $ch_title = mysqli_real_escape_string($conn, $ch['title']);
            mysqli_query($conn, "INSERT INTO chapters (module_id, chapter_title) VALUES ('$module_id', '$ch_title')");
            $chapter_id = mysqli_insert_id($conn);
            if (isset($ch['subs'])) {
                foreach ($ch['subs'] as $sub) {
                    $sub_title = mysqli_real_escape_string($conn, $sub);
                    mysqli_query($conn, "INSERT INTO sub_chapters (chapter_id, sub_title) VALUES ('$chapter_id', '$sub_title')");
                }
            }
        }
        recordActivity($conn, "Added Module: EPU-$module_code");
        echo json_encode(['status' => 'success', 'title' => 'Module Saved', 'description' => "EPU-$module_code recorded successfully."]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'title' => 'System Error', 'description' => $e->getMessage()]);
}
