<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['topic_id'], $_POST['start'])) {
    $topic_id = $_POST['topic_id'];
    $start = $_POST['start'];

    $sql = "UPDATE topics SET start = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $start, $topic_id);
        if ($stmt->execute()) {
            echo "Start time updated successfully.";
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
    $conn->close();
}
?>
