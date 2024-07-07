<?php
include 'conn.php';

$agenda_id = $_POST['agenda_id'];
$gft_id = $_POST['gft_id'];
$order_value = $_POST['order_value'];

if (!empty($agenda_id) && !empty($gft_id) && isset($order_value)) {
    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check if the order already exists for this agenda and GFT
        $stmt = $conn->prepare("SELECT * FROM domm_gft_order WHERE agenda_id = ? AND gft_id = ?");
        $stmt->bind_param('ii', $agenda_id, $gft_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing order value
            $stmt = $conn->prepare("UPDATE domm_gft_order SET order_value = ? WHERE agenda_id = ? AND gft_id = ?");
            $stmt->bind_param('iii', $order_value, $agenda_id, $gft_id);
        } else {
            // Insert new order value
            $stmt = $conn->prepare("INSERT INTO domm_gft_order (agenda_id, gft_id, order_value) VALUES (?, ?, ?)");
            $stmt->bind_param('iii', $agenda_id, $gft_id, $order_value);
        }

        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}

$conn->close();
?>
