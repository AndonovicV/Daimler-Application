<?php
include_once('php-attendance\inc\navigationAgenda.php');
include 'conn.php';

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = ""; // Default value if not set
}

// Fetch GFTs connected to the selected team
$sql_gfts = "SELECT DISTINCT GFT as name, Module_team as moduleteam FROM spec_book WHERE Module_team = '$selected_team'";
$result_gfts = $conn->query($sql_gfts);
$selectedAgendaId = isset($_GET['agenda_id']) ? $_GET['agenda_id'] : null;
?>

<!DOCTYPE html>
<html data-bs-theme="dark" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datatable</title>

    <!-- Libraries -->
    <script src="plugins\jQuery\jquery.min.js"></script>
    <link href="plugins\bootstrap-5.3.3-dist\css\bootstrap.min.css" rel="stylesheet">
    <script src="plugins\bootstrap-5.3.3-dist\js\bootstrap.min.js"></script>
    <link rel="stylesheet" href="plugins\datatables\datatables.min.css">
    <script src="plugins\datatables\datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <!--Link to Virtual select Plugin CSS-->
    <link rel="stylesheet" href="plugins\virtual_select\virtual-select.min.css">
    <!--Link to Virtual select Plugin JS-->
    <script src="plugins/virtual_select/virtual-select.min.js"></script>
   
    <!--DATATABLE LIBRARIES-->

    <!--Link to datepicker 1 JS-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <!--Link to datepicker 2 JS-->
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.5.2/js/dataTables.dateTime.min.js"></script>
    <!--Link to checkbox CSS-->
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
    <!--Link to checkbox JS-->
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

    <!-- Custom CSS -->
    <link href="custom_css\mt_agenda.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="custom_js/mt_agenda.js"></script>

</head>

<body>
    <div class="container">
        <h3>Select or Create Agenda:</h3>
        <select id="agendaSelect" data-search="true">
            <option value="">Select Agenda...</option>
        <?php

        // Prepare the SQL query with a placeholder for the selected team
        $sql = "SELECT * FROM mt_agenda_list WHERE module_team = ?";

        // Initialize the statement
        $stmt = $conn->prepare($sql);

        // Check if the statement was prepared successfully
        if ($stmt) {
            // Bind the parameter to the prepared statement
            $stmt->bind_param('s', $selected_team); // Use 'i' if module_team is an integer
            // Execute the statement
            $stmt->execute();
            // Get the result
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Check if the current option is the selected one
                    $selected = ($row["agenda_id"] == $selectedAgendaId) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>" . htmlspecialchars($row["agenda_name"]) . "</option>";
                }
            }

            // Close the statement
            $stmt->close();
        } else {
            // Handle potential errors
            echo "Error: " . $conn->error;
        }
        ?>
        </select>

        <script>
            VirtualSelect.init({
                ele: '#agendaSelect'
            });
        </script>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-light" id="createAgendaBtn">Create new agenda</button>
        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#personalTaskModal" id="modalBtn">Personal Task</button>

        <!-- Modal for creating a new agenda -->
        <div class="modal fade" id="createAgendaModal" tabindex="-1" aria-labelledby="createAgendaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAgendaModalLabel">Create New Agenda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="agendaName" class="form-label">Agenda Name:</label>
                            <input type="text" class="form-control" id="agendaName">
                        </div>
                        <div class="mb-3">
                            <label for="agendaDate" class="form-label">Agenda Date:</label>
                            <input type="date" class="form-control" id="agendaDate">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-light" id="createAgendaConfirmBtn">Create</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Task Modal -->
        <div class="modal fade" id="personalTaskModal" tabindex="-1" aria-labelledby="personalTaskLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- Display the selected agenda ID in the modal title -->
                        <h1 class="modal-title fs-5" id="personalTaskLabel">Selected Agenda ID</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <textarea name="summary" id="summary" rows="4" class="text" style="width: 100%;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <table id="agendaTable" class="display">
            <thead>
                <tr>
                    <th align="center">Type</th>
                    <th align="center"></th> <!--GFT/Change Request/Task description -->
                    <th align="center">Responsible</th>
                    <th align="center">Deadline</th>
                    <th align="center" class="actions">Actions</th>
                    <th align="center">M.R.</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result_gfts->num_rows > 0) {
                while ($row_gft = $result_gfts->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><strong>GFT "; // Type
                    echo "<td><strong>GFT " . $row_gft["name"] . "</strong></td>"; // GFT
                    echo "<td></td>"; // Responsible 
                    echo "<td></td>"; // Deadline
                    echo "<td><button class='button-12 addRow' role='button'>New Row</button></td>"; // Actions
                    echo "<td><input type='checkbox'></td>"; // Meeting Resubmition Checkbox (needs to be saved to DB)
                    echo "</tr>";
                    // Fetch change requests based on $selected_team and $row_gft["name"]
                    $selected_team = $row_gft["moduleteam"];
                    $selected_gft = $row_gft["name"];
                    $sql_change_requests = "SELECT title FROM change_requests WHERE lead_module_team = '$selected_team' AND lead_gft = '$selected_gft'";
                    $result_change_requests = $conn->query($sql_change_requests);

                    if ($result_change_requests->num_rows > 0) {
                        while ($row_change_request = $result_change_requests->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td></td>"; // Type
                            echo "<td>" . $row_change_request["title"] . "</a></td>"; // Change Request
                            echo "<td></td>"; // Responsible
                            echo "<td></td>"; // Empty column for module team
                            echo "<td><button class='button-12 addRow' role='button'>New Row</button></td>"; // Actions
                            echo "<td><input type='checkbox'></td>"; // Meeting Resubmition Checkbox (needs to be saved to DB)
                            echo "</tr>";

                            // Fetch topics and tasks for this change request
                            fetchTasksAndTopics($conn, $row_gft["name"], $row_change_request["title"]);
                        }
                    } else {
                        echo "<tr>";
                        echo "<td></td>"; // Empty column for module team
                        echo "<td colspan='5'>No change requests for GFT " . $row_gft["name"] . "</td>";
                        echo "<td></td>"; // Responsible - You may need to add data here based on your requirements
                        echo "<td></td>"; // Empty column
                        echo "<td></td>"; // Empty column
                        echo "<td></td>"; // Empty column
                        echo "</tr>";

                        // Fetch topics and tasks for this GFT only
                        fetchTasksAndTopics($conn, $row_gft["name"], null);
                    }
                }
            } else {
                echo "<tr>";
                echo "<td></td>"; // Empty column for module team
                echo "<td colspan='5'>No change requests for this team</td>";
                echo "<td></td>"; // Responsible - You may need to add data here based on your requirements
                echo "<td></td>"; // Empty column
                echo "<td></td>"; // Empty column
                echo "<td></td>"; // Empty column
                echo "</tr>";
            }

            // Function to fetch tasks and topics
            function fetchTasksAndTopics($conn, $gft, $cr) {
                // Remove "title for " from the CR value if present
                $cr_stripped = $cr ? str_replace('title for ', '', $cr) : null;
                $selectedAgendaId = isset($_GET['agenda_id']) ? $_GET['agenda_id'] : null;

                // Debugging output
                //echo "<tr><td colspan='5'>Fetching Topics and Tasks for GFT: " . htmlspecialchars($gft) . " and CR: " . htmlspecialchars($cr_stripped) . "</td></tr>";

                $sql_topics = "SELECT * FROM topics WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL)";
                $stmt_topics = $conn->prepare($sql_topics);
                $stmt_topics->bind_param('isss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped);
                $stmt_topics->execute();
                $result_topics = $stmt_topics->get_result();
                

                if ($result_topics->num_rows > 0) {
                    while ($row_topic = $result_topics->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><strong>Topic</strong></td>"; // Empty column for module team
                        echo "<td>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Type
                        echo "<td>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
                        echo "<td></td>"; // Empty column
                        echo "<td><button class='button-12 addRow' role='button'>New Row</button></td>"; // Actions
                        echo "<td><button class='button-12 deleteRow'>Delete</button></td>"; // Empty column
                        echo "</tr>";
                    }
                }

                $sql_tasks = "SELECT * FROM tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL)";
                $stmt_tasks = $conn->prepare($sql_tasks);
                $stmt_tasks->bind_param('isss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped);
                $stmt_tasks->execute();
                $result_tasks = $stmt_tasks->get_result();

                if ($result_tasks->num_rows > 0) {
                    while ($row_task = $result_tasks->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><strong>Task</strong></td>"; // Empty column for module team
                        echo "<td>" . htmlspecialchars($row_task["name"]) . "</td>"; // Type
                        echo "<td>" . htmlspecialchars($row_task["responsible"]) . "</td>"; // Responsible
                        echo "<td></td>"; // Empty column
                        echo "<td><button class='button-12 addRow' role='button'>New Row</button></td>"; // Actions
                        echo "<td><button class='button-12 deleteRow'>Delete</button></td>"; // Empty column
                        echo "</tr>";
                    }
                }
            }
            ?>
        </tbody>
</body>
</html>
<?php
$conn->close();
?>
