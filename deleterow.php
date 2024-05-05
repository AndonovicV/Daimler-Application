<?php
// Connect to the database
include 'conn.php';

// Check if the "id" parameter is provided
if(isset($_POST['rowId'])) {
    $rowId = $_POST['rowId'];

    // Construct the deletion query
    $delete_sql = "DELETE FROM mt_agenda WHERE id = '$rowId'";
    
    // Execute the deletion query
    if ($conn->query($delete_sql) === TRUE) {
        echo "Row deleted successfully.";
    } else {
        echo "Error deleting row: " . $conn->error;
    }
} else {
    $rowId = $_POST['rowId'];
    echo $rowId;
    echo "ID parameter not provided.";
}
?>