<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    $asap = isset($_POST['asap']) ? intval($_POST['asap']) : 0;

    if ($task_id > 0) {
        $sql = "UPDATE tasks SET asap = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $asap, $task_id);
            if ($stmt->execute()) {
                echo "ASAP status updated successfully";
            } else {
                echo "Error updating ASAP status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Invalid task ID";
    }
}

$conn->close();
?>
