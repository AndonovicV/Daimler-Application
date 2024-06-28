<?php
// Assuming you've established a database connection
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Fetch data from the JSON request
    $Id = $data['id'];
    $rowType = $data['row_type'];
    $content = $data['content'];
    $fieldType = $data['field_type'];

    // Determine which table to update based on rowType
    switch ($rowType) {
        case 'I':
            $columnName = 'information';
            break;
        case 'A':
            $columnName = 'assignment';
            break;
        case 'D':
            $columnName = 'decision';
            break;
        default:
            // Handle invalid rowType
            break;
    }

    // Update content in the respective table
        // Determine which table to update based on rowType
        switch ($fieldType) {
            case 'content':
                $sql = "UPDATE $columnName SET content = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $content, $Id);
            
                if ($stmt->execute()) {
                    // Content updated successfully
                    echo 'Content saved successfully';
                } else {
                    // Error in updating
                    echo 'Failed to save content';
                }                
                break;
            case 'responsible':
                $sql = "UPDATE $columnName SET responsible = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $content, $Id);
            
                if ($stmt->execute()) {
                    // Content updated successfully
                    echo 'Content saved successfully';
                } else {
                    // Error in updating
                    echo 'Failed to save content';
                }                       
                break;

            default:
                // Handle invalid rowType
                break;
        }
    


    $stmt->close();
}
?>
