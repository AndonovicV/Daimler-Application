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


//PERSONAL TASK variables
$user_id = 1; // Example user ID
$sql_personal_tasks = "SELECT summary FROM personal_tasks WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
$result_personal_tasks = $conn->query($sql_personal_tasks);

if ($result_personal_tasks->num_rows > 0) {
    // Output data of each row
    $row = $result_personal_tasks->fetch_assoc();
    $summary = $row['summary'];
} else {
    $summary = "";
}
?>
<?php
function generateAgendaSelect($conn, $selected_team, $selectedAgendaId)
{
    $output = '<select id="agendaSelect" data-search="true" class="form-select">';
    $output .= '<option value="">Select Agenda...</option>';

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
                $output .= "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>"
                    . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                    . "</option>";

                // Display the agenda_date if this option is the selected one
                if ($selected) {
                    $agenda_date = htmlspecialchars($row["agenda_date"]);
                }
            }
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle potential errors
        $output .= "<option value=''>Error: " . htmlspecialchars($conn->error) . "</option>";
    }

    $output .= '</select>';
    return $output;
}

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

    <!--Link to Bootstrap Datepicker Plugin-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

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
        <div class="container mt-5">
            <h1 style="color: #777" class='mt-4'>MT AGENDA</h1>
            <div class="mb-3">
                <select id="agendaSelect" data-search="true" class="styled-select w-100" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;">
                    <option value="">Select Agenda...</option>
                    <?php
                    $sql = "SELECT * FROM mt_agenda_list WHERE module_team = ?";
                    $stmt = $conn->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param('s', $selected_team);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $selected = ($row["agenda_id"] == $selectedAgendaId) ? "selected" : "";
                                echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>" . htmlspecialchars($row["agenda_name"]) . "</option>";

                                if ($selected) {
                                    $agenda_date = htmlspecialchars($row["agenda_date"]);
                                }
                            }
                        }

                        $stmt->close();
                    } else {
                        echo "Error: " . $conn->error;
                    }
                    ?>
                </select>
                <!--Virtual Select Trigger-->
                <script>
                    VirtualSelect.init({
                        ele: '#agendaSelect'
                    });
                </script>
            </div>
            <div class="d-flex justify-content-between mb-3">

                <button type="button" class="btn btn-light flex-fill mx-1" data-bs-toggle="modal" data-bs-target="#personalTaskModal" id="modalBtn" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
                    Personal Task
                </button>
                <button type="button" id="createAgendaBtn" class="btn btn-primary flex-fill mx-1" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
                    Create a new agenda
                </button>
                <button type="button" class="btn btn-primary flex-fill mx-1" onclick="window.location.href = 'protokol.php?protokol_id=<?php echo $selectedAgendaId; ?>'" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
                    To Protokoll
                </button>
                <select id="changeRequestSelect" data-search="true" class="styled-select w-10" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;">
                    <option value="">Change Request Filter</option>
                    <?php
                    $sql = "SELECT * FROM change_requests WHERE fasttrack = 'Yes' AND `lead_module_team` = '$selected_team'";
                    $stmt = $conn->prepare($sql);
                    // No need for bind_param as the SQL query has no placeholders
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['title']) . '">' . htmlspecialchars($row['title']) . '</option>';
                    }
                    ?>
                </select>
                <script>
                    VirtualSelect.init({
                        multiple: true,
                        ele: '#changeRequestSelect'
                    });
                </script>
            </div>

            <?php
            if (isset($agenda_date)) {
                echo "<h3 class='mt-4'>Agenda Date: $agenda_date</h3>";
            }
            ?>

        </div>
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
                            <input for="agendaDate" class="form-control" id="agendaDate" data-date-format="yyyy/mm/dd" placeholder="yyyy/mm/dd">
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
        <!-- Personal Task Modal -->
        <div class="modal fade" id="personalTaskModal" tabindex="-1" aria-labelledby="personalTaskLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- Display the selected agenda ID in the modal title -->
                        <h1 class="modal-title fs-5" id="personalTaskLabel">Personal Tasks</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <textarea name="summary" id="summary" rows="16" class="text" style="width: 100%;">><?php echo htmlspecialchars($summary); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="personalTaskBtn">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Personal Task Modal -->
        <div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="forwardModal">Forward task</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <label for="agendaSelect">Select Agenda:</label>
                            <select id="agendaSelect" data-search="true" class="form-select">
                                <option value="">Select Agenda...</option>
                                <?php
                                // Example variables
                                $selectedAgendaId = 1; // Example selected agenda ID

                                // Enable error reporting
                                ini_set('display_errors', 1);
                                ini_set('display_startup_errors', 1);
                                error_reporting(E_ALL);

                                // Prepare the SQL query with a placeholder for the selected team
                                $sql = "SELECT * FROM mt_agenda_list WHERE module_team = ?";

                                // Initialize the statement
                                $stmt = $conn->prepare($sql);

                                // Check if the statement was prepared successfully
                                if ($stmt) {
                                    // Bind the parameter to the prepared statement
                                    $stmt->bind_param('s', $selected_team); // Use 'i' if module_team is an integer

                                    // Print the SQL statement and parameters to the console
                                    echo "<script>console.log('SQL: " . $sql . "');</script>";
                                    echo "<script>console.log('Selected Team: " . $selected_team . "');</script>";

                                    // Execute the statement
                                    $stmt->execute();

                                    // Get the result
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            // Check if the current option is the selected one
                                            $selected = ($row["agenda_id"] == $selectedAgendaId) ? "selected" : "";
                                            echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>"
                                                . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                                                . "</option>";
                                        }
                                    } else {
                                        echo "<script>console.log('No results found');</script>";
                                    }

                                    // Close the statement
                                    $stmt->close();
                                } else {
                                    // Handle potential errors
                                    echo "<option value=''>Error: " . htmlspecialchars($conn->error) . "</option>";
                                    echo "<script>console.log('Error: " . htmlspecialchars($conn->error) . "');</script>";
                                }
                                ?>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="forwardModalsave" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="forwardModal">Forward task</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <label for="agendaSelect">Select Agenda:</label>
                            <select id="agendaSelect" data-search="true" class="form-select">
                                <option value="">Select Agenda...</option>
                                <?php
                                // Example variables
                                $selected_team = 'TeamA'; // Example team
                                $selectedAgendaId = 1; // Example selected agenda ID

                                // Prepare the SQL query with a placeholder for the selected team
                                $sql = "SELECT * FROM mt_agenda_list WHERE module_team = ?";

                                // Initialize the statement
                                $stmt = $conn->prepare($sql);

                                // Check if the statement was prepared successfully
                                if ($stmt) {
                                    echo "EYO";
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
                                            echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>"
                                                . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                                                . "</option>";
                                        }
                                    }

                                    // Close the statement
                                    $stmt->close();
                                } else {
                                    // Handle potential errors
                                    echo "<option value=''>Error: " . htmlspecialchars($conn->error) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="forwardModalsave">Save changes</button>
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
                        echo "<td><button class='button-12 addRow' role='button'>+</button> </td>"; // Actions
                        echo "<td></td>"; // Meeting Resubmition Checkbox (needs to be saved to DB)
                        echo "</tr>";
                        // Fetch change requests based on $selected_team and $row_gft["name"]
                        $selected_team = $row_gft["moduleteam"];
                        $selected_gft = $row_gft["name"];
                        $sql_change_requests = "SELECT title FROM change_requests WHERE lead_module_team = '$selected_team' AND lead_gft = '$selected_gft' AND fasttrack = 'Yes'";
                        $result_change_requests = $conn->query($sql_change_requests);

                        if ($result_change_requests->num_rows > 0) {
                            echo "<tr>";
                            echo "<td></td>"; // Type
                            echo "<td><strong>Change requests:</strong></td>"; // Change Request
                            echo "<td></td>"; // Responsible
                            echo "<td></td>"; // Actions
                            echo "<td></td>"; // Meeting Resubmition Checkbox (needs to be saved to DB)
                            echo "</tr>";
                            while ($row_change_request = $result_change_requests->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td></td>"; // Type
                                echo "<td>" . $row_change_request["title"] . "</a></td>"; // Change Request
                                echo "<td></td>"; // Responsible
                                echo "<td><button class='button-12 addRow' role='button'>+</button> </td>"; // Actions
                                echo "<td></td>"; // Meeting Resubmition Checkbox (needs to be saved to DB)
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
                    echo "</tr>";
                }

                // Function to fetch tasks and topics
                function fetchTasksAndTopics($conn, $gft, $cr)
                {
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
                            echo "<tr id='topic-{$row_topic["id"]}' data-type='topic' data-id='{$row_topic["id"]}'>";
                            echo "<td><strong>Topic</strong></td>"; // Empty column for module team
                            echo "<td>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Type
                            echo "<td>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
                            echo "<td><button class='button-12 addRow' role='button'>+</button> <button class='button-12 deleteRow' role='button'>-</button></td>"; // Actions
                            echo "<td><button data-bs-toggle='modal' data-bs-target='#forwardModal' id='modalBtn' class='button-12'  role='button'>→</button></td>"; // Actions
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
                            echo "<tr id='task-{$row_task["id"]}' data-type='task' data-id='{$row_task["id"]}'>";
                            echo "<td><strong>Task</strong></td>"; // Empty column for module team
                            echo "<td>" . htmlspecialchars($row_task["name"]) . "</td>"; // Type
                            echo "<td>" . htmlspecialchars($row_task["responsible"]) . "</td>"; // Responsible
                            echo "<td><button class='button-12 addRow' role='button'>+</button> <button class='button-12 deleteRow' role='button'>-</button></td>"; // Actions
                            echo "<td><button data-bs-toggle='modal' data-bs-target='#forwardModal' id='modalBtn' class='button-12'  role='button'>→</button></td>"; // Actions
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