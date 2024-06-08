<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agenda_id = intval($_POST['agenda_id']);
    $member_id = intval($_POST['member_id']);
    $status = intval($_POST['status']);
    $checkbox_name = $_POST['checkbox_name'];

    if ($checkbox_name == 'status[' . $member_id . ']') {
        $present = ($status == 1) ? 1 : 0;
        $absent = ($status == 2) ? 1 : 0;
        $substituted = ($status == 3) ? 1 : 0;

        $sql = "UPDATE module_team_member_attendance 
                SET present = ?, absent = ?, substituted = ? 
                WHERE agenda_id = ? AND member_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('iiiii', $present, $absent, $substituted, $agenda_id, $member_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        }
    } elseif ($checkbox_name == 'present[' . $member_id . ']') {  // Changed $guest_id to $member_id
        $guest_id = intval($member_id);  // Since it's actually a guest ID in this context
        $present = ($status == 1) ? 1 : 0;

        $sql = "UPDATE module_team_guest_guest_attendance 
                SET present = ? 
                WHERE agenda_id = ? AND id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('iii', $present, $agenda_id, $guest_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
