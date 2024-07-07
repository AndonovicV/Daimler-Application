<?php
include 'conn.php';
include_once('navigation.php');

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = ""; // Default value if not set
}

// Check only essential fields: guest_name and agenda_id
if (isset($_POST['agenda_id']) && isset($_POST['guest_name'])) {
    $agendaId = intval($_POST['agenda_id']);
    $guestName = $_POST['guest_name'];
    $department = $_POST['department'] ?? ''; // Use null coalescing operator to handle missing optional data
    $substitute = $_POST['substitute'] ?? '';
    $moduleTeamName = $selected_team; // From session

    // Insert new guest into the domm_guests table with basic info
    $sql = "INSERT INTO domm_guests (guest_name, department, module_team_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('sss', $guestName, $department, $moduleTeamName);
        if ($stmt->execute()) {
            $guestId = $stmt->insert_id;
            $stmt->close();

            // Try to link the new guest with an agenda, not required for success
            $sql = "INSERT INTO domm_module_team_guest_attendance (agenda_id, guest_id, substitute, present) VALUES (?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('iis', $agendaId, $guestId, $substitute);
                $stmt->execute(); // Execute but do not hinge success/failure on this action
                $stmt->close();
            }
            
            // Return success as long as the main guest info is added
            echo json_encode(['status' => 'success', 'guest_id' => $guestId, 'guest_name' => $guestName, 'department' => $department, 'module_team_name' => $moduleTeamName, 'substitute' => $substitute]);
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
