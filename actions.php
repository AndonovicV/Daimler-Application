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
// fetching agenda data based on the selected agenda ID
if (isset($_POST['agenda_id']) && $_POST['agenda_id'] != 'new') {
    $agenda_id = $_POST['agenda_id'];
    $sql = "SELECT * FROM mt_agenda WHERE agenda_id = $agenda_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Echo each row as HTML
            echo "<tr id='" . $row["item_id"] . "'>"; 
            echo "<td>" . $row["item_id"] . "</td>";
            echo "<td>" . $row["GFT"] . "</td>";
            echo "<td>" . $row["Topic"] . "</td>";
            echo "<td>" . $row["Status"] . "</td>";
            echo "<td>" . $row["Change_Request"] . "</td>";
            echo "<td>" . $row["Task"] . "</td>";
            echo "<td>" . $row["Comment"] . "</td>";
            echo "<td>" . $row["Milestone"] . "</td>";
            echo "<td>" . $row["Responsible"] . "</td>";
            echo "<td>" . $row["Start"] . "</td>";
            echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
            echo "<td><button class='btn btn-danger deleteRow'>Delete Row</button></td>";
            echo "</tr>";
        }
    } else {
        // Handle case when no data is found
        echo "<tr><td colspan='12'>No data found.</td></tr>";
    }
}

if (isset($_POST['agenda_name']) && !empty($_POST['agenda_name'])) {
    $agendaName = $_POST['agenda_name'];
    
    // Insert new row into mt_agenda_list table
    $insertSql = "INSERT INTO mt_agenda_list (agenda_name) VALUES ('$agendaName')";
    if ($conn->query($insertSql) === TRUE) {
        // Retrieve the auto-generated agenda_id
        $agendaId = $conn->insert_id;
        
        // Insert an empty row into mt_agenda table
        $emptyRowSql = "INSERT INTO mt_agenda (GFT, Topic, Status, Change_Request, Task, Comment, Milestone, Responsible, Start, New_Row, Delete_Row, agenda_id) 
                        VALUES ('', '', '', '', '', '', '', '', '', 'Yes', 'No', '$agendaId')";
        if ($conn->query($emptyRowSql) === TRUE) {
            echo $agendaId; // Return the newly generated agenda_id
        } else {
            echo "Error creating new agenda: " . $conn->error;
        }
    } else {
        echo "Error creating new agenda: " . $conn->error;
    }
} else {
    echo "Agenda name not provided.";
}

?>  