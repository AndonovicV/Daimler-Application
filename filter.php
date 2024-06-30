<?php
session_start();
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_titles'], $_POST['selectedAgendaId'])) {
        $selected_titles = $_POST['selected_titles'];
        $selectedAgendaId = $_POST['selectedAgendaId'];
        
        error_log("Received selected titles: " . print_r($selected_titles, true));
        error_log("Received selected agenda ID: " . $selectedAgendaId);

        if ($selectedAgendaId !== null) {
            // Clear existing filter selections for the current agenda
            $clear_sql = "DELETE FROM agenda_change_request_filters WHERE agenda_id = ?";
            $clear_stmt = $conn->prepare($clear_sql);
            if ($clear_stmt) {
                $clear_stmt->bind_param('i', $selectedAgendaId);
                if (!$clear_stmt->execute()) {
                    error_log('Error executing clear statement: ' . $clear_stmt->error);
                    echo "Error clearing existing filters.";
                    exit;
                }
                $clear_stmt->close();
                error_log("Cleared existing filters for agenda ID: " . $selectedAgendaId);
            } else {
                error_log('Error preparing clear statement: ' . $conn->error);
                echo "Error preparing statement.";
                exit;
            }

            foreach ($selected_titles as $title) {
                // Insert new filter selections
                $insert_sql = "INSERT INTO agenda_change_request_filters (agenda_id, change_request_id, filter_active) VALUES (?, (SELECT ID FROM change_requests WHERE title = ?), 1)";
                $insert_stmt = $conn->prepare($insert_sql);
                if ($insert_stmt) {
                    $insert_stmt->bind_param('is', $selectedAgendaId, $title);
                    if (!$insert_stmt->execute()) {
                        error_log('Error executing insert statement: ' . $insert_stmt->error);
                        echo "Error inserting new filter.";
                        exit;
                    }
                    $insert_stmt->close();
                    error_log("Inserted new filter for title: " . $title);
                } else {
                    error_log('Error preparing insert statement: ' . $conn->error);
                    echo "Error preparing statement.";
                    exit;
                }
            }
            echo "Success";
        } else {
            echo "No agenda selected. Session value: " . print_r($_SESSION, true);
            error_log("No agenda selected. Session value: " . print_r($_SESSION, true));
        }
    } elseif (isset($_POST['title'], $_POST['agenda_id'], $_POST['action']) && $_POST['action'] === 'unselect') {
        $title = $_POST['title'];
        $agendaId = $_POST['agenda_id'];
        
        error_log("Received title to unselect: " . $title);
        error_log("Received agenda ID for unselection: " . $agendaId);

        $delete_sql = "DELETE FROM agenda_change_request_filters WHERE agenda_id = ? AND change_request_id = (SELECT ID FROM change_requests WHERE title = ? LIMIT 1)";
        $delete_stmt = $conn->prepare($delete_sql);
        if ($delete_stmt) {
            $delete_stmt->bind_param('is', $agendaId, $title);
            if ($delete_stmt->execute()) {
                if ($delete_stmt->affected_rows > 0) {
                    echo "Success";
                    error_log("Successfully unselected filter for title: " . $title);
                } else {
                    echo "Failed to unselect filter: No such filter found or other error.";
                    error_log("Failed to unselect filter: No such filter found or other error for title: " . $title);
                }
            } else {
                error_log('Error executing delete statement: ' . $delete_stmt->error);
                echo "Error executing statement.";
            }
            $delete_stmt->close();
        } else {
            error_log('Error preparing delete statement: ' . $conn->error);
            echo "Error preparing statement.";
        }
    } else {
        echo "No data received";
        error_log("No data received in POST request.");
    }
}
?>
