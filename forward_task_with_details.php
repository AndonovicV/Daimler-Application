<?php
//when forwarding the task form protokol to protokol, this makes 2 tasks. 1 is seen, 1 is not (sent = 1/0). Doesnt have a big impact so i left it.
include 'conn.php'; // Ensure you have a connection to the database

$taskId = $_POST['task_id'];
$agendaId = $_POST['agenda_id'];

// Start transaction
$conn->begin_transaction();

try {
    // Copy the main task
    $sql = "INSERT INTO tasks (agenda_id, name, responsible, gft, cr, deleted, asap, deadline) 
            SELECT ?, name, responsible, gft, cr, deleted, asap, deadline FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $agendaId, $taskId);
    $stmt->execute();
    $newTaskId = $conn->insert_id; // Get the ID of the newly inserted task

    // Types of IAD rows to handle
    $types = ['information', 'assignment', 'decision'];
    foreach ($types as $type) {
        $sql = "INSERT INTO $type (agenda_id, gft, cr, task_id, content, responsible) 
                SELECT ?, gft, cr, ?, content, responsible FROM $type WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $agendaId, $newTaskId, $taskId);
        $stmt->execute();
    }

    // Commit the transaction
    $conn->commit();
    echo 'Task and related details forwarded successfully';
} catch (Exception $e) {
    $conn->rollback(); // Something went wrong, rollback
    echo 'Error: ' . $e->getMessage();
}

$conn->close();
?>
