<?php
// handlers/curriculum-action.php
include '../../config/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';

    // Handle Delete
    if ($action === 'delete') {
        $module_id = mysqli_real_escape_string($conn, $_POST['module_id']);
        mysqli_query($conn, "DELETE FROM modules WHERE id = '$module_id'");
        echo json_encode(['status' => 'success', 'title' => 'Deleted', 'description' => 'Module removed.']);
        exit;
    }

    // Handle Save
    $module_code = $_POST['module_code'] ?? '';
    $module_title = $_POST['module_title'] ?? '';
    $chapters = $_POST['chapters'] ?? [];

    if (empty($module_code) || empty($module_title)) {
        echo json_encode(['status' => 'error', 'title' => 'Error', 'description' => 'Required fields missing.']);
        exit;
    }

    // Insert Module
    $sqlMod = "INSERT INTO modules (module_code, module_title) VALUES ('$module_code', '$module_title')";
    if (mysqli_query($conn, $sqlMod)) {
        $module_id = mysqli_insert_id($conn);

        // Loop through chapters for numbering
        foreach ($chapters as $index => $ch) {
            $chapter_no = $index + 1; // Creates 1, 2, 3...
            $numbered_ch_title = $chapter_no . " " . $ch['title'];
            
            $safe_ch_title = mysqli_real_escape_string($conn, $numbered_ch_title);
            mysqli_query($conn, "INSERT INTO chapters (module_id, chapter_title) VALUES ('$module_id', '$safe_ch_title')");
            $chapter_id = mysqli_insert_id($conn);

            // Loop through sub-chapters for numbering (e.g., 1.1, 1.2)
            if (isset($ch['subs'])) {
                foreach ($ch['subs'] as $subIndex => $sub_title) {
                    $sub_no = $subIndex + 1;
                    $numbered_sub_title = $chapter_no . "." . $sub_no . " " . $sub_title;

                    $safe_sub_title = mysqli_real_escape_string($conn, $numbered_sub_title);
                    mysqli_query($conn, "INSERT INTO sub_chapters (chapter_id, sub_title) VALUES ('$chapter_id', '$safe_sub_title')");
                }
            }
        }

        // Log the activity
        recordActivity($conn, "Added Module: EPU-$module_code");

        echo json_encode(['status' => 'success', 'title' => 'Saved', 'description' => 'Curriculum saved']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'title' => 'System Error', 'description' => $e->getMessage()]);
}