<?php
/**
 * Saves a message to the activity_log table
 */
function recordActivity($conn, $message) {
    $cleanMessage = mysqli_real_escape_string($conn, $message);
    $sql = "INSERT INTO activity_log (activity_description) VALUES ('$cleanMessage')";
    mysqli_query($conn, $sql);
}