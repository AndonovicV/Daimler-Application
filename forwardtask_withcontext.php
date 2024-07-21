<?php 
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Fetch data from the JSON request
    $old_task_id = isset($data['task_id']) ? $data['task_id'] : null;
    $old_topic_id = isset($data['topic_id']) ? $data['topic_id'] : null;
    $new_agenda_id = $data['agenda_id'];

    if ($old_task_id !== null) {
        // Prepare and execute the query to fetch the old task data
        $stmt = $conn->prepare("SELECT id, agenda_id, name, responsible, gft, cr, details, deadline, topic_id FROM domm_tasks WHERE id = ?");
        $stmt->bind_param("i", $old_task_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } elseif ($old_topic_id !== null) {
        // Prepare and execute the query to fetch the old topic data
        $stmt = $conn->prepare("SELECT id, agenda_id, name, responsible, gft, cr, details FROM domm_topics WHERE id = ?");
        $stmt->bind_param("i", $old_topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo 'Task or Topic ID not provided';
        exit(); // Exit script if neither task_id nor topic_id is provided
    }

    if ($result->num_rows > 0) {
        // Fetch the data and store in variables
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $agenda_id = $row['agenda_id'];
        $name = $row['name'];
        $deadline = isset($row['deadline']) ? $row['deadline'] : null;
        $responsible = $row['responsible'];
        $gft = $row['gft'];
        $cr = $row['cr'];
        $details = $row['details'];
        $topic_id = $row['topic_id'];

// Check if the filter for the Change Request is already active
$check_stmt = $conn->prepare("SELECT * FROM domm_agenda_change_request_filters WHERE agenda_id = ? AND change_request_id = ? AND filter_active = 1");
$check_stmt->bind_param("ii", $new_agenda_id, $cr);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    // Activate the filter for the Change Request connected to the task
    $filter_stmt = $conn->prepare("INSERT INTO domm_agenda_change_request_filters (agenda_id, change_request_id, filter_active) VALUES (?, ?, 1)");
    $filter_stmt->bind_param("ii", $new_agenda_id, $cr);
    $filter_stmt->execute();
    $filter_stmt->close();
}
$check_stmt->close();

// Prepare and execute the query to insert the new task or topic with the new agenda_id
if ($old_task_id !== null) {
    $insert_stmt = $conn->prepare("INSERT INTO domm_tasks (name, responsible, deadline, gft, cr, details, agenda_id, deleted, topic_id, sent) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?)");
    $insert_stmt->bind_param("sssssssis", $name, $responsible, $deadline, $gft, $cr, $details, $new_agenda_id, $topic_id, $sent);
    $insert_stmt->execute();
    $insert_stmt->close();
} elseif ($old_topic_id !== null) {
    $insert_stmt = $conn->prepare("INSERT INTO domm_topics (name, responsible, gft, cr, details, agenda_id) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("sssssi", $name, $responsible, $gft, $cr, $details, $new_agenda_id);
    $insert_stmt->execute();
    $insert_stmt->close();

    // Check if the filter for the Change Request is already active
    $taskfortopic_stmt = $conn->prepare("SELECT * FROM domm_tasks WHERE topic_id = ? AND deleted = 0");
    $taskfortopic_stmt->bind_param("i", $old_topic_id);
    $taskfortopic_stmt->execute();
    $tasks_result = $taskfortopic_stmt->get_result();

    if ($tasks_result->num_rows == 0) {
        $row = $tasks_result->fetch_assoc();
        $deadline = isset($row['deadline']) ? $row['deadline'] : null;
        $name = $row['name'];
        $responsible = $row['responsible'];
        $gft = $row['gft'];
        $cr = $row['cr'];
        $details = $row['details'];
        $topic_id = $row['topic_id'];

        // Activate the filter for the Change Request connected to the task
        $filter_stmt = $conn->prepare("INSERT INTO domm_agenda_change_request_filters (agenda_id, change_request_id, filter_active) VALUES (?, ?, 1)");
        $filter_stmt->bind_param("ii", $new_agenda_id, $cr);
        $filter_stmt->execute();
        $filter_stmt->close();
    }
    $taskfortopic_stmt->close();
}

        
        if ($insert_stmt->execute()) {
            $new_task_id = $conn->insert_id; // Get the ID of the newly inserted task/topic

            // Fetch related entries from domm_information, domm_assignment, and domm_decision tables
            $related_tables = ['domm_information', 'domm_assignment', 'domm_decision'];
            foreach ($related_tables as $table) {
                $fetch_related_stmt = $conn->prepare("SELECT * FROM $table WHERE task_id = ?");
                $fetch_related_stmt->bind_param("i", $old_task_id);
                $fetch_related_stmt->execute();
                $related_result = $fetch_related_stmt->get_result();
                
                while ($related_row = $related_result->fetch_assoc()) {
                    // Insert each related entry with the new task_id
                    $insert_related_stmt = $conn->prepare("INSERT INTO $table (agenda_id, gft, cr, task_id, content, responsible) VALUES (?, ?, ?, ?, ?, ?)");
                    $insert_related_stmt->bind_param("ississ", $new_agenda_id, $related_row['gft'], $related_row['cr'], $new_task_id, $related_row['content'], $related_row['responsible']);
                    $insert_related_stmt->execute();
                    $insert_related_stmt->close();
                }
                $fetch_related_stmt->close();
            }

            echo 'Task or Topic successfully copied to the new agenda and related entries duplicated';
            // Set the old task/topic as deleted
            if ($old_task_id !== null) {
                $update_stmt = $conn->prepare("UPDATE domm_tasks SET sent = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $old_task_id);
            } elseif ($old_topic_id !== null) {
                $update_stmt = $conn->prepare("DELETE FROM domm_topics WHERE id = ?");
                $update_stmt->bind_param("i", $old_topic_id);
            }
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            echo 'Failed to copy task or topic to the new agenda';
        }
        
        $insert_stmt->close();
    } else {
        echo 'Task or Topic not found';
    }

    $stmt->close();
}

$conn->close();
?>
