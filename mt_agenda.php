<?php
include_once('navigation.php');
include 'conn.php';

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = ""; // Default value if not set
}

$selectedAgendaId = isset($_GET['agenda_id']) ? $_GET['agenda_id'] : null;
if ($selectedAgendaId) {
    $_SESSION['selected_agenda_id'] = $selectedAgendaId;
}

// Fetch GFTs connected to the selected team and order by order_value
$sql_gfts = "
    SELECT g.name, g.moduleteam, g.id, o.order_value
    FROM org_gfts_vehicle_mb g
    LEFT JOIN domm_gft_order o ON g.id = o.gft_id AND o.agenda_id = ?
    WHERE g.moduleteam = ?
    ORDER BY o.order_value IS NULL, o.order_value ASC, g.name ASC";

$stmt_gfts = $conn->prepare($sql_gfts);
$stmt_gfts->bind_param('is', $selectedAgendaId, $selected_team);
$stmt_gfts->execute();
$result_gfts = $stmt_gfts->get_result();

// PERSONAL TASK variables
$user_id = 1; // Example user ID
$sql_personal_tasks = "SELECT summary FROM domm_personal_tasks WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
$result_personal_tasks = $conn->query($sql_personal_tasks);

if ($result_personal_tasks->num_rows > 0) {
    $row = $result_personal_tasks->fetch_assoc();
    $summary = $row['summary'];
} else {
    $summary = "";
}

function generateAgendaSelect($conn, $selected_team, $selectedAgendaId)
{
    $output = '<select id="agendaSelect" data-search="true" class="form-select">';
    $output .= '<option value="">Select Agenda...</option>';

    $sql = "SELECT * FROM domm_mt_agenda_list WHERE module_team = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $selected_team);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $selected = ($row["agenda_id"] == $selectedAgendaId) ? "selected" : "";
                $output .= "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>"
                    . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                    . "</option>";
            }
        }

        $stmt->close();
    } else {
        $output .= "<option value=''>Error: " . htmlspecialchars($conn->error) . "</option>";
    }

    $output .= '</select>';
    return $output;
}

// Only for deleting the agendas
function generateDeleteAgendaSelect($conn, $selected_team)
{
    $output = '<select id="deleteAgendaSelect" data-search="true" class="form-select">';
    $output .= '<option value="">Delete Agenda...</option>';

    $sql = "SELECT * FROM domm_mt_agenda_list WHERE module_team = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $selected_team);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $output .= "<option value='" . htmlspecialchars($row["agenda_id"]) . "'>"
                    . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                    . "</option>";
            }
        }

        $stmt->close();
    } else {
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
    <!-- Link to Timepicker plugin -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> <!-- Required for Excel -->

    <!-- Custom CSS -->
    <link href="custom_css\mt_agenda.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="custom_js/mt_agenda.js"></script>
</head>

<body>
    <div class="container">
        <h1 style="color: #777" class='mt-4'>MT AGENDA</h1>
        <div class="row mb-3">
            <div class="col-md-6">
                <select id="agendaSelect" data-search="true" class="styled-select w-100" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;">
                    <option value="">Select Agenda...</option>
                    <?php
                    $sql = "SELECT * FROM domm_mt_agenda_list WHERE module_team = ?";
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
                <div style="margin-left: 99.9%; width:15%">
                    <select id="deleteAgendaSelect" data-search="true" class="styled-select" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important;">
                        <option value="" disabled selected>Delete Agenda...</option>
                        <?php
                        $sql = "SELECT * FROM domm_mt_agenda_list WHERE module_team = ?";
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
                            ele: '#deleteAgendaSelect',
                            placeholder: 'Delete Agenda...'
                        });
                    </script>
                </div>
            </div>
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
        </div>
        <div class="d-flex justify-content-between mb-3">
            <div id="filterDiv" style="width: 100%;">
                <select id="changeRequestSelect" data-search="true" multiple class="styled-select" placeholder="Filter Change Request" style="width: 100% !important; height: 200px; font-size: 16px;">
                    <option value=""disabled selected>Filter Change Request</option>
                    <?php
                    // Fetch change requests with the filter status for the selected agenda
                    $sql = "SELECT cr.title, cr.filter_checkbox, acrf.filter_active
                                FROM domm_change_requests cr
                                LEFT JOIN domm_agenda_change_request_filters acrf ON cr.ID = acrf.change_request_id AND acrf.agenda_id = ?
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
        <div class="modal fade" id="personalTaskModal" tabindex="-1" aria-labelledby="personalTaskLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="personalTaskLabel">Personal Tasks</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <textarea name="summary" id="summary" rows="16" class="text" style="width: 100%;"><?php echo htmlspecialchars($summary); ?></textarea>
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
                            <label for="agendaSelectTask">Select existing Agenda:</label>
                            <select id="agendaSelectTask" data-search="true" class="form-select">
                                <option value="">Select Agenda...</option>
                                <?php

                                $sql = "SELECT * FROM domm_mt_agenda_list WHERE module_team = ?";
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
                        <button type="button" class="btn btn-light" id="sendTaskBtn" data-bs-dismiss="modal">Send</button>
                    </div>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">Send to a new agenda</h1>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="newagendaDate" class="form-label">New Agenda Date:</label>
                                <input class="form-control datepicker" id="newagendaDate" data-date-format="yyyy/mm/dd" placeholder="yyyy/mm/dd">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-light" id="createAgendaConfirmWithTaskBtn">Create agenda with selected task</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table id="agendaTable" class="display">
    <thead>
        <tr>
            <th align="center">Order</th>
            <th align="center">Type</th>
            <th align="center">Content</th>
            <th align="center" style="width: 15%;">Responsible</th>
            <th align="center" style="width: 5%;">Start</th>
            <th align="center" style="width: 10%;">Duration</th> <!-- Adjust the width as needed -->
            <th align="center" style="width: 15%;">Actions</th> <!-- Adjust the width as needed -->
        </tr>
    </thead>
    <tbody>
        <?php
       if ($result_gfts->num_rows > 0) {
        while ($row_gft = $result_gfts->fetch_assoc()) {
            $gftId = $row_gft["id"]; // Assuming there's an ID field for GFT
            $orderValue = isset($row_gft['order_value']) ? $row_gft['order_value'] : '';
            echo "<tr id='{$gftId}'>";
            echo "<td><input type='number' name='order[" . $row_gft['id'] . "]' value='" . $orderValue . "' class='form-control order-input' data-gft-id='" . $row_gft['id'] . "' style='width: 80px;'></td>";
            echo "<td style='color: #2E8B57; position: relative;'>";
            echo "<strong>GFT</strong>";
            echo "<input type='hidden' class='gft-id' value='{$gftId}'>";
            echo "</td>"; // Type
            echo "<td style='color: #2E8B57'><strong>GFT " . htmlspecialchars($row_gft["name"]) . "</strong></td>"; // Description
            echo "<td></td>"; // Responsible
            echo "<td></td>"; // Start
            echo "<td></td>"; // Duration
            echo "<td>
                    <div class='button-container'>
                        <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                        <div class='dropdown-menu'>
                            <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                            <button class='dropdown-item' onclick='addBreak(this)'>Break</button>
                        </div>
                    </div>
                  </td>"; // Actions
            echo "</tr>";
            
            fetchTasksAndTopicsforGFT($conn,$gftId);
            $selected_team = $row_gft["moduleteam"];
            $selected_gft = $row_gft["name"];
            $sql_change_requests = "SELECT cr.title,cr.ID 
                    FROM domm_change_requests cr 
                    JOIN domm_agenda_change_request_filters acrf 
                    ON cr.ID = acrf.change_request_id 
                    WHERE acrf.agenda_id = ? AND acrf.filter_active = 1 AND cr.lead_module_team = ? AND cr.lead_gft = ? AND cr.fasttrack = 'Yes'";
            $stmt = $conn->prepare($sql_change_requests);
            $stmt->bind_param('iss', $selectedAgendaId, $selected_team, $selected_gft);
            $stmt->execute();
            $result_change_requests = $stmt->get_result();
    
            if ($result_change_requests->num_rows > 0) {
                // echo "<tr>";
                // echo "<td></td>"; // Type
                // echo "<td><strong>Change requests:</strong></td>"; // Description
                // echo "<td></td>"; // Responsible
                // echo "<td></td>"; // Start
                // echo "<td></td>"; // Duration
                // echo "<td></td>"; // Actions
                // echo "</tr>";
    
                while ($row_change_request = $result_change_requests->fetch_assoc()) {
                    $changeRequestId = $row_change_request["ID"];
                    $changeRequestTitle = htmlspecialchars($row_change_request["title"]);
                    echo "<tr data-title='" . $changeRequestTitle . "'>";
                    echo "<td></td>"; // Order Input
                    echo "<td style='position: relative;'>";
                    echo "<strong>CR</strong>";
                    echo "<input type='hidden' class='change-request-id' value='{$changeRequestId}'>";
                    echo "</td>"; // Type
                    echo "<td><a href='https://www.daimlertruck.com/en.php?id={$changeRequestId}'>{$changeRequestTitle}</a></td>"; // <------- Enter CR Link here 
                    echo "<td></td>"; // Responsible
                    echo "<td></td>"; // Start
                    echo "<td></td>"; // Duration
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>
                                </div>
                                <button id='unselectFilterBtn' class='button-12 unselect' role='button'>x</button>
                            </div>
                          </td>"; // Actions
                    echo "</tr>";
    
                    // Fetch domm_topics and tasks for this change request
                    fetchTasksAndTopics($conn, $gftId, $changeRequestId);
                }
            } else {
                echo "<tr>";
                echo "<td></td>"; // Type
                echo "<td></td>"; // Order Input
                echo "<td colspan='5'></td>"; //description? //Should be: No change requests for GFT " . htmlspecialchars($row_gft["name"]) . "
                echo "<td></td>"; // Responsible
                echo "<td></td>"; // Start
                echo "<td></td>"; // Duration
                echo "<td></td>"; // Actions column is present
                echo "</tr>";
                


                // Fetch domm_topics and tasks for this GFT only
                //fetchTasksAndTopics($conn, $gftId, null);
            }
        }
    } else {
        echo "<tr>";
        echo "<td></td>"; // Type
        echo "<td></td>"; // Order Input
        echo "<td colspan='5'>No change requests for this team</td>"; //description?
        echo "<td></td>"; // Responsible
        echo "<td></td>"; // Start
        echo "<td></td>"; // Duration
        echo "<td></td>"; // Actions
        echo "</tr>";
    }
    
        // Function to fetch tasks and domm_topics
        function fetchTasksAndTopics($conn, $gft, $cr)
        {
            // Remove "title for " from the CR value if present
            $cr_stripped = $cr ? str_replace('title for ', '', $cr) : null;
            $selectedAgendaId = isset($_GET['agenda_id']) ? $_GET['agenda_id'] : null;
            
            $sql_topics = "SELECT * FROM domm_topics WHERE agenda_id = ? AND sent = 0 AND gft = ? AND (cr = ?)";
            $stmt_topics = $conn->prepare($sql_topics);
            $stmt_topics->bind_param('iss', $selectedAgendaId, $gft, $cr);
            $stmt_topics->execute();
            $result_topics = $stmt_topics->get_result();

            if ($result_topics->num_rows > 0) {
                while ($row_topic = $result_topics->fetch_assoc()) {
                    $topicId = htmlspecialchars($row_topic["id"]);
                    $start = isset($row_topic["start"]) ? htmlspecialchars($row_topic["start"]) : '';
                    $duration_value = !empty($row_topic["duration"]) ? htmlspecialchars(date('H:i', strtotime($row_topic["duration"]))) : '';
                    $duration_placeholder = empty($duration_value) ? 'minutes' : '';
            
                    echo "<tr id='{$topicId}' data-type='topic' data-id='{$topicId}'>";
                    echo "<td></td>"; // Order Input
                    echo "<td class='topic-row' style='position: relative;'>";
                    echo "<strong>Topic</strong>";
                    echo "<input type='hidden' class='topic-id' value='{$topicId}'>";
                    echo "</td>"; // Type
                    echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid #dfbaff; max-width: 200px;'>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Description
                    echo "<td class='editabletasktopic-cell' data-column='responsible' contenteditable='true' style='border: 1px solid #dfbaff;'>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
                    echo "<td class='editabletasktopic-cell' style='border: 1px solid #dfbaff;'>";
                    echo "<input type='text' class='timepicker' data-column='start' data-topic-id='{$topicId}' data-start-value='{$start}' value='{$start}' style='width: 100%;'>";
                    echo "</td>"; // Start
                    echo "<td class='editabletasktopic-cell' style='border: 1px solid #dfbaff;'>";
                    echo "<input type='text' class='duration-input' data-column='duration' data-topic-id='{$topicId}' data-duration-value='{$duration_value}' value='{$duration_value}' placeholder='{$duration_placeholder}' style='width: 100%;'>";
                    echo "</td>"; // Duration
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>
                                </div>
                                <button class='button-12 deleteRow' role='button'>-</button>
                                <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$topicId}' class='button-12 forwardTopicBtns' role='button'>→</button>
                            </div>
                          </td>"; // Actions
                    echo "</tr>";

                    fetchTasksforTopics($conn, $topicId, $selectedAgendaId, $gft, $cr_stripped);
                    
                }
            }
            
            
                      
            $sql_tasks = "SELECT * FROM domm_tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL) AND sent = 0 AND deleted = 0 AND topic_id = '' ";
            $stmt_tasks = $conn->prepare($sql_tasks);
            $stmt_tasks->bind_param('isss', $selectedAgendaId, $gft, $cr_stripped, $cr_stripped);
            $stmt_tasks->execute();
            $result_tasks = $stmt_tasks->get_result();

            if ($result_tasks->num_rows > 0) {
                while ($row_task = $result_tasks->fetch_assoc()) {
                    $taskId = $row_task["id"];
                    $isASAP = $row_task["asap"] == 1;
                    $buttonColor = $isASAP ? 'red' : 'white';
                    $datepickerVisibility = $isASAP ? 'display:none;' : 'display:block;';
                    echo "<tr id='{$taskId}' data-type='task' data-id='{$taskId}'>";
                    echo "<td></td>"; // Order Input               
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
                    echo "<td style='width: 0px;'></td>"; // Start (empty for tasks)
                    echo "<td style='width: 0px;'></td>"; // Duration (empty for tasks)
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>
                                </div>
                                <button class='button-12 deleteRow' role='button'>-</button>
                                <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$taskId}' class='button-12 forwardTaskBtns' role='button'>→</button>  
                            </div>
                        </td>"; // Actions
                    echo "</tr>";
                }
            }

            fetchBreaks($conn, $selectedAgendaId, $gft, $cr_stripped); // Fetch breaks related to the specific CR
        }

        // Function to fetch tasks and topics
        function fetchTasksforTopics($conn, $topicId, $selectedAgendaId, $gft, $cr_stripped)
        {

            $sql_tasks = "SELECT * FROM domm_tasks WHERE agenda_id = ? AND gft = ? AND (cr = ? OR ? IS NULL) AND sent = 0 AND deleted = 0 AND topic_id = ?";
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
                    echo "<td></td>"; // Order Input                  
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
                    echo "<td style='width: 0px;'></td>"; // Start (empty for tasks)
                    echo "<td style='width: 0px;'></td>"; // Duration (empty for tasks)
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>
                                </div>
                                <button class='button-12 deleteRow' role='button'>-</button>
                            </div>
                        </td>"; // Actions
                    echo "</tr>";
                }
            }
        }

        // Function to fetch tasks and topics
        function fetchTasksAndTopicsforGFT($conn, $gft)
        {
            // Remove "title for " from the CR value if present
            $selectedAgendaId = isset($_GET['agenda_id']) ? $_GET['agenda_id'] : null;
            
            $sql_topics = "SELECT * FROM domm_topics WHERE agenda_id = ? AND sent = 0 AND gft = ? AND (cr = '') ";
            $stmt_topics = $conn->prepare($sql_topics);
            $stmt_topics->bind_param('is', $selectedAgendaId, $gft);
            $stmt_topics->execute();
            $result_topics = $stmt_topics->get_result();

            if ($result_topics->num_rows > 0) {
                while ($row_topic = $result_topics->fetch_assoc()) {
                    $topicId = htmlspecialchars($row_topic["id"]);
                    $start = isset($row_topic["start"]) ? htmlspecialchars($row_topic["start"]) : '';
                    $duration_value = !empty($row_topic["duration"]) ? htmlspecialchars(date('H:i', strtotime($row_topic["duration"]))) : '';
                    $duration_placeholder = empty($duration_value) ? 'minutes' : '';
            
                    echo "<tr id='{$topicId}' data-type='topic' data-id='{$topicId}'>";
                    echo "<td></td>"; // Order Input
                    echo "<td class='topic-row' style='position: relative;'>";
                    echo "<strong>Topic</strong>";
                    echo "<input type='hidden' class='topic-id' value='{$topicId}'>";
                    echo "</td>"; // Type
                    echo "<td class='editabletasktopic-cell' contenteditable='true' style='border: 1px solid #dfbaff; max-width: 200px;'>" . htmlspecialchars($row_topic["name"]) . "</td>"; // Description
                    echo "<td class='editabletasktopic-cell' data-column='responsible' contenteditable='true' style='border: 1px solid #dfbaff;'>" . htmlspecialchars($row_topic["responsible"]) . "</td>"; // Responsible
                    echo "<td class='editabletasktopic-cell' style='border: 1px solid #dfbaff;'>";
                    echo "<input type='text' class='timepicker' data-column='start' data-topic-id='{$topicId}' data-start-value='{$start}' value='{$start}' style='width: 100%;'>";
                    echo "</td>"; // Start
                    echo "<td class='editabletasktopic-cell' style='border: 1px solid #dfbaff;'>";
                    echo "<input type='text' class='duration-input' data-column='duration' data-topic-id='{$topicId}' data-duration-value='{$duration_value}' value='{$duration_value}' placeholder='{$duration_placeholder}' style='width: 100%;'>";
                    echo "</td>"; // Duration
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>

                                </div>
                                <button class='button-12 deleteRow' role='button'>-</button>
                                <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$topicId}' class='button-12 forwardTopicBtns' role='button'>→</button>
                            </div>
                          </td>"; // Actions
                    echo "</tr>";
            
                    fetchTasksforTopics($conn, $topicId, $selectedAgendaId, $gft, "");
                }
            }
            
            
                      
            $sql_tasks = "SELECT * FROM domm_tasks WHERE agenda_id = ? AND gft = ? AND (cr = '') AND sent = 0 AND deleted = 0 AND topic_id= '' ";
            $stmt_tasks = $conn->prepare($sql_tasks);
            $stmt_tasks->bind_param('is', $selectedAgendaId, $gft);
            $stmt_tasks->execute();
            $result_tasks = $stmt_tasks->get_result();

            if ($result_tasks->num_rows > 0) {
                while ($row_task = $result_tasks->fetch_assoc()) {
                    $taskId = $row_task["id"];
                    $isASAP = $row_task["asap"] == 1;
                    $buttonColor = $isASAP ? 'red' : 'white';
                    $datepickerVisibility = $isASAP ? 'display:none;' : 'display:block;';
                    echo "<tr id='{$taskId}' data-type='task' data-id='{$taskId}'>";
                    echo "<td></td>"; // Order Input               
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
                    echo "<td style='width: 0px !important;'></td>"; // Start (empty for tasks)
                    echo "<td style='width: 0px !important'></td>"; // Duration (empty for tasks)
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>

                                </div>
                                <button class='button-12 deleteRow' role='button'>-</button>
                                <button data-bs-toggle='modal' data-bs-target='#forwardModal' data-id='{$taskId}' class='button-12 forwardTaskBtns' role='button'>→</button>  
                            </div>
                        </td>"; // Actions
                    echo "</tr>";
                }
            }

            fetchBreaks($conn, $selectedAgendaId, $gft, ''); // Fetch breaks related to the specific GFT
        }
        
        // Fetch Breaks
        function fetchBreaks($conn, $agendaId, $gft, $cr) {
            $sql_breaks = "SELECT * FROM domm_breaks WHERE agenda_id = ? AND gft = ? AND cr = ? AND deleted = 0";
            $stmt_breaks = $conn->prepare($sql_breaks);
            $stmt_breaks->bind_param('iss', $agendaId, $gft, $cr);
            $stmt_breaks->execute();
            $result_breaks = $stmt_breaks->get_result();
        
            if ($result_breaks->num_rows > 0) {
                while ($row_break = $result_breaks->fetch_assoc()) {
                    $breakId = $row_break["id"];
                    $duration = isset($row_break["duration"]) ? htmlspecialchars(date('H:i', strtotime($row_break["duration"]))) : '00:00';
                    echo "<tr id='{$row_break["id"]}' data-type='break' data-id='{$row_break["id"]}'>";
                    echo "<td></td>"; // Order Input                
                    echo "<td class='break-row' style='position: relative;'>";
                    echo "<strong>Break</strong>";
                    echo "<input type='hidden' class='break-id' value='{$breakId}'>";
                    echo "</td>"; // Type    
                    echo "<td></td>"; // Description
                    echo "<td></td>"; // Responsible
                    echo "<td></td>"; // Start
                    echo "<td class='editabletasktopic-cell' style='border: 1px solid #00FFFF;'>";
                    echo "<input type='text' class='duration-input' data-column='duration' data-break-id='{$row_break["id"]}'  data-duration-value='{$duration}' value='{$duration}' placeholder='minutes' style='width: 100%;'>";
                    echo "</td>"; // Duration
                    echo "<td>
                            <div class='button-container'>
                                <button class='button-12 dropdown-toggle' onclick='toggleDropdown(this)'>+</button>
                                <div class='dropdown-menu'>
                                    <button class='dropdown-item' onclick='addTask(this)'>Task</button>
                                    <button class='dropdown-item' onclick='addTopic(this)'>Topic</button>
                                    <button class='dropdown-item' onclick='addBreak(this)'>Break</button>
                                </div>
                                <button class='button-12 deleteRow' role='button'>-</button>
                            </div>
                          </td>"; // Actions
                    echo "</tr>";
                }
            }
        }
        ?>
    </tbody>
</table>
</body>

</html>
<?php
$conn->close();
?>
