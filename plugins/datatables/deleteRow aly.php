<?php
include 'C:\xampp\htdocs\Daimler\conn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['rowId'])) {
        $rowId = $_POST['rowId'];

        try {
            $stmt = $conn->prepare("DELETE FROM mt_agenda_aly WHERE id = ?");
            $stmt->bind_param('i', $rowId);
            $stmt->execute();
            
            echo "Row deleted successfully.";
        } catch(Exception $e) {
            echo "Error deleting row: " . $e->getMessage();
        }
    } else {
        echo "RowId not provided.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
