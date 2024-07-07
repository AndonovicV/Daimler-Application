<?php 
include 'conn.php';  

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'topic') {
    $sql = "SELECT id FROM topics ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['last_id' => $row['id']]);
    } else {
        echo json_encode(['error' => 'No records found']);
    }
} elseif ($type === 'task') {
    // Fetch the last ID from the tasks table
    $sql = "SELECT id FROM tasks ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $taskRow = $result->fetch_assoc();
        $lastTaskId = $taskRow['id'];

        // Fetch the last ID from the domm_information table
        $infoSql = "SELECT id FROM domm_information ORDER BY id DESC LIMIT 1";
        $infoResult = $conn->query($infoSql);
        $lastInfoId = ($infoResult->num_rows > 0) ? $infoResult->fetch_assoc()['id'] : null;

        // Fetch the last ID from the domm_assignment table
        $assignmentSql = "SELECT id FROM domm_assignment ORDER BY id DESC LIMIT 1";
        $assignmentResult = $conn->query($assignmentSql);
        $lastAssignmentId = ($assignmentResult->num_rows > 0) ? $assignmentResult->fetch_assoc()['id'] : null;

        // Fetch the last ID from the domm_decision table
        $decisionSql = "SELECT id FROM domm_decision ORDER BY id DESC LIMIT 1";
        $decisionResult = $conn->query($decisionSql);
        $lastDecisionId = ($decisionResult->num_rows > 0) ? $decisionResult->fetch_assoc()['id'] : null;

        echo json_encode([
            'last_task_id' => $lastTaskId,
            'last_information_id' => $lastInfoId,
            'last_assignment_id' => $lastAssignmentId,
            'last_decision_id' => $lastDecisionId
        ]);
    } else {
        echo json_encode(['error' => 'No task records found']);
    }
} else {
    echo json_encode(['error' => 'Invalid type']);
}

$conn->close();
?>
