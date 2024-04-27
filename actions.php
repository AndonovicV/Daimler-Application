<?php 
include 'conn.php'; 
echo "hello";
// mt_agenda new row
    if (isset($_POST['counter'])) {
        $counter = $_POST['counter'];
       
       $sql = "INSERT INTO mt_agenda (GFT, Topic, Status, Change_Request, Task, Comment, Milestone, Responsible, Start, New_Row, Delete_Row)
        VALUES ('$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', 'Yes', 'No')";
       
       if ($conn->query($sql) === TRUE) {
            echo "New record inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Counter value is not set";
    }


// mt_agenda delete row
    if (isset($_POST['rowId'])) {
        $rowId = $_POST['rowId'];

        try {
            $stmt = $conn->prepare("DELETE FROM mt_agenda WHERE id = ?");
            $stmt->bind_param('i', $rowId);
            $stmt->execute();
            
            echo "Row deleted successfully.";
        } catch(Exception $e) {
            echo "Error deleting row: " . $e->getMessage();
        }
    } else {
        echo "RowId not provided.";
    }
?>  