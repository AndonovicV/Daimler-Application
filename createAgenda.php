<?php
include 'conn.php';
session_start(); // Start the session

// Insert new row (Table) into mt_agenda_list table
if (isset($_POST['agenda_name'], $_POST['agenda_date']) && !empty($_POST['agenda_name']) && !empty($_POST['agenda_date'])) {
    $agendaName = $_POST['agenda_name'];
    $agendaDate = $_POST['agenda_date'];

    // Check if the session variable is set
    if (isset($_SESSION['selected_team'])) {
        $selected_team = $_SESSION['selected_team'];
        echo "Selected Team: " . $selected_team . "<br>";
    } else {
        echo "No team selected.<br>";
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
        echo "New agenda ID: " . $agendaId . "<br>";

        // Fetch all member_id and department values from module_team_members table
        $memberQuery = "SELECT member_id, department FROM module_team_members";
        $memberResult = $conn->query($memberQuery);

        if ($memberResult->num_rows > 0) {
            while ($memberRow = $memberResult->fetch_assoc()) {
                $memberId = $memberRow['member_id'];
                $department = $memberRow['department'];

                // Insert into module_team_member_attendance table
                $insertAttendanceSql = "INSERT INTO module_team_member_attendance (agenda_id, member_id, department, present, absent, substituted) VALUES ($agendaId, $memberId, '$department', 0, 0, 0)";
                if ($conn->query($insertAttendanceSql) === TRUE) {
                    echo "Inserted member ID: $memberId with department: $department into attendance for agenda ID: $agendaId<br>";
                } else {
                    echo "Error inserting member ID: $memberId into attendance: " . $conn->error . "<br>";
                }
            }
        } else {
            echo "No members found in module_team_members table.<br>";
        }

        // Fetch all guest_id, guest_name, and department values from guests table
        $guestQuery = "SELECT guest_id, guest_name, department FROM guests";
        $guestResult = $conn->query($guestQuery);

        if ($guestResult->num_rows > 0) {
            while ($guestRow = $guestResult->fetch_assoc()) {
                $guestName = $guestRow['guest_name'];
                $department = $guestRow['department'];

                // Insert into module_team_guest_guest_attendance table
                $insertGuestAttendanceSql = "INSERT INTO module_team_guest_guest_attendance (agenda_id, guest_name, department, substitute, present) VALUES ($agendaId, '$guestName', '$department', NULL, 0)";
                if ($conn->query($insertGuestAttendanceSql) === TRUE) {
                    echo "Inserted guest name: $guestName with department: $department into guest attendance for agenda ID: $agendaId<br>";
                } else {
                    echo "Error inserting guest name: $guestName into guest attendance: " . $conn->error . "<br>";
                }
            }
        } else {
            echo "No guests found in guests table.<br>";
        }
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
} else {
    echo "Please provide both agenda name and agenda date.<br>";
}
?>
