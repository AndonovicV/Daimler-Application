<?php 
include 'conn.php';  

// mt_agenda new row
if (isset($_POST['meanId']) && isset($_POST['counter'])) {
    $meanId = $_POST['meanId'];
    $counter = $_POST['counter'];

    $sql = "INSERT INTO mt_agenda (item_id, GFT, Topic, Status, Change_Request, Task, Comment, Milestone, Responsible, Start, New_Row, Delete_Row)
            VALUES ('$meanId', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', 'Yes', 'No')";
    echo "SQL query: " . $sql; // Echo the SQL query
    if ($conn->query($sql) === TRUE) {
        echo "New record inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    //echo "Mean ID or Counter value is not set";
}

// mt_agenda delete row
if (isset($_POST['rowId'])) {
    $rowId = $_POST['rowId'];
    //echo $rowId; // Corrected variable name
    $sql = "DELETE FROM mt_agenda WHERE CONCAT(`mt_agenda`.`item_id`) = '$rowId'";
    echo "SQL query: " . $sql; // Echo the SQL query
    if ($conn->query($sql) === TRUE) {
        //echo "Row deleted successfully.";
    } else {
        //echo "Error deleting row: " . $conn->error;
    }
} else {
    //echo "RowId not provided.";
}
?>  