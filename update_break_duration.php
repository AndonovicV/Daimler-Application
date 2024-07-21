<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'insert') {
        // Insert a new break
        $agenda_id = $_POST['agenda_id'];
        $gft = $_POST['gft'];
        $cr = $_POST['cr'];

        $sql = "INSERT INTO domm_breaks (agenda_id, gft, cr, duration) VALUES (?, ?, ?, '00:00')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('iss', $agenda_id, $gft, $cr);
            if ($stmt->execute()) {
                $last_id = $stmt->insert_id;
                echo $last_id; // Return the ID of the inserted break
            } else {
                echo "Error inserting record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } elseif (isset($_POST['break_id']) && isset($_POST['duration'])) {
        // Update the duration of an existing break
        $break_id = $_POST['break_id'];
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

        $sql = "UPDATE domm_breaks SET duration = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('si', $formatted_duration, $break_id);
            if ($stmt->execute()) {
                echo "Duration updated successfully to $formatted_duration.";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
    $conn->close();
}
?>
