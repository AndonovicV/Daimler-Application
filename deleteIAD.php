<?php
// Assuming you've established a database connection
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if rowId and rowType are set in the POST data
    if(isset($_POST['rowId']) && isset($_POST['rowType'])) {
        // Fetch data from the POST request
        $Id = $_POST['rowId'];
        $rowType = $_POST['rowType'];

        // Determine which table to update based on rowType
        switch ($rowType) {
            case 'I':
                $columnName = 'domm_information';
                break;
            case 'A':
                $columnName = 'domm_assignment';
                break;
            case 'D':
                $columnName = 'domm_decision';
                break;
            default:
                // Handle invalid rowType
                echo 'Invalid rowType';
                exit; // or handle the error in a different way
        }

        // Update content in the respective table
        $sql = "DELETE FROM $columnName WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $Id);

        if ($stmt->execute()) {
            // Content updated successfully
            echo 'Content deleted successfully';
        } else {
            // Error in updating
            echo 'Failed to delete content';
            // Add error handling to see what went wrong, e.g., echo $conn->error;
        }

        $stmt->close();
    } else {
        echo 'rowId and rowType not provided';
    }
} else {
    echo 'Invalid request method';
}
?>
