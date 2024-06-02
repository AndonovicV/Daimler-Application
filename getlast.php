<?php 
include 'conn.php';  

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'topic') {
    $sql = "SELECT id FROM topics ORDER BY id DESC LIMIT 1";
} elseif ($type === 'task') {
    $sql = "SELECT id FROM tasks ORDER BY id DESC LIMIT 1";
} else {
    echo json_encode(['error' => 'Invalid type']);
    exit;
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['last_id' => $row['id']]);
} else {
    echo json_encode(['error' => 'No records found']);
}

$conn->close();
?>