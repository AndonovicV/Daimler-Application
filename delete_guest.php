<?php
include 'conn.php';

if (isset($_POST['guest_id'])) {
    $guestId = intval($_POST['guest_id']);

    $sql = "DELETE FROM module_team_guest_guest_attendance WHERE guest_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param('i', $guestId);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete guest attendance.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No guest ID provided.']);
}
?>
