<?php
include 'conn.php';

$agendaId = $_POST['agenda_id'];
$gftId = $_POST['gft'];
$cr = $_POST['cr'];

// Insert a new break with default values into the `domm_breaks` table
$sql = "INSERT INTO domm_breaks (agenda_id, gft, cr, duration) VALUES (?, ?, ?, '00:00:00')";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iss', $agendaId, $gftId, $cr);

if ($stmt->execute()) {
    // Retrieve the ID of the newly inserted break
    $newBreakId = $stmt->insert_id;
    echo $newBreakId;
} else {
    echo 'error';
}

$stmt->close();
$conn->close();
?>
