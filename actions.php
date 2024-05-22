<?php 
include 'conn.php';  
session_start(); // Start the session if not already started

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
    //echo($selected_team);
} else {
    $selected_team = ""; // Default value if not set
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the necessary data is set
    if (isset($_POST['agendaId'])) {
        // Assign POST data to variables, using null coalescing to handle missing keys
        $agendaId = $_POST['agendaId'];
        $content = $_POST['content'] ?? '';
        $responsible = $_POST['responsible'] ?? '';
        $gft = $_POST['gft'] ?? '';
        $cr = $_POST['cr'] ?? '';

        // Debugging: Echo the received data
        echo "Received Data:<br>";
        echo "Agenda ID: " . htmlspecialchars($agendaId) . "<br>";
        echo "Content: " . htmlspecialchars($content) . "<br>";
        echo "Responsible: " . htmlspecialchars($responsible) . "<br>";
        echo "GFT: " . htmlspecialchars($gft) . "<br>";
        echo "CR: " . htmlspecialchars($cr) . "<br>";

        // Check if it's a task or a topic
        if (isset($_POST['taskContent'])) {
            // It's a task
            $sql = "INSERT INTO tasks (agenda_id, name, responsible, gft, cr, details) 
                    VALUES (?, ?, ?, ?, ?, '')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $agendaId, $content, $responsible, $gft, $cr);
            echo "Inserting as Task<br>";
        } elseif (isset($_POST['topicContent'])) {
            // It's a topic
            $sql = "INSERT INTO topics (agenda_id, name, responsible, gft, cr, details) 
                    VALUES (?, ?, ?, ?, ?, '')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $agendaId, $content, $responsible, $gft, $cr);
            echo "Inserting as Topic<br>";
        } else {
            echo "Error: Neither taskContent nor topicContent is set<br>";
        }

        // Debugging: Echo the SQL query
        echo "SQL Query: " . htmlspecialchars($sql) . "<br>";

        if ($stmt->execute()) {
            echo "Data saved successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // If agendaId is not set, send an error message
        echo "Error: Agenda ID is not set";
    }
} else {
    // If it's not a POST request, send an error message
    echo "Error: Invalid request method";
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
// Check if agenda_id is set in the POST request
if(isset($_POST['agenda_id'])) {
    // Sanitize the input to prevent SQL injection
    $agendaId = mysqli_real_escape_string($conn, $_POST['agenda_id']);

    // Fetch agenda data from the database based on the agenda_id
    $sql = "SELECT * FROM agenda_data WHERE agenda_id = '$agendaId'";
    $result = $conn->query($sql);

    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Initialize an empty string to store HTML
        $html = '';

        // Loop through each row of data and construct HTML
        while ($row = $result->fetch_assoc()) {
            // Construct HTML for each row
            $html .= "<tr id='" . $row["item_id"] . "'>";
            $html .= "<td>" . $row["item_id"] . "</td>";
            $html .= "<td>" . $row["GFT"] . "</td>";
            $html .= "<td>" . $row["Topic"] . "</td>";
            $html .= "<td>" . $row["Status"] . "</td>";
            $html .= "<td>" . $row["Change_Request"] . "</td>";
            $html .= "<td>" . $row["Task"] . "</td>";
            $html .= "<td>" . $row["Comment"] . "</td>";
            $html .= "<td>" . $row["Milestone"] . "</td>";
            $html .= "<td>" . $row["Responsible"] . "</td>";
            $html .= "<td>" . $row["Start"] . "</td>";
            $html .= "<td><button class='btn btn-primary addRow'>New</button></td>";
            $html .= "<td><button class='btn btn-danger deleteRow'>Delete</button></td>";
            $html .= "</tr>";
        }

        // Echo the generated HTML
        echo $html;
    } else {
        // If no data found, echo an empty row or a message
        echo '<tr><td colspan="12">No data found for the selected agenda.</td></tr>';
    }
} else {
    // If agenda_id is not set in the POST request, return an error message
    echo '<tr><td colspan="12">Error: Agenda ID not provided.</td></tr>';
}

// Insert new row (Table) into mt_agenda_list table
if (isset($_POST['agenda_name'], $_POST['agenda_date']) && !empty($_POST['agenda_name']) && !empty($_POST['agenda_date'])) {
    $agendaName = $_POST['agenda_name'];
    $agendaDate = $_POST['agenda_date'];

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
    $insertSql = "INSERT INTO mt_agenda_list (agenda_name, agenda_date, module_team) VALUES ('$agendaName', '$agendaDate', '$selected_team')";
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
    echo "Agenda name or date not provided.";
}
// PERSONAL NOTES
if(isset($_POST['selectedAgendaId'])) {
    // Sanitize the input data
    $agendaId = mysqli_real_escape_string($conn, $_POST['selectedAgendaId']);

    // Fetch the agenda name based on the agenda ID
    $sql = "SELECT agenda_name FROM mt_agenda_list WHERE agenda_id = '$agendaId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the agenda name
        $row = $result->fetch_assoc();
        $agendaName = $row['agenda_name'];
        // Return the agenda name as the response
        echo $agendaName;
    } else {
        // If no matching agenda found, return an error message
        echo "Agenda not found";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $summary = $conn->real_escape_string($_POST['summary']);
    $user_id = intval($_POST['user_id']);

    // Check if there's already a record for the user
    $check_sql = "SELECT * FROM personal_tasks WHERE user_id = $user_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE personal_tasks SET summary = '$summary' WHERE user_id = $user_id";

        if ($conn->query($update_sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // Insert new record if no record found for the user
        $insert_sql = "INSERT INTO personal_tasks (user_id, summary) VALUES ($user_id, '$summary')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }
}
?>  