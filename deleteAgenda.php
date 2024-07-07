<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agendaIds = isset($_POST['agenda_ids']) ? $_POST['agenda_ids'] : [];

    if (!empty($agendaIds)) {
        $placeholders = implode(',', array_fill(0, count($agendaIds), '?'));
        
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Delete from related tables
            $relatedTables = [
                'domm_agenda_change_request_filters',
                'domm_assignment',
                'domm_decision',
                'domm_information',
                'module_team_guest_attendance',
                'module_team_member_attendance',
                'tasks',
                'topics'
            ];

            foreach ($relatedTables as $table) {
                $deleteSql = "DELETE FROM $table WHERE agenda_id IN ($placeholders)";
                $stmt = $conn->prepare($deleteSql);
                if ($stmt) {
                    $types = str_repeat('i', count($agendaIds));
                    $stmt->bind_param($types, ...array_map('intval', $agendaIds));
                    $stmt->execute();
                    $stmt->close();
                } else {
                    throw new Exception("Failed to prepare statement for table $table: " . $conn->error);
                }
            }

            // Delete from mt_agenda_list
            $deleteSql = "DELETE FROM mt_agenda_list WHERE agenda_id IN ($placeholders)";
            $stmt = $conn->prepare($deleteSql);
            if ($stmt) {
                $types = str_repeat('i', count($agendaIds));
                $stmt->bind_param($types, ...array_map('intval', $agendaIds));
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Failed to prepare statement for mt_agenda_list: " . $conn->error);
            }

            // Commit transaction
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid agenda IDs.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
$conn->close();
?>
