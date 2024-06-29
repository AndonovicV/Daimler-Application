<?php
include 'conn.php';

if (isset($_POST['guest_id'])) {
    $guestId = intval($_POST['guest_id']);

    $sql = "DELETE FROM module_team_guest_attendance WHERE guest_id = ?";
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

// In case they want to remove guests from the main guest table:
    if (isset($_POST['guest_id'])) {
    $guestId = intval($_POST['guest_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete from module_team_guest_attendance table
        $sql = "DELETE FROM module_team_guest_attendance WHERE guest_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $guestId);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete guest attendance.');
            }
            $stmt->close();
        } else {
            throw new Exception($conn->error);
        }

        // Delete from guests table
        $sql = "DELETE FROM guests WHERE guest_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $guestId);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete guest.');
            }
            $stmt->close();
        } else {
            throw new Exception($conn->error);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No guest ID provided.']);
}
?>
