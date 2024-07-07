<?php
include 'conn.php';
session_start(); // Start the session

// Check if agenda_name and agenda_date are set and not empty
if (isset($_POST['agenda_name'], $_POST['agenda_date']) && !empty($_POST['agenda_name']) && !empty($_POST['agenda_date'])) {
    $agendaName = $_POST['agenda_name'];
    $agendaDate = $_POST['agenda_date'];

    // Check if the selected_team session variable is set
    if (isset($_SESSION['selected_team'])) {
        $selected_team = $_SESSION['selected_team'];
    } else {
        echo json_encode(["error" => "No team selected."]);
        exit;
    }

    // Escape special characters to prevent SQL injection
    $agendaName = $conn->real_escape_string($agendaName);
    $agendaDate = $conn->real_escape_string($agendaDate);
    $selected_team = $conn->real_escape_string($selected_team);

    // Insert new row into mt_agenda_list table
    $insertSql = "INSERT INTO mt_agenda_list (agenda_name, agenda_date, module_team) VALUES ('$agendaName', '$agendaDate', '$selected_team')";
    if ($conn->query($insertSql) === TRUE) {
        // Retrieve the auto-generated agenda_id
        $agendaId = $conn->insert_id;

        // Fetch all member_id and department values from domm_module_team_members table
        $memberQuery = "SELECT member_id, department FROM domm_module_team_members";
        $memberResult = $conn->query($memberQuery);

        if ($memberResult->num_rows > 0) {
            while ($memberRow = $memberResult->fetch_assoc()) {
                $memberId = $memberRow['member_id'];
                $department = $memberRow['department'];

                // Insert into domm_module_team_member_attendance table
                $insertAttendanceSql = "INSERT INTO domm_module_team_member_attendance (agenda_id, member_id, department, present, absent, substituted) VALUES ($agendaId, $memberId, '$department', 0, 0, 0)";
                if (!$conn->query($insertAttendanceSql)) {
                    echo json_encode(["error" => "Error inserting member ID: $memberId into attendance: " . $conn->error]);
                    exit;
                }
            }
        }

        // Fetch all guest_id, guest_name, and department values from domm_guests table
        $guestQuery = "SELECT guest_id, guest_name, department FROM domm_guests";
        $guestResult = $conn->query($guestQuery);

        if ($guestResult->num_rows > 0) {
            while ($guestRow = $guestResult->fetch_assoc()) {
                $guestId = $guestRow['guest_id'];
                $guestName = $guestRow['guest_name'];
                $department = $guestRow['department'];

                // Insert into domm_module_team_guest_attendance table
                $insertGuestAttendanceSql = "INSERT INTO domm_module_team_guest_attendance (agenda_id, guest_id, department, substitute, present) VALUES ($agendaId, $guestId, '$department', NULL, 0)";
                if (!$conn->query($insertGuestAttendanceSql)) {
                    echo json_encode(["error" => "Error inserting guest ID: $guestId into guest attendance: " . $conn->error]);
                    exit;
                }
            }
        }

        // Return the new agenda ID as JSON
        echo json_encode(["success" => true, "agenda_id" => $agendaId]);
    } else {
        echo json_encode(["error" => "Error creating agenda: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Please provide both agenda name and agenda date."]);
}
?>
