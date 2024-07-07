<?php
include 'conn.php';
session_start(); // Ensure the session is started if not already started

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = ""; // Default value if not set
}

if (isset($_GET['agenda_id'])) {
    $agendaId = intval($_GET['agenda_id']);

    // Prepare and execute the SQL query for attendance data with member names filtered by the selected module team
    $sql = "SELECT 
                a.*, 
                m.member_name, 
                m.department
            FROM 
                domm_module_team_member_attendance a
            JOIN 
                domm_module_team_members m ON a.member_id = m.member_id
            WHERE 
                a.agenda_id = ? AND
                m.module_team_name = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("is", $agendaId, $selected_team);
        $stmt->execute(); // Execute the prepared statement
        $result = $stmt->get_result(); // Get the result of the query

        $attendanceRows = '';
        while ($row = $result->fetch_assoc()) {
            $memberId = $row['member_id'];
            $memberName = $row['member_name'];
            $department = $row['department'];
            $presentChecked = $row['present'] ? 'checked' : '';
            $absentChecked = $row['absent'] ? 'checked' : '';
            $substitutedChecked = $row['substituted'] ? 'checked' : '';
            $attendanceRows .= "<tr class='member-row'>
                <td class='px-2 py-1 text-light-emphasis fw-bold'>
                    <input type='hidden' name='member_id[]' value='{$memberId}'>
                    {$memberName}
                </td>
                <td class='text-center px-2 py-1 text-light-emphasis'>{$department}</td>
                <td class='text-center px-2 py-1 text-light-emphasis'>
                    <div class='form-check d-flex w-100 justify-content-center'>
                        <input class='form-check-input' type='checkbox' name='status[{$memberId}]' value='1' id='status_p_{$memberId}' data-status='1' {$presentChecked}>
                    </div>
                </td>
                <td class='text-center px-2 py-1 text-light-emphasis'>
                    <div class='form-check d-flex w-100 justify-content-center'>
                        <input class='form-check-input' type='checkbox' name='status[{$memberId}]' value='2' id='status_a_{$memberId}' data-status='1' {$absentChecked}>
                    </div>
                </td>
                <td class='text-center px-2 py-1 text-light-emphasis'>
                    <div class='form-check d-flex w-100 justify-content-center'>
                        <input class='form-check-input' type='checkbox' name='status[{$memberId}]' value='3' id='status_s_{$memberId}' data-status='1' {$substitutedChecked}>
                    </div>
                </td>
            </tr>";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Fetch guest list data with filtering by module_team_name
    $sql = "SELECT 
                g.*, 
                ga.guest_name, 
                ga.department AS guest_department 
            FROM 
                domm_module_team_guest_attendance g
            JOIN 
                domm_guests ga ON g.guest_id = ga.guest_id
            WHERE 
                g.agenda_id = ? AND
                ga.module_team_name = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("is", $agendaId, $selected_team);
        $stmt->execute();
        $result = $stmt->get_result();
        $guestRows = '';
        while ($row = $result->fetch_assoc()) {
            $guestId = $row['guest_id'];
            $guestName = $row['guest_name'];
            $presentChecked = $row['present'] ? 'checked' : '';
            $guestRows .= "<tr>
                <td class='px-2 py-1 text-light-emphasis fw-bold'>
                    <input type='hidden' name='guest_id' value='{$guestId}'>
                    {$guestName}
                </td>
                <td class='text-center px-2 py-1 text-light-emphasis'>{$row['guest_department']}</td>
                <td class='substitute editable' class='px-2 py-1 text-light'>{$row['substitute']}</td>
                <td class='text-center px-2 py-1 text-light-emphasis'>
                    <input class='form-check-input' type='checkbox' name='present[{$guestId}]' id='present_{$guestId}' data-status='1' {$presentChecked}>
                </td>
                <td class='text-center px-2 py-1'>
                    <div class='btn-group' role='group' aria-label='Basic example'>
                        <button class='btn btn-sm btn-outline-danger rounded-0' type='button' id='deleteBtnGuest' title='Delete'><i class='fas fa-trash'></i></button>
                    </div>
                </td>
            </tr>";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    echo json_encode([
        'attendance' => $attendanceRows,
        'guest_list' => $guestRows
    ]);
} else {
    echo json_encode(['error' => 'No agenda ID provided.']);
}
?>
