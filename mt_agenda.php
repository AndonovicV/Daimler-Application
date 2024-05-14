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
            // Fetching data from mt_agenda_list table
            $sql = "SELECT * FROM mt_agenda_list";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["agenda_id"] . "'>" . $row["agenda_name"] . "</option>";
                }
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
                    <th>Module team</th>
                    <th>Type</th>
                    <th>Responsible</th>
                    <th class="actions">Actions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result_gfts->num_rows > 0) {
                while ($row_gft = $result_gfts->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_gft["moduleteam"] . "</td>"; // Module team
                    echo "<td><strong>GFT " . $row_gft["name"] . "</strong></td>"; // Type
                    echo "<td></td>"; // Responsible - You may need to add data here based on your requirements
                    echo "<td><button class='btn btn-primary addRow'>New Row</button></td>"; // Actions
                    echo "<td></td>"; // Empty column
                    echo "</tr>";

                    // Fetch change requests based on $selected_team and $row_gft["name"]
                    $selected_team = $row_gft["moduleteam"];
                    $selected_gft = $row_gft["name"];
                    $sql_change_requests = "SELECT title FROM change_requests WHERE lead_module_team = '$selected_team' AND lead_gft = '$selected_gft'";
                    $result_change_requests = $conn->query($sql_change_requests);

                    if ($result_change_requests->num_rows > 0) {
                        while ($row_change_request = $result_change_requests->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td></td>"; // Empty column for module team
                            echo "<td>" . $row_change_request["title"] . "</td>"; // Type (title)
                            echo "<td></td>"; // Responsible - You may need to add data here based on your requirements
                            echo "<td></td>"; // No actions for individual titles
                            echo "<td></td>"; // Empty column
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No change requests for GFT " . $row_gft["name"] . "</td></tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='5'>No GFTs connected to the selected team</td></tr>";
            }
            ?>
            </tbody>
</body>


</html>

<?php
$conn->close();
?>
