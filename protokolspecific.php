<?php
// Include the database connection
include 'conn.php';

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the raw POST data
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Check if JSON decoding was successful
    if ($data === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }

    // Fetch data from the JSON request
    $taskId = $data['task_id'] ?? null;
    $rowType = $data['row_type'] ?? null;
    $content = $data['content'] ?? null;

    // Validate incoming data
    if ($taskId === null || $rowType === null || $content === null) {
        echo json_encode(['status' => 'error', 'message' => 'Incomplete data']);
        exit;
    }

    // Determine which table to insert into based on rowType
    switch ($rowType) {
        case 'I':
            $tableName = 'domm_information';
            break;
        case 'A':
            $tableName = 'domm_assignment';
            break;
        case 'D':
            $tableName = 'domm_decision';
            break;
        default:
            // Handle invalid rowType
            echo json_encode(['status' => 'error', 'message' => 'Invalid row type']);
            exit;
    }

    // Insert new content into the respective table using prepared statements
    $sql = "INSERT INTO $tableName (task_id, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    // Bind parameters and execute statement
    $stmt->bind_param("is", $taskId, $content);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save content']);
        exit;
    }

    // Get the last inserted ID
    $newId = $stmt->insert_id;

    // Close the statement
    $stmt->close();

    // Return the response
    echo json_encode(['status' => 'success', 'message' => 'Content saved successfully', 'id' => $newId]);

    // Close the database connection
    $conn->close();
} else {
    // Handle invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>