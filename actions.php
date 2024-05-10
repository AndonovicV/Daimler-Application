<?php 
include 'conn.php';  

// mt_agenda new row
if (isset($_POST['meanId']) && isset($_POST['counter']) && isset($_POST['agendaId'])) {
    $meanId = $_POST['meanId'];
    $counter = $_POST['counter'];
    $agendaId = $_POST['agendaId']; // Get the agenda_id

    $sql = "INSERT INTO mt_agenda (item_id, agenda_id, GFT, Topic, Status, Change_Request, Task, Comment, Milestone, Responsible, Start, New_Row, Delete_Row)
            VALUES ( '$meanId', '$agendaId', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', 'Yes', 'No')";
    echo "SQL query: " . $sql; // Echo the SQL query
    if ($conn->query($sql) === TRUE) {
        echo "New record inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    //echo "Mean ID, Counter value, or Agenda ID is not set";
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
            echo "<td><button class='btn btn-primary addRow'>New</button></td>";
            echo "<td><button class='btn btn-danger deleteRow'>Delete</button></td>";
            echo "</tr>";
        }
    } else {
        // Handle case when no data is found
        echo "<tr><td colspan='12'>No data found.</td></tr>";
    }
}

// Insert new row (Table) into mt_agenda_list table
if (isset($_POST['agenda_name']) && !empty($_POST['agenda_name'])) {
    $agendaName = $_POST['agenda_name'];

    // Fetch the maximum item_id from mt_agenda table
    $maxItemIdQuery = "SELECT MAX(item_id) AS max_item_id FROM mt_agenda";
    $maxItemIdResult = $conn->query($maxItemIdQuery);
    if ($maxItemIdResult->num_rows > 0) {
        $row = $maxItemIdResult->fetch_assoc();
        $nextItemId = $row['max_item_id'] + 1;
    } else {
        $nextItemId = 1; // If no rows found, start from 1
    }

    // Insert new row into mt_agenda_list table
    $insertSql = "INSERT INTO mt_agenda_list (agenda_name) VALUES ('$agendaName')";
    if ($conn->query($insertSql) === TRUE) {
        // Retrieve the auto-generated agenda_id
        $agendaId = $conn->insert_id;

        // Insert an empty row into mt_agenda table with the next item_id
        $emptyRowSql = "INSERT INTO mt_agenda (item_id, agenda_id, GFT, Topic, Status, Change_Request, Task, Comment, Milestone, Responsible, Start, New_Row, Delete_Row) 
                        VALUES ('$nextItemId', '$agendaId', '', '', '', '', '', '', '', '', '', 'Yes', 'No')";
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