<?php 
session_start();
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
        $topic = $_POST['topic'] ?? '';
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
            $sql = "INSERT INTO tasks (agenda_id, name, responsible, gft, cr, topic_id, details, deleted, sent) 
                    VALUES (?, ?, ?, ?, ?, ?, '', 0, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssss", $agendaId, $content, $responsible, $gft, $cr, $topic);
            echo "Inserting as Task<br>";
            if ($stmt->execute()) {
                echo "Data saved successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
            echo 'Task successfully created';
            $sql_get_task_id = "SELECT id FROM tasks WHERE agenda_id = ? ORDER BY id DESC LIMIT 1";
            $stmt_get_task_id = $conn->prepare($sql_get_task_id);
            $stmt_get_task_id->bind_param("i", $agendaId);
            $stmt_get_task_id->execute();
            $result_task_id = $stmt_get_task_id->get_result();
            
            $row_task_id = $result_task_id->fetch_assoc();
            $taskId = $row_task_id['id'];
                        
            // Insert into domm_information table
            $sql_information = "INSERT INTO domm_information (agenda_id, gft, cr, task_id, content) VALUES (?, ?, ?, ?, ?)";
            $stmt_information = $conn->prepare($sql_information);
            $stmt_information->bind_param("isiss", $agendaId, $gft, $cr, $taskId, $content);
            $stmt_information->execute();
            
            // Insert into domm_assignment table
            $sql_assignment = "INSERT INTO domm_assignment (agenda_id, gft, cr, task_id, content) VALUES (?, ?, ?, ?, ?)";
            $stmt_assignment = $conn->prepare($sql_assignment);
            $stmt_assignment->bind_param("isiss", $agendaId, $gft, $cr, $taskId, $content);
            $stmt_assignment->execute();
            
            // Insert into domm_decision table
            $sql_decision = "INSERT INTO domm_decision (agenda_id, gft, cr, task_id, content) VALUES (?, ?, ?, ?, ?)";
            $stmt_decision = $conn->prepare($sql_decision);
            $stmt_decision->bind_param("isiss", $agendaId, $gft, $cr, $taskId, $content);
            $stmt_decision->execute();

        } elseif (isset($_POST['topicContent'])) {
            // It's a topic
            $sql = "INSERT INTO topics (agenda_id, name, responsible, gft, cr, details) 
                    VALUES (?, ?, ?, ?, ?, '')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $agendaId, $content, $responsible, $gft, $cr);
            echo "Inserting as Topic<br>";
            if ($stmt->execute()) {
                echo "Data saved successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Error: Neither taskContent nor topicContent is set<br>";
        }

        // Debugging: Echo the SQL query
        echo "SQL Query: " . htmlspecialchars($sql) . "<br>";



        $stmt_task->close();
        $stmt_information->close();
        $stmt_assignment->close();
        $stmt_decision->close();
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
    $rowType = $_POST['rowType'];

    switch ($rowType) {
        case 'topic':
            // Delete Topic logic
            $sql = "DELETE FROM topics WHERE id = ?";
            break;
        case 'task':
            // Delete Task logic
            $sql = "UPDATE tasks SET deleted = 1 WHERE id = ?;";
            break;
        default:
            echo "Invalid row type";
            exit;
    }
    echo $stmt;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $rowId);
    if ($stmt->execute()) {
        echo "Row deleted successfully";
    } else {
        echo "Error deleting row: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
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

//PERSONAL TASK index.php
if (isset($_POST['save_task_trigger'])) {
    $summary = $conn->real_escape_string($_POST['summary']);
    $user_id = intval($_POST['user_id']);

    // Check if there's already a record for the user
    $check_sql = "SELECT * FROM domm_personal_tasks WHERE user_id = $user_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE domm_personal_tasks SET summary = '$summary' WHERE user_id = $user_id";

        if ($conn->query($update_sql) === TRUE) {
            echo "Record updated successfully";
            header("Location:index.php");
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // Insert new record if no record found for the user
        $insert_sql = "INSERT INTO domm_personal_tasks (user_id, summary) VALUES ($user_id, '$summary')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }
}

//PERSONAL TASK mt agenda and protokoll
if ($_SERVER["REQUEST_METHOD"] == "POST") {    // needs to be an if (isset($_POST trigger
    $summary = $conn->real_escape_string($_POST['summary']);
    $user_id = intval($_POST['user_id']);

    // Check if there's already a record for the user
    $check_sql = "SELECT * FROM domm_personal_tasks WHERE user_id = $user_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE domm_personal_tasks SET summary = '$summary' WHERE user_id = $user_id";

        if ($conn->query($update_sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // Insert new record if no record found for the user
        $insert_sql = "INSERT INTO domm_personal_tasks (user_id, summary) VALUES ($user_id, '$summary')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }
}

if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_titles'])) {
        $selected_titles = $_POST['selected_titles'];
        $selectedAgendaId = isset($_SESSION['selected_agenda_id']) ? $_SESSION['selected_agenda_id'] : null;

        if ($selectedAgendaId !== null) {
            // Clear existing filter selections for the current agenda
            $clear_sql = "DELETE FROM domm_agenda_change_request_filters WHERE agenda_id = ?";
            $clear_stmt = $conn->prepare($clear_sql);
            $clear_stmt->bind_param('i', $selectedAgendaId);
            $clear_stmt->execute();

            foreach ($selected_titles as $title) {
                // Insert new filter selections
                $insert_sql = "INSERT INTO domm_agenda_change_request_filters (agenda_id, change_request_id, filter_active) VALUES (?, (SELECT ID FROM change_requests WHERE title = ?), 1)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param('is', $selectedAgendaId, $title);
                $insert_stmt->execute();
                $insert_stmt->close();
            }
            echo "Success";
        } else {
            echo "No agenda selected. Session value: " . print_r($_SESSION, true);
        }
    } elseif (isset($_POST['title'], $_POST['agenda_id'], $_POST['action']) && $_POST['action'] === 'unselect') {
        $title = $_POST['title'];
        $agendaId = $_POST['agenda_id'];
    
        $delete_sql = "DELETE FROM domm_agenda_change_request_filters WHERE agenda_id = ? AND change_request_id = (SELECT ID FROM change_requests WHERE title = ? LIMIT 1)";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param('is', $agendaId, $title);
        $delete_stmt->execute();
    
        if ($delete_stmt->affected_rows > 0) {
            echo "Success";
        } else {
            echo "Failed to unselect filter: No such filter found or other error.";
        }
    
        $delete_stmt->close();
    } else {
        echo "No data received";
    }
}

$conn->close();
?>  