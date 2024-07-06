<?php
include_once('navigation.php');
include 'conn.php';

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = ""; // Default value if not set
}

$selectedAgendaId = isset($_GET['protokol_id']) ? $_GET['protokol_id'] : null;
if ($selectedAgendaId) {
    $_SESSION['selected_agenda_id'] = $selectedAgendaId;
} else {
    $selectedAgendaId = isset($_SESSION['selected_agenda_id']) ? $_SESSION['selected_agenda_id'] : null;
}

// Fetch GFTs connected to the selected team
// Fetch GFTs connected to the selected team and order by order_value
$sql_gfts = "
    SELECT g.name, g.moduleteam, g.id, o.order_value
    FROM org_gfts g
    LEFT JOIN gft_order o ON g.id = o.gft_id AND o.agenda_id = ?
    WHERE g.moduleteam = ?
    ORDER BY o.order_value IS NULL, o.order_value ASC, g.name ASC";

$stmt_gfts = $conn->prepare($sql_gfts);
$stmt_gfts->bind_param('is', $selectedAgendaId, $selected_team);
$stmt_gfts->execute();
$result_gfts = $stmt_gfts->get_result();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> <!-- Required for Excel -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('.datepicker', {
                dateFormat: 'Y-m-d',
                // Add any additional options here
            });
        });
    </script>
    <!-- Custom CSS -->
    <link href="custom_css\protokol.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="custom_js/protokol.js"></script>
    <script src="custom_js/protokolspecific.js"></script>

</head>

<body>

    <div class="container">
        <div class="container mt-4" style="color: #fff;">
            <h1 style="color: #777" class='mt-4'>PROTOKOLL</h1>
            <div class="row mb-3">
                <div class="col-md-6">
                    <select id="protokolSelect" data-search="true" class="styled-select w-100 mb-3" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;">
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
                                        . htmlspecialchars($row["agenda_name"])
                                        . "</option>";
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
                    <script>
                        VirtualSelect.init({
                            ele: '#protokolSelect'
                        });
                    </script>
                </div>
                <div class="col-md-6 d-flex justify-content-end" style="margin-left: 99.9%; width:20%">
                    <select id="deleteProtokolSelect" data-search="true" class="styled-select" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important;">
                        <option value="">Delete Protocol...</option>
                        <?php
                        $sql = "SELECT * FROM mt_agenda_list WHERE module_team = ?";
                        $stmt = $conn->prepare($sql);

                        if ($stmt) {
                            $stmt->bind_param('s', $selected_team);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "'>" . htmlspecialchars($row["agenda_name"]) . "</option>";
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
                            multiple: true,
                            ele: '#deleteProtokolSelect',
                            placeholder: 'Delete Protocol...'
                        });
                    </script>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <button type="button" class="btn btn-light flex-fill mx-1" data-bs-toggle="modal" data-bs-target="#personalTaskModal" id="modalBtn" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
                        Personal Task
                    </button>

                    <button type="button" class="button-12 addRow flex-fill mx-1" onclick="window.location.href = 'attendance.php?agenda_id=<?php echo $selectedAgendaId; ?>'" style="background-color: #333 !important; color: #fff !important; border-color: #444 !important;">
                        To Attendance
                    </button>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div id="filterDiv" style="width: 100%;">
                        <select id="changeRequestSelect" data-search="true" placeholder = "Filter Change Request" multiple class="styled-select" style="width: 100% !important; height: 200px; font-size: 16px;">
                            <option value="">Filter Change Request</option>
                            <?php
                            // Fetch change requests with the filter status for the selected protokol
                            $sql = "SELECT cr.title, cr.filter_checkbox, acrf.filter_active
                                FROM change_requests cr
                                LEFT JOIN agenda_change_request_filters acrf ON cr.ID = acrf.change_request_id AND acrf.agenda_id = ?
                                WHERE cr.lead_module_team = ? AND cr.fasttrack = 'Yes'";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('is', $selectedAgendaId, $selected_team);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                // Check the filter status
                                $selected = ($row['filter_active']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['title']) . '" ' . $selected . '>' . htmlspecialchars($row['title']) . '</option>';
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
            <?php
            if (isset($protokol_date)) {
                echo "<h3 class='mt-4'>Agenda Date: $protokol_date</h3>";
            }
            ?>
        </div>
        <table id="protokolTable" class="display">
            <thead>
                <tr>
                    <th align="center">Type</th>
                    <th align="center">Description</th> <!--GFT/Change Request/Task description -->
                    <th align="center">Responsible</th>
                    <th align="center" class="actions"  style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_gfts->num_rows > 0) {
                    while ($row_gft = $result_gfts->fetch_assoc()) {
                        $gftId = $row_gft["id"]; // Assuming there's an ID field for GFT
                        echo "<tr id='{$gftId}'>";
                        echo "<td style='color: #2E8B57; position: relative;'>";
                        echo "<strong>GFT</strong>";
                        echo "<input type='hidden' class='gft-id' value='{$gftId}'>";
                        echo "</td>"; // Type                        
                        echo "<td style='color: #2E8B57'><strong>GFT " . $row_gft["name"] . "</strong></td>"; // GFT
                        echo "<td></td>"; // Responsible 

                        echo "<td>
                                <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                </div>
                            </div>
                                </div>
                            </td>"; // Actions
                        echo "</tr>";

                        fetchTopicsforGFT($conn, $row_gft["id"], null);


                        // Fetch change requests based on $selected_team and $row_gft["name"]
                        $selected_team = $row_gft["moduleteam"];
                        $selected_gft = $row_gft["name"];
                        $sql_change_requests = "SELECT cr.title,cr.ID 
                        FROM change_requests cr 
                        JOIN agenda_change_request_filters acrf 
                        ON cr.ID = acrf.change_request_id 
                        WHERE acrf.agenda_id = ? AND acrf.filter_active = 1 AND cr.lead_module_team = ? AND cr.lead_gft = ? AND cr.fasttrack = 'Yes'";
                        $stmt = $conn->prepare($sql_change_requests);
                        $stmt->bind_param('iss', $selectedAgendaId, $selected_team, $selected_gft);
                        $stmt->execute();
                        $result_change_requests = $stmt->get_result();

                        if ($result_change_requests->num_rows > 0) {
                            // echo "<tr>";
                            // echo "<td></td>"; // Type
                            // echo "<td><strong>Change requests:</strong></td>"; // Change Request
                            // echo "<td></td>"; // Responsible
                            // echo "<td></td>"; // Actions
                            // echo "</tr>";
                            while ($row_change_request = $result_change_requests->fetch_assoc()) {
                                $changeRequestId = $row_change_request["ID"];
                                echo "<tr data-title='" . htmlspecialchars($row_change_request["title"]) . "'>";
                                echo "<td style='position: relative;'>";
                                echo "<strong>CH</strong>";
                                echo "<input type='hidden' class='change-request-id' value='{$changeRequestId}'>";
                                echo "</td>"; // Type
                                echo "<td>" . $row_change_request["title"] . "</a></td>"; // Change Request
                                echo "<td></td>"; // Responsible

                                echo "<td>
                                    <div class='button-container'>
                                    <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                    <div class='dropdown-menu'>
                                        <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                        <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    </div>
                                        <button id='unselectFilterBtn' class='button-12 unselect' role='button'>x</button>
                                </div>
                                </td>"; // Actions
                                echo "</tr>";

                                // Fetch topics and tasks for this change request
                                fetchTasksAndTopics($conn, $row_gft["id"], $row_change_request["ID"]);
                            }
                        } else {
                            echo "<tr>";
                            echo "<td></td>"; // Empty column for module team
                            echo "<td colspan='5'>No change requests for GFT " . $row_gft["name"] . "</td>";
                            echo "<td></td>"; // Responsible - You may need to add data here based on your requirements
                            echo "<td></td>"; // Empty column
                            echo "</tr>";

                            // Fetch topics and tasks for this GFT only
                            //fetchTasksAndTopics($conn, $row_gft["id"], null);
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
                            $topicId = $row_topic["id"];
                            echo "<tr id='topic-{$row_topic["id"]}' data-type='topic' data-id='{$row_topic["id"]}'>";
                            echo "<td class='topic-row' style='position: relative;'>";
                            echo "<strong>Topic</strong>";
                            echo "<input type='hidden' class='topic-id' value='{$topicId}'>";
                            echo "</td>"; // Type                              
                            echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid #dfbaff;'>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Type
                            echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid #dfbaff;'>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
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

                            fetchTasksforTopics($conn, $topicId, $selectedAgendaId, $gft, $cr_stripped);
                        }
                    }

                    $sql_tasks = "SELECT * FROM tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL) AND topic_id = ''";
                    $stmt_tasks = $conn->prepare($sql_tasks);
                    $stmt_tasks->bind_param('isss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped);
                    $stmt_tasks->execute();
                    $result_tasks = $stmt_tasks->get_result();

                    if ($result_tasks->num_rows > 0) {
                        while ($row_task = $result_tasks->fetch_assoc()) {

                            if ($row_task["deleted"] == "0") {
                                $taskId = $row_task["id"];
                                $isASAP = $row_task["asap"] == 1;
                                $buttonColor = $isASAP ? 'red' : 'white';
                                $datepickerVisibility = $isASAP ? 'display:none;' : 'display:block;';
                                echo "<tr id='{$taskId}' data-type='task' data-id='{$taskId}'>";
                                echo "<td class='task-row' style='position: relative;'>";
                                echo "<strong>Task</strong>";
                                echo "<input type='hidden' class='task-id' value='{$taskId}'>";
                                echo "</td>"; // Type                                
                                echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid orange; max-width: 200px;'>" . htmlspecialchars($row_task["name"]) . "</td>";
                                echo "<td style='background-color: #212529 !important; width: 100px !important;'>"; // Apply background color and minimum width
                                echo "<input class='editabletasktopic-cell' data-column='responsible' type='text' style='background-color: #212529 !important; border: 1px solid orange; width: 100%;' value='" . htmlspecialchars($row_task["responsible"]) . "'>"; // Adjust width to fill the container
                                echo "<br>";
                                echo "<br>";
                                echo "<input class='editabletasktopic-cell datepicker' data-column='deadline' type='text' id='datepicker-{$taskId}' style='color: white !important; border: 1px solid orange; width: 70%; {$datepickerVisibility}' value='" . htmlspecialchars($row_task["deadline"]) . "'>"; // Use an ID for the input field
                                echo "<button class='asap-button' data-task-id='{$taskId}' style='color: {$buttonColor};'>ASAP</button>";
                                echo "</td>";
                                echo "<td>
                                        <div class='button-container'>
                                            <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                            <div class='dropdown-menu'>
                                                <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                                <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                                <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                                                <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                                                <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                                            </div>
                                            <button class='button-12 deleteRow' role='button'>-</button>
                                            <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$taskId}' class='button-12 forwardTaskBtns' role='button'>→</button>  
                                        </div>
                                      </td>"; // Actions
                                echo "</tr>";
                            }
                            // Load rows from information table
                            $sql_information = "SELECT * FROM information WHERE task_id = ?";
                            $stmt_information = $conn->prepare($sql_information);
                            $stmt_information->bind_param('i', $row_task["id"]);
                            $stmt_information->execute();
                            $result_information = $stmt_information->get_result();

                            while ($row_information = $result_information->fetch_assoc()) {
                                echo "<tr id='{$row_information["id"]}' data-type='I' data-id='{$row_information["id"]}'>";
                                echo "<td><strong>I</strong></td>"; // Empty column for module team
                                echo "<td class='editable-cell' data-field='content' contenteditable='true'>"  .  htmlspecialchars($row_information["content"]) . "</td>"; // Content
                                echo "<td></td>"; // Responsible
                                echo "<td>
                        <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                            <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                            <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                        </div>
                        <button class='button-12 deleteIADRow' role='button'>-</button>                        
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
                                echo "<tr id='{$row_assignment["id"]}' data-type='A' data-id='{$row_assignment["id"]}'>";
                                echo "<td><strong>A</strong></td>"; // Empty column for module team
                                echo "<td class='editable-cell' data-field='content' contenteditable='true'>" .  htmlspecialchars($row_assignment["content"]) . "</td>"; // Content
                                echo "<td class='editable-cell' data-field='responsible' contenteditable='true'>" .  htmlspecialchars($row_assignment["responsible"]) . "</td>"; // Content
                                echo "<td>
                        <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                            <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                            <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                        </div>
                        <button class='button-12 deleteIADRow' role='button'>-</button>
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
                                echo "<tr id='{$row_decision["id"]}' data-type='D' data-id='{$row_decision["id"]}'>";
                                echo "<td><strong>D</strong></td>"; // Empty column for module team
                                echo "<td class='editable-cell' data-field='content' contenteditable='true'>" . htmlspecialchars($row_decision["content"]) . "</td>"; // Content
                                echo "<td></td>"; // Responsible
                                echo "<td>
                        <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                            <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                            <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>

                        </div>
                        <button class='button-12 deleteIADRow' role='button'>-</button>
                    </div>
                      </td>"; // Actions
                                echo "</tr>";
                            }
                        }
                    }
                     // Function to fetch tasks and topics
                }
                function fetchTasksforTopics($conn, $topicId, $selectedAgendaId, $gft, $cr_stripped)
                {
    
                    $sql_tasks = "SELECT * FROM tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL) AND sent = 0 AND deleted = 0 AND topic_id = ?";
                    $stmt_tasks = $conn->prepare($sql_tasks);
                    $stmt_tasks->bind_param('issss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped, $topicId);
                    $stmt_tasks->execute();
                    $result_tasks = $stmt_tasks->get_result();
    
                    if ($result_tasks->num_rows > 0) {
                        while ($row_task = $result_tasks->fetch_assoc()) {
                            $taskId = $row_task["id"];
                            $isASAP = $row_task["asap"] == 1;
                            $buttonColor = $isASAP ? 'red' : 'white';
                            $datepickerVisibility = $isASAP ? 'display:none;' : 'display:block;';
                            echo "<tr id='{$taskId}' data-type='task' data-id='{$taskId}'>";
                            echo "<td class='task-row' style='position: relative;'>";
                            echo "<strong>Task</strong>";
                            echo "<input type='hidden' class='task-id' value='{$taskId}'>";
                            echo "</td>"; // Type                    
                            echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid orange; max-width: 200px;'>" . htmlspecialchars($row_task["name"]) . "</td>"; // Description
                            echo "<td style='background-color: #212529 !important; width: 300px !important;'>"; // Responsible
                            echo "<input class='editabletasktopic-cell' data-column='responsible' type='text' style='background-color: #212529 !important; border: 1px solid orange; width: 100%;' value='" . htmlspecialchars($row_task["responsible"]) . "'>";
                            echo "<br>";
                            echo "<br>";
                            echo "<input class='editabletasktopic-cell datepicker' data-column='deadline' type='text' id='datepicker-{$taskId}' style='color: white !important; border: 1px solid orange; width: 70%; {$datepickerVisibility}' value='" . htmlspecialchars($row_task["deadline"]) . "'>";
                            echo "<button class='asap-button' data-task-id='{$taskId}' style='color: {$buttonColor};'>ASAP</button>";
                            echo "</td>";
                            echo "<td>
                                    <div class='button-container'>
                                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                        <div class='dropdown-menu'>
                                            <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                        </div>
                                        <button class='button-12 deleteRow' role='button'>-</button>
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
                                 echo "<tr id='{$row_information["id"]}' data-type='I' data-id='{$row_information["id"]}'>";
                                 echo "<td><strong>I</strong></td>"; // Empty column for module team
                                 echo "<td class='editable-cell' data-field='content' contenteditable='true'>"  .  htmlspecialchars($row_information["content"]) . "</td>"; // Content
                                 echo "<td></td>"; // Responsible
                                 echo "<td>
                         <div class='button-container'>
                         <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                         <div class='dropdown-menu'>
                             <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                             <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                             <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                             <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                             <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                         </div>
                         <button class='button-12 deleteIADRow' role='button'>-</button>                        
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
                                 echo "<tr id='{$row_assignment["id"]}' data-type='A' data-id='{$row_assignment["id"]}'>";
                                 echo "<td><strong>A</strong></td>"; // Empty column for module team
                                 echo "<td class='editable-cell' data-field='content' contenteditable='true'>" .  htmlspecialchars($row_assignment["content"]) . "</td>"; // Content
                                 echo "<td class='editable-cell' data-field='responsible' contenteditable='true'>" .  htmlspecialchars($row_assignment["responsible"]) . "</td>"; // Content
                                 echo "<td>
                         <div class='button-container'>
                         <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                         <div class='dropdown-menu'>
                             <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                             <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                             <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                             <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                             <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                         </div>
                         <button class='button-12 deleteIADRow' role='button'>-</button>
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
                                 echo "<tr id='{$row_decision["id"]}' data-type='D' data-id='{$row_decision["id"]}'>";
                                 echo "<td><strong>D</strong></td>"; // Empty column for module team
                                 echo "<td class='editable-cell' data-field='content' contenteditable='true'>" . htmlspecialchars($row_decision["content"]) . "</td>"; // Content
                                 echo "<td></td>"; // Responsible
                                 echo "<td>
                         <div class='button-container'>
                         <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                         <div class='dropdown-menu'>
                             <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                             <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                             <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                             <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                             <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
 
                         </div>
                         <button class='button-12 deleteIADRow' role='button'>-</button>
                     </div>
                       </td>"; // Actions
                                 echo "</tr>";
                             }                       
                    }
                    }
                }
                // Function to fetch tasks and topics
                function fetchTopicsforGFT($conn, $gft, $cr)
                {
                    // Remove "title for " from the CR value if present
                    $cr_stripped = $cr ? str_replace('title for ', '', $cr) : null;
                    $selectedAgendaId = isset($_GET['protokol_id']) ? $_GET['protokol_id'] : null;
                    // Debugging output
                    //echo "<tr><td colspan='5'>Fetching Topics and Tasks for GFT: " . htmlspecialchars($gft) . " and CR: " . htmlspecialchars($cr_stripped) . "</td></tr>";

                    $sql_topics = "SELECT * FROM topics WHERE agenda_id = ? AND gft = ? AND (cr = '')";
                    $stmt_topics = $conn->prepare($sql_topics);
                    $stmt_topics->bind_param('is', $selectedAgendaId, $gft);
                    $stmt_topics->execute();

                    $result_topics = $stmt_topics->get_result();
                    if ($result_topics->num_rows > 0) {
                        while ($row_topic = $result_topics->fetch_assoc()) {
                            $topicId = $row_topic["id"];
                            echo "<tr id='topic-{$row_topic["id"]}' data-type='topic' data-id='{$row_topic["id"]}'>";
                            echo "<td class='topic-row' style='position: relative;'>";
                            echo "<strong>Topic</strong>";
                            echo "<input type='hidden' class='topic-id' value='{$topicId}'>";
                            echo "</td>"; // Type                              
                            echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid #dfbaff;'>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Type
                            echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid #dfbaff;'>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
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

                            fetchTasksforTopics($conn, $topicId, $selectedAgendaId, $gft, $cr_stripped);
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