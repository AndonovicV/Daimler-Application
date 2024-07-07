<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['topic_id'], $_POST['duration'])) {
    $topic_id = $_POST['topic_id'];
    $duration = $_POST['duration']; // This is the duration in minutes as input by the user

    // Check if duration is numeric and convert to minutes in the HH:mm format
    if (is_numeric($duration)) {
        // Convert minutes to hours and minutes
        $hours = intdiv($duration, 60);
        $minutes = $duration % 60;
        $formatted_duration = sprintf("%02d:%02d", $hours, $minutes);
    } else {
        // Default to 00:00 if not numeric
        $formatted_duration = "00:00";
    }

    $sql = "UPDATE domm_topics SET duration = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $formatted_duration, $topic_id);
        if ($stmt->execute()) {
            echo "Duration updated successfully to $formatted_duration.";
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
