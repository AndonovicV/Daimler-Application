<?php 
include 'conn.php';

function log_message($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, __DIR__ . '/debug.log');
}

log_message('Script started.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    log_message('POST data received: ' . print_r($data, true));

    // Fetch data from the JSON request
    $old_task_id = isset($data['task_id']) ? $data['task_id'] : null;
    $old_topic_id = isset($data['topic_id']) ? $data['topic_id'] : null;
    $new_agenda_id = $data['agenda_id'];

    if ($old_task_id !== null) {
        log_message("Fetching old task data for task_id: $old_task_id");
        // Prepare and execute the query to fetch the old task data
        $stmt = $conn->prepare("SELECT id, agenda_id, name, responsible, gft, cr, details, deadline FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $old_task_id);
    } elseif ($old_topic_id !== null) {
        log_message("Fetching old topic data for topic_id: $old_topic_id");
        // Prepare and execute the query to fetch the old topic data, including the new columns
        $stmt = $conn->prepare("SELECT id, agenda_id, name, responsible, gft, cr, details, start, duration FROM topics WHERE id = ?");
        $stmt->bind_param("i", $old_topic_id);
    } else {
        log_message('Task or Topic ID not provided');
        echo 'Task or Topic ID not provided';
        exit(); // Exit script if neither task_id nor topic_id is provided
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        log_message('Data fetched successfully from the database.');
        // Fetch the data and store in variables
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $deadline = isset($row['deadline']) ? $row['deadline'] : null;
        $name = $row['name'];
        $responsible = $row['responsible'];
        $gft = $row['gft'];
        $cr = $row['cr'];
        $details = $row['details'];
        $start = isset($row['start']) ? $row['start'] : null;
        $duration = isset($row['duration']) ? $row['duration'] : null;
    
        log_message("Fetched data: " . print_r($row, true));
    
        // Check if the filter for the Change Request is already active
        $check_stmt = $conn->prepare("SELECT * FROM agenda_change_request_filters WHERE agenda_id = ? AND change_request_id = ? AND filter_active = 1");
        $check_stmt->bind_param("is", $new_agenda_id, $cr);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
    
        if ($check_result->num_rows == 0) {
            log_message("Activating filter for Change Request: $cr");
            // Activate the filter for the Change Request connected to the task
            $filter_stmt = $conn->prepare("INSERT INTO agenda_change_request_filters (agenda_id, change_request_id, filter_active) VALUES (?, ?, 1)");
            $filter_stmt->bind_param("is", $new_agenda_id, $cr);
            $filter_stmt->execute();
            $filter_stmt->close();
        }
        $check_stmt->close();
    
        if ($old_task_id !== null) {
            $conn->begin_transaction(); // Start transaction
            log_message("Inserting new task with agenda_id: $new_agenda_id");
            $insert_stmt = $conn->prepare("INSERT INTO tasks (name, responsible, deadline, gft, cr, details, agenda_id, deleted, sent, topic_id) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, '')");
            if ($insert_stmt === false) {
                $conn->rollback(); // Rollback transaction on error
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $insert_stmt->bind_param("ssssssi", $name, $responsible, $deadline, $gft, $cr, $details, $new_agenda_id);
            $insert_stmt->execute();
            $newTaskId = $conn->insert_id; // Get the ID of the newly inserted task
        
            // Types of IAD rows to handle
            $types = ['information', 'assignment', 'decision'];
            foreach ($types as $type) {
                $sql = "INSERT INTO $type (agenda_id, gft, cr, task_id, content, responsible) 
                        SELECT ?, gft, cr, ?, content, responsible FROM $type WHERE task_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $conn->rollback(); // Rollback transaction on error
                    throw new Exception('Prepare failed: ' . $conn->error);
                }
                $stmt->bind_param("iii", $new_agenda_id, $newTaskId, $old_task_id);
                $stmt->execute();
            }
            
            $conn->commit(); // Commit the transaction
            echo 'Task and related details forwarded successfully';
        }  elseif ($old_topic_id !== null) {
            log_message("Inserting new topic with agenda_id: $new_agenda_id");
            $insert_stmt = $conn->prepare("INSERT INTO topics (name, responsible, gft, cr, details, agenda_id, start, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssssis", $name, $responsible, $gft, $cr, $details, $new_agenda_id, $start, $duration);
        }
    if ($old_topic_id !== null) {

        // Execute the insert statement
        if ($insert_stmt->execute()) {
            // Check if the insert was successful
            if ($insert_stmt->affected_rows > 0) {
                // Get the ID of the newly inserted topic or task
                $new_topic_id = $conn->insert_id;
                // Log the new topic or task ID
                log_message("New topic/task inserted with ID: $new_topic_id");
            } else {
                // Log an error message if the insert failed
                log_message("Failed to insert new topic/task.");
            }
    
            log_message('Task or Topic successfully copied to the new agenda');
            echo 'Task or Topic successfully copied to the new agenda';
    
            if ($old_topic_id !== null) {
                log_message("Copying tasks associated with the old topic_id: $old_topic_id");
                
                // Prepare all statements outside of the loops to improve efficiency
                $task_stmt = $conn->prepare("SELECT id, name, responsible, deadline, gft, cr, details FROM tasks WHERE topic_id = ?");
                $task_stmt->bind_param("i", $old_topic_id);
                $task_stmt->execute();
                $task_result = $task_stmt->get_result();
                
                while ($task_row = $task_result->fetch_assoc()) {
                    $old_task_id = $task_row['id'];  // Correctly fetch the task_id for current row
                    log_message("Found task with ID: $old_task_id associated with the old topic_id: $old_topic_id");
                    
                    // Variables extracted from fetched task
                    extract($task_row);
                    $task_insert_stmt = $conn->prepare("INSERT INTO tasks (name, responsible, deadline, gft, cr, details, agenda_id, topic_id, deleted, sent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)");
                    $task_insert_stmt->bind_param("ssssssii", $name, $responsible, $deadline, $gft, $cr, $details, $new_agenda_id, $new_topic_id);
                    if ($task_insert_stmt->execute()) {
                        $new_task_id = $conn->insert_id;  // Fetch the new task ID after insert
                        log_message("Inserted new task with ID: $new_task_id for new topic_id: $new_topic_id");
                    } else {
                        log_message("Error inserting new task for old task ID: $old_task_id. Error: " . $task_insert_stmt->error);
                    }
                    $task_insert_stmt->close();
                    
                    $related_tables = ['information', 'assignment', 'decision'];
                    foreach ($related_tables as $table) {
                        log_message("Processing related table: $table for old task ID: $old_task_id");
                        $fetch_related_stmt = $conn->prepare("SELECT gft, cr, content, responsible FROM $table WHERE task_id = ?");
                        $fetch_related_stmt->bind_param("i", $old_task_id);
                        $fetch_related_stmt->execute();
                        $related_result = $fetch_related_stmt->get_result();
                        
                        while ($related_row = $related_result->fetch_assoc()) {
                            log_message("Found related row in table $table for old task ID: $old_task_id with content: " . json_encode($related_row));
                            $insert_related_stmt = $conn->prepare("INSERT INTO $table (agenda_id, gft, cr, task_id, content, responsible) VALUES (?, ?, ?, ?, ?, ?)");
                            $insert_related_stmt->bind_param("ississ", $new_agenda_id, $related_row['gft'], $related_row['cr'], $new_task_id, $related_row['content'], $related_row['responsible']);
                            if ($insert_related_stmt->execute()) {
                                log_message("Inserted related row into table $table for new task ID: $new_task_id");
                            } else {
                                log_message("Error inserting related row into table $table for old task ID: $old_task_id. Error: " . $insert_related_stmt->error);
                            }
                            $insert_related_stmt->close();
                        }
                        $fetch_related_stmt->close();
                    }
                }
                
                $task_stmt->close();
            }
    


            // Set the old task/topic as deleted or sent
            if ($old_task_id !== null) {
                log_message("Marking old task as sent: $old_task_id");
                $update_stmt = $conn->prepare("UPDATE tasks SET sent = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $old_task_id);
            } elseif ($old_topic_id !== null) {
                log_message("Deleting old topic: $old_topic_id");
                $update_stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
                $update_stmt->bind_param("i", $old_topic_id);
            }
            
            if ($update_stmt->execute()) {
                log_message("Successfully updated/deleted old task/topic with ID: " . ($old_task_id !== null ? $old_task_id : $old_topic_id));
            } else {
                log_message("Failed to update/delete old task/topic with ID: " . ($old_task_id !== null ? $old_task_id : $old_topic_id) . ". Error: " . $update_stmt->error);
            }
            $update_stmt->close();
        } else {
            log_message('Failed to copy task or topic to the new agenda');
            echo 'Failed to copy task or topic to the new agenda';
        }
    }
        $insert_stmt->close();
    } else {
        log_message('Task or Topic not found');
        echo 'Task or Topic not found';
    }

    $stmt->close();
}

$conn->close();
log_message('Script ended.');
?>
