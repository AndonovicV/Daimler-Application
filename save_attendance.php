<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agenda_id = intval($_POST['agenda_id']);

    // Process member attendance
    if (isset($_POST['member_id']) && isset($_POST['status'])) {
        foreach ($_POST['member_id'] as $index => $member_id) {
            $member_id = intval($member_id);
            $status = intval($_POST['status'][$member_id]);

            $present = ($status == 1) ? 1 : 0;
            $absent = ($status == 2) ? 1 : 0;
            $substituted = ($status == 3) ? 1 : 0;

            $sql = "UPDATE module_team_member_attendance 
                    SET present = ?, absent = ?, substituted = ? 
                    WHERE agenda_id = ? AND member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiiii', $present, $absent, $substituted, $agenda_id, $member_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Process guest attendance
    if (isset($_POST['present'])) {
        foreach ($_POST['present'] as $guest_id => $present) {
            $guest_id = intval($guest_id);
            $present = ($present == 'on') ? 1 : 0;

            $sql = "UPDATE module_team_guest_guest_attendance 
                    SET present = ? 
                    WHERE agenda_id = ? AND id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $present, $agenda_id, $guest_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
