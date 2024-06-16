<?php
include_once('navigation.php');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                <div id="filterDiv">
                    <select id="changeRequestSelect" data-search="true" multiple class="styled-select w-10">
                        <option value="">Filter Change Request</option>
                        <?php
                        // Modify the SQL query to select both filter_checkbox = 1 and filter_checkbox = 0
                        $sql = "SELECT title, filter_checkbox FROM change_requests WHERE lead_module_team = ? AND fasttrack = 'Yes'";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $selected_team);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            // Check the value of filter_checkbox and set selected attribute if it's 1
                            if ($row['filter_checkbox'] == '1') {
                                echo '<option value="' . htmlspecialchars($row['title']) . '" selected>' . htmlspecialchars($row['title']) . '</option>';
                            } else {
                                echo '<option value="' . htmlspecialchars($row['title']) . '">' . htmlspecialchars($row['title']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
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
                            <input class="form-control datepicker" id="agendaDate" data-date-format="yyyy/mm/dd" placeholder="yyyy/mm/dd">
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
        <div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="forwardModalLabel">Forward</h1>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <label for="agendaSelectTask">Select Agenda:</label>
                            <select id="agendaSelectTask" data-search="true" class="form-select">
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
                                            echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>"
                                                . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                                                . "</option>";
                                        }
                                    }

                                    $stmt->close();
                                } else {
                                    echo "<option value=''>Error: " . htmlspecialchars($conn->error) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-light" id="sendTaskBtn" data-bs-dismiss="modal">Send</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var forwardTaskBtns = document.querySelectorAll('.forwardTaskBtns');
                var forwardTopicBtns = document.querySelectorAll('.forwardTopicBtns');
                var forwardModal = document.getElementById('forwardModal');
                var sendTaskBtn = document.getElementById('sendTaskBtn');

                forwardTaskBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var taskId = this.getAttribute('data-id');
                        forwardModal.setAttribute('data-task-id', taskId);

                        var modalTitle = forwardModal.querySelector('.modal-title');
                        modalTitle.textContent = 'Forward Task ID: ' + taskId;
                    });
                });

                forwardTopicBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var topicId = this.getAttribute('data-id');
                        forwardModal.setAttribute('data-topic-id', topicId);

                        var modalTitle = forwardModal.querySelector('.modal-title');
                        modalTitle.textContent = 'Forward Topic ID: ' + topicId;
                    });
                });

                sendTaskBtn.addEventListener('click', function() {
                    console.log("Send button clicked");
                    var taskId = forwardModal.getAttribute('data-task-id');
                    var topicId = forwardModal.getAttribute('data-topic-id');
                    var selectedAgendaId = document.getElementById('agendaSelectTask').value;
                    console.log('Task ID:', taskId);
                    console.log('Topic ID:', topicId);
                    console.log('Selected Agenda ID:', selectedAgendaId);

                    // Create an object with the data to be sent

                    var data = {};
                    if (taskId) {
                        data = {
                            task_id: taskId,
                            agenda_id: selectedAgendaId
                        };
                    } else {
                        data = {
                            topic_id: topicId,
                            agenda_id: selectedAgendaId
                        };
                    }

                    console.log('Data to send:', data);

                    // Perform the AJAX request
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'forwardtask.php', true); // Changed URL to forwardtask.php
                    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                console.log('Task successfully copied to the agenda');
                            } else {
                                console.error('Failed to copy task to the agenda', xhr.status, xhr.responseText);
                            }
                        }
                    };
                    xhr.send(JSON.stringify(data));

                });
            });
        </script>


        <table id="agendaTable" class="display">
            <thead>
                <tr>
                    <th align="center">Type</th>
                    <th align="center"></th> <!--GFT/Change Request/Task description -->
                    <th align="center">Responsible</th>
                    <th align="center" style="width: 225px;">Actions</th> <!-- Adjust the width as needed -->
                </tr>
            </thead>
            <tbody>
                <div id="tbodyDiv">
                    <?php
                    if ($result_gfts->num_rows > 0) {
                        while ($row_gft = $result_gfts->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>GFT "; // Type
                            echo "<td><strong>GFT " . $row_gft["name"] . "</strong></td>"; // GFT
                            echo "<td></td>"; // Responsible 

                            echo "<td>
                            <div class='button-container'>
                            <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                            <div class='dropdown-menu'>
                                <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            </div>
                        </div>
                            </div>
                          </td>"; // Actions


                            echo "</tr>";
                            // Fetch change requests based on $selected_team and $row_gft["name"]
                            $selected_team = $row_gft["moduleteam"];
                            $selected_gft = $row_gft["name"];
                            $sql_change_requests = "SELECT title FROM change_requests WHERE lead_module_team = '$selected_team' AND lead_gft = '$selected_gft' AND fasttrack = 'Yes' AND filter_checkbox = '1'";
                            $result_change_requests = $conn->query($sql_change_requests);

                            if ($result_change_requests->num_rows > 0) {
                                echo "<tr>";
                                echo "<td></td>"; // Type
                                echo "<td><strong>Change requests:</strong></td>"; // Change Request
                                echo "<td></td>"; // Responsible
                                echo "<td></td>"; // Actions
                                //echo "<td></td>"; // Meeting Resubmition Checkbox (needs to be saved to DB)
                                echo "</tr>";
                                while ($row_change_request = $result_change_requests->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td></td>"; // Type
                                    echo "<td>" . $row_change_request["title"] . "</a></td>"; // Change Request
                                    echo "<td></td>"; // Responsible

                                    echo "<td>
                                    <div class='button-container'>
                                    <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                    <div class='dropdown-menu'>
                                        <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                        <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    </div>
                                </div>
                                  </td>"; // Actions
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
                                //echo "<td></td>"; // Empty column
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
                        //echo "<td></td>"; // Empty column
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
                                echo "<tr id='{$row_topic["id"]}' data-type='topic' data-id='{$row_topic["id"]}'>";
                                echo "<td><strong>Topic</strong></td>"; // Empty column for module team
                                echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid white; max-width: 200px;'>" . htmlspecialchars($row_topic["name"]) . "</td>";
                                echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid white;'>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
                                echo "<td>
                                        <div class='button-container'>
                                            <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                            <div class='dropdown-menu'>
                                                <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                                <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                            </div>
                                            <button class='button-12 deleteRow' role='button'>-</button>
                                            <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$row_topic["id"]}' class='button-12 forwardTopicBtns' role='button'>→</button>  
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        }

                        $sql_tasks = "SELECT * FROM tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL) AND deleted = 0";
                        $stmt_tasks = $conn->prepare($sql_tasks);
                        $stmt_tasks->bind_param('isss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped);
                        $stmt_tasks->execute();
                        $result_tasks = $stmt_tasks->get_result();

                        if ($result_tasks->num_rows > 0) {
                            while ($row_task = $result_tasks->fetch_assoc()) {
                                $taskId = $row_task["id"];
                                echo "<tr id='{$taskId}' data-type='task' data-id='{$taskId}'>";
                                echo "<td><strong>Task</strong></td>"; // Static task name or type
                                echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid white; max-width: 200px;'>" . htmlspecialchars($row_task["name"]) . "</td>";
                                echo "<td style='background-color: #212529 !important; width: 100px !important;'>"; // Apply background color and minimum width
                                echo "<input class='editabletasktopic-cell' data-column='responsible' type='text' style='background-color: #212529 !important; border: 1px solid white; width: 100%;' value='" . htmlspecialchars($row_task["responsible"]) . "'>"; // Adjust width to fill the container
                                echo "<br>";
                                echo "<br>";
                                echo "<input class='editabletasktopic-cell datepicker' data-column='deadline' type='text' id='datepicker-{$taskId}' style='background-color: #212529 !important; border: 1px solid white; width: 70%;' value='" . htmlspecialchars($row_task["deadline"]) . "'>"; // Use an ID for the input field
                                echo "<button class='asap-button' data-task-id='{$taskId}' style='color: white;'>ASAP</button>"; 
                                echo "</td>";
                                echo "<td>
                                        <div class='button-container'>
                                            <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                            <div class='dropdown-menu'>
                                                <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                                <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                            </div>
                                            <button class='button-12 deleteRow' role='button'>-</button>
                                            <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$taskId}' class='button-12 forwardTaskBtns' role='button'>→</button>  
                                        </div>
                                      </td>"; // Actions
                                echo "</tr>";
                            }
                        }                        
                    }
                    ?>
                </div>
            </tbody>
</body>

</html>
<?php
$conn->close();
?>