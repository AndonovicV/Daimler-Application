<?php
include 'conn.php';

if (isset($_GET['agenda_id'])) {
    $agendaId = intval($_GET['agenda_id']);

    // Fetch attendance data
    $attendanceResult = $conn->query("SELECT * FROM module_team_member_attendance WHERE agenda_id = $agendaId");
    $attendanceRows = '';
    while ($row = $attendanceResult->fetch_assoc()) {
        $memberId = $row['id'];
        $presentChecked = $row['present'] ? 'checked' : '';
        $absentChecked = $row['absent'] ? 'checked' : '';
        $substitutedChecked = $row['substituted'] ? 'checked' : '';
        $attendanceRows .= "<tr class='member-row'>
            <td class='px-2 py-1 text-light-emphasis fw-bold'>
                <input type='hidden' name='member_id[]' value='{$memberId}'>
                {$row['members']}
            </td>
            <td class='text-center px-2 py-1 text-light-emphasis'>{$row['department']}</td>
            <td class='text-center px-2 py-1 text-light-emphasis'>
                <div class='form-check d-flex w-100 justify-content-center'>
                    <input class='form-check-input' type='checkbox' name='status[{$memberId}]' value='1' id='status_p_{$memberId}' {$presentChecked}>
                </div>
            </td>
            <td class='text-center px-2 py-1 text-light-emphasis'>
                <div class='form-check d-flex w-100 justify-content-center'>
                    <input class='form-check-input' type='checkbox' name='status[{$memberId}]' value='2' id='status_a_{$memberId}' {$absentChecked}>
                </div>
            </td>
            <td class='text-center px-2 py-1 text-light-emphasis'>
                <div class='form-check d-flex w-100 justify-content-center'>
                    <input class='form-check-input' type='checkbox' name='status[{$memberId}]' value='3' id='status_s_{$memberId}' {$substitutedChecked}>
                </div>
            </td>
        </tr>";
    }

    // Fetch guest list data
    $guestResult = $conn->query("SELECT * FROM module_team_guest_guest_attendance WHERE agenda_id = $agendaId");
    $guestRows = '';
    while ($row = $guestResult->fetch_assoc()) {
        $guestId = $row['id'];
        $presentChecked = $row['present'] ? 'checked' : '';
        $guestRows .= "<tr>
            <td class='px-2 py-1 text-light-emphasis fw-bold'>{$row['guest_name']}</td>
            <td class='text-center px-2 py-1 text-light-emphasis'>{$row['department']}</td>
            <td class='px-2 py-1 text-light'>{$row['substitute']}</td>
            <td class='text-center px-2 py-1'>
                <input class='form-check-input' type='checkbox' name='present[{$guestId}]' id='present_{$guestId}' {$presentChecked}>
            </td>
            <td class='text-center px-2 py-1'>
                <div class='btn-group' role='group' aria-label='Basic example'>
                    <button class='btn btn-sm btn-outline-primary rounded-0' type='button' title='Edit'><i class='fas fa-edit'></i></button>
                    <button class='btn btn-sm btn-outline-danger rounded-0' type='button' title='Delete'><i class='fas fa-trash'></i></button>
                </div>
            </td>
        </tr>";
    }

    echo json_encode([
        'attendance' => $attendanceRows,
        'guest_list' => $guestRows
    ]);
} else {
    echo json_encode(['error' => 'No agenda ID provided.']);
}
?>
