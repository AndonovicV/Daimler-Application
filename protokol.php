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
$selectedAgendaId = isset($_GET['protokol_id']) ? $_GET['protokol_id'] : null;


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
    <link href="custom_css\protokol.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="custom_js/protokol.js"></script>

</head>

<body>
    
    <div class="container">
    <div class="container mt-5" style="color: #fff;">
    <h1 style="color: #777" class='mt-4'>PROTOKOLL</h1>
    <select 
        id="protokolSelect" 
        data-search="true" 
        class="styled-select w-100 mb-3" 
        style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;"> <!-- This should work but it doesn't -->
        <option value="">Select protocol...</option>
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
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Check if the current option is the selected one
                            $selected = ($row["agenda_id"] == $selectedAgendaId) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>" 
                                 . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                                 . "</option>";
                            if ($selected) {
                                    $agenda_date = htmlspecialchars($row["agenda_date"]);
                                }
                        }
                    }
                }
            }

            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
        ?>
    </select>
    <script>
                    VirtualSelect.init({
                        ele: '#protokolSelect'
                    });
                </script>
    <div class="d-flex justify-content-between mb-3">

        <button type="button" class="btn btn-light flex-fill mx-1" data-bs-toggle="modal" data-bs-target="#personalTaskModal" id="modalBtn" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
            Personal Task
        </button>

        <button type="button" class="button-12 addRow flex-fill mx-1" onclick="window.location.href = 'mt_agenda.php?agenda_id=<?php echo $selectedAgendaId; ?>'"  style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
            To Agenda
        </button>

    </div>

    <?php
    if (isset($agenda_date)) {
        echo "<h3 class='mt-4'>Agenda Date: $agenda_date</h3>";
    }
    ?>
    </div>


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
        <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Add event listener for the blur event on contenteditable cells
                    document.querySelectorAll('.editable-cell').forEach(function(cell) {
                        cell.addEventListener('blur', function() {
                            var $cell = $(this);
                            var newValue = $cell.text();
                            var taskId = $cell.closest('tr').data('id');
                            var rowType = $cell.closest('tr').data('type');

                            // Send AJAX request to save the content
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'saveContent.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === XMLHttpRequest.DONE) {
                                    if (xhr.status === 200) {
                                        console.log('Content saved successfully');
                                    } else {
                                        console.error('Failed to save content');
                                    }
                                }
                            };
                            xhr.send(JSON.stringify({
                                task_id: taskId,
                                row_type: rowType,
                                content: newValue
                            }));
                        });
                    });
                });
        </script>


            <?php
            if (isset($protokol_date)) {
                echo "<h3 class='mt-4'>Agenda Date: $protokol_date</h3>";
            }
            ?>

    <table id="protokolTable" class="display">
    <thead>
        <tr>
            <th align="center">Type</th>
            <th align="center"></th> <!--GFT/Change Request/Task description -->
            <th align="center">Responsible</th>
            <th align="center" class="actions">Actions</th>
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
                $sql_change_requests = "SELECT title FROM change_requests WHERE lead_module_team = '$selected_team' AND lead_gft = '$selected_gft' AND fasttrack = 'Yes'";
                $result_change_requests = $conn->query($sql_change_requests);

                if ($result_change_requests->num_rows > 0) {
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
            echo "</tr>";
        }

        // Function to fetch tasks and topics
        function fetchTasksAndTopics($conn, $gft, $cr)
        {
            // Remove "title for " from the CR value if present
            $cr_stripped = $cr ? str_replace('title for ', '', $cr) : null;
            $selectedAgendaId = isset($_GET['protokol_id']) ? $_GET['protokol_id'] : null;

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
                    echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid white;'>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Type
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

            $sql_tasks = "SELECT * FROM tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL)";
            $stmt_tasks = $conn->prepare($sql_tasks);
            $stmt_tasks->bind_param('isss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped);
            $stmt_tasks->execute();
            $result_tasks = $stmt_tasks->get_result();

            if ($result_tasks->num_rows > 0) {
                while ($row_task = $result_tasks->fetch_assoc()) {
                    echo "<tr id='{$row_task["id"]}' data-type='task' data-id='{$row_task["id"]}'>";
                    echo "<td><strong>Task</strong></td>"; // Empty column for module team
                    echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid white;'>" . htmlspecialchars($row_task["name"]) . "</td>"; // Type
                    echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid white;'>" . htmlspecialchars($row_task["responsible"]) . "</td>"; // Responsible
                    echo "<td>
                    <div class='button-container'>
                    <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                    <div class='dropdown-menu'>
                        <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                        <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                        <button class='dropdown-item' onclick='addTopic()'>Information</button>
                        <button class='dropdown-item' onclick='addTopic()'>Assignment</button>
                        <button class='dropdown-item' onclick='addTopic()'>Decision</button>
                    </div>
                    <button class='button-12 deleteRow' role='button'>-</button>
                    <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$row_task["id"]}' class='button-12 forwardTaskBtns' role='button'>→</button>  
                    </div>
                  </td>"; // Actions
                    echo "</tr>";

                    // Load rows from information table
                    $sql_information = "SELECT * FROM information WHERE task_id = ?";
                    $stmt_information = $conn->prepare($sql_information);
                    $stmt_information->bind_param('i', $row_task["id"]);
                    $stmt_information->execute();
                    $result_information = $stmt_information->get_result();

                    while ($row_information = $result_information->fetch_assoc()) {
                        echo "<tr id='task-{$row_task["id"]}' data-type='I' data-id='{$row_task["id"]}'>";
                        echo "<td><strong>I</strong></td>"; // Empty column for module team
                        echo "<td class='editable-cell' contenteditable='true'>"  .  htmlspecialchars($row_information["content"]) . "</td>"; // Content
                        echo "<td></td>"; // Responsible
                        echo "<td>
                        <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            <button class='dropdown-item' onclick='addTopic()'>Information</button>
                            <button class='dropdown-item' onclick='addTopic()'>Assignment</button>
                            <button class='dropdown-item' onclick='addTopic()'>Decision</button>
                        </div>
                        <button class='button-12 deleteRow' role='button'>-</button>
                        
                    </div>
                      </td>"; // Actions
                     echo "</tr>";
                    }

                    // Load rows from assignment table (similar process as information)
                    $sql_assignment = "SELECT * FROM assignment WHERE task_id = ?";
                    $stmt_assignment = $conn->prepare($sql_assignment);
                    $stmt_assignment->bind_param('i', $row_task["id"]);
                    $stmt_assignment->execute();
                    $result_assignment = $stmt_assignment->get_result();

                    while ($row_assignment = $result_assignment->fetch_assoc()) {
                        echo "<tr id='task-{$row_task["id"]}' data-type='A' data-id='{$row_task["id"]}'>";
                        echo "<td><strong>A</strong></td>"; // Empty column for module team
                        echo "<td class='editable-cell' contenteditable='true'>" .  htmlspecialchars($row_assignment["content"]) . "</td>"; // Content
                        echo "<td></td>"; // Responsible
                        echo "<td>
                        <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            <button class='dropdown-item' onclick='addTopic()'>Information</button>
                            <button class='dropdown-item' onclick='addTopic()'>Assignment</button>
                            <button class='dropdown-item' onclick='addTopic()'>Decision</button>
                        </div>
                        <button class='button-12 deleteRow' role='button'>-</button>
                    </div>
                      </td>"; // Actions
                        echo "</tr>";
                    }

                    // Load rows from decision table (similar process as information)
                    $sql_decision = "SELECT * FROM decision WHERE task_id = ?";
                    $stmt_decision = $conn->prepare($sql_decision);
                    $stmt_decision->bind_param('i', $row_task["id"]);
                    $stmt_decision->execute();
                    $result_decision = $stmt_decision->get_result();

                    while ($row_decision = $result_decision->fetch_assoc()) {
                        echo "<tr id='task-{$row_task["id"]}' data-type='D' data-id='{$row_task["id"]}'>";
                        echo "<td><strong>D</strong></td>"; // Empty column for module team
                        echo "<td class='editable-cell' contenteditable='true'>" . htmlspecialchars($row_decision["content"]) . "</td>"; // Content
                        echo "<td></td>"; // Responsible
                        echo "<td>
                        <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTask()'>Task</button>
                            <button class='dropdown-item' onclick='addTopic()'>Topic</button>
                            <button class='dropdown-item' onclick='addTopic()'>Information</button>
                            <button class='dropdown-item' onclick='addTopic()'>Assignment</button>
                            <button class='dropdown-item' onclick='addTopic()'>Decision</button>

                        </div>
                        <button class='button-12 deleteRow' role='button'>-</button>
                    </div>
                      </td>"; // Actions
                    echo "</tr>";
                    }
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