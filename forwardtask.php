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
        $stmt = $conn->prepare("SELECT id, agenda_id, name, responsible, gft, cr, details, deadline FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $old_task_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } elseif ($old_topic_id !== null) {
        // Prepare and execute the query to fetch the old topic data
        $stmt = $conn->prepare("SELECT id, agenda_id, name, responsible, gft, cr, details FROM topics WHERE id = ?");
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
        $deadline = isset($row['deadline']) ? $row['deadline'] : null;
        $name = $row['name'];
        $responsible = $row['responsible'];
        $gft = $row['gft'];
        $cr = $row['cr'];
        $details = $row['details'];
        
        // Check if the filter for the Change Request is already active
        $check_stmt = $conn->prepare("SELECT * FROM agenda_change_request_filters WHERE agenda_id = ? AND change_request_id = ? AND filter_active = 1");
        $check_stmt->bind_param("is", $new_agenda_id, $cr);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Activate the filter for the Change Request connected to the task
            $filter_stmt = $conn->prepare("INSERT INTO agenda_change_request_filters (agenda_id, change_request_id, filter_active) VALUES (?,  ?, 1)");
            $filter_stmt->bind_param("is", $new_agenda_id, $cr);
            $filter_stmt->execute();
            $filter_stmt->close();
        }
        $check_stmt->close();

        // Prepare and execute the query to insert the new task or topic with the new agenda_id
        if ($old_task_id !== null) {
            $insert_stmt = $conn->prepare("INSERT INTO tasks (name, responsible, deadline, gft, cr, details, agenda_id, deleted, sent) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)");
            $insert_stmt->bind_param("ssssssi", $name, $responsible, $deadline, $gft, $cr, $details, $new_agenda_id);
        } elseif ($old_topic_id !== null) {
            $insert_stmt = $conn->prepare("INSERT INTO topics (name, responsible, gft, cr, details, agenda_id) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssssi", $name, $responsible, $gft, $cr, $details, $new_agenda_id);
        }

        if ($insert_stmt->execute()) {
            echo 'Task or Topic successfully copied to the new agenda';

            if ($old_topic_id !== null) {
                // Get the ID of the newly inserted topic
                $new_topic_id = $conn->insert_id;

                // Fetch and copy all tasks associated with the old topic_id
                $task_stmt = $conn->prepare("SELECT name, responsible, deadline, gft, cr, details FROM tasks WHERE topic_id = ?");
                $task_stmt->bind_param("i", $old_topic_id);
                $task_stmt->execute();
                $task_result = $task_stmt->get_result();

                while ($task_row = $task_result->fetch_assoc()) {
                    $task_name = $task_row['name'];
                    $task_responsible = $task_row['responsible'];
                    $task_deadline = $task_row['deadline'];
                    $task_gft = $task_row['gft'];
                    $task_cr = $task_row['cr'];
                    $task_details = $task_row['details'];

                    $task_insert_stmt = $conn->prepare("INSERT INTO tasks (name, responsible, deadline, gft, cr, details, agenda_id, topic_id, deleted, sent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)");
                    $task_insert_stmt->bind_param("ssssssii", $task_name, $task_responsible, $task_deadline, $task_gft, $task_cr, $task_details, $new_agenda_id, $new_topic_id);
                    $task_insert_stmt->execute();
                    $task_insert_stmt->close();
                }
                $task_stmt->close();
            }

            // Set the old task/topic as deleted
            if ($old_task_id !== null) {
                $update_stmt = $conn->prepare("UPDATE tasks SET sent = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $old_task_id);
            } elseif ($old_topic_id !== null) {
                $update_stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
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
