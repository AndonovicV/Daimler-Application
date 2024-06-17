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
        $update_stmt = $conn->prepare("UPDATE tasks SET sent = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $old_task_id);
        $update_stmt->execute();
        $update_stmt->close();
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
        $deadline = $row['deadline'];
        $name = $row['name'];
        $responsible = $row['responsible'];
        $gft = $row['gft'];
        $cr = $row['cr'];
        $details = $row['details'];
        
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
