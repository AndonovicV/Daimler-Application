<?php
// Assuming you've established a database connection
include 'conn.php';

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Fetch data from the JSON request
    $taskId = $data['task_id'];
    $rowType = $data['row_type'];
    $content = $data['content'];

    // Determine which table to insert into based on rowType
    switch ($rowType) {
        case 'I':
            $tableName = 'information';
            break;
        case 'A':
            $tableName = 'assignment';
            break;
        case 'D':
            $tableName = 'decision';
            break;
        default:
            // Handle invalid rowType
            break;
    }

    // Insert new content into the respective table
    $sql = "INSERT INTO $tableName (task_id, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $taskId, $content);

    if ($stmt->execute()) {
        // Content inserted successfully
        echo 'Content saved successfully';
    } else {
        // Error in inserting
        echo 'Failed to save content';
    }

    $stmt->close();
} else {
    // Handle invalid request method
    echo 'Invalid request method';
}
?>