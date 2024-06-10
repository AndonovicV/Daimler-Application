<?php
include 'conn.php';

if (isset($_POST['agenda_id']) && isset($_POST['guest_name'])) {
    $agendaId = intval($_POST['agenda_id']);
    $guestName = $_POST['guest_name'];
    $department = $_POST['department']; // Assuming department input is also collected in modal
    $substitute = $_POST['substitute']; // Assuming substitute input is also collected in modal

    $sql = "INSERT INTO guests (guest_name, department) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('ss', $guestName, $department);
        if ($stmt->execute()) {
            $guestId = $stmt->insert_id;
            $stmt->close();

            $sql = "INSERT INTO module_team_guest_guest_attendance (agenda_id, guest_id, substitute, present) VALUES (?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('iis', $agendaId, $guestId, $substitute);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'guest_id' => $guestId, 'guest_name' => $guestName, 'department' => $department, 'substitute' => $substitute]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to add guest to attendance.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => $conn->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add guest.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}
?>
