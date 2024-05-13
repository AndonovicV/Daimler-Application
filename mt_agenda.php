<?php
include_once('php-attendance\inc\navigationAgenda.php');
include 'conn.php';

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
    //echo($selected_team);
} else {
    $selected_team = ""; // Default value if not set
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> <!--CDN Link, Local doesnt work -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> <!--CDN Link, Local doesnt work -->
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
        <!-- Data table -->
        <div class="row">
            <div class="col-md-12">
                <table id="agendaTable" class="display" style="display: none;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>GFT</th>
                            <th>Topic</th>
                            <th>Status</th>
                            <th>Change Request</th>
                            <th>Task</th>
                            <th>Comment</th>
                            <th>Milestone</th>
                            <th>Responsible</th>
                            <th>Start</th>
                            <th>New</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="agendaTableBody">
                        <!--Datatable body loaded here-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    // Fetch all GFTs and their corresponding Change Requests for Module Team A
    $sql = "SELECT GFT, Change_Request FROM mt_agenda_test_2 WHERE md_team = 'Module Team A'";
    $result = mysqli_query($conn, $sql);

    // Initialize variables
    $currentGFT = null;
    ?>
    <table id="mt_agenda_test2">
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <?php if ($row['GFT'] !== $currentGFT) { ?>
                    <?php if ($currentGFT !== null) { ?>
                        </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><?php echo $row['GFT']; ?></td>
                    </tr>
                    <tr>
                        <td></td> <!--For visual Space -->
                        <td><?php echo $row['Change_Request']; ?></td>
                    </tr>
                    <?php $currentGFT = $row['GFT']; ?>
                <?php } else { ?>
                    <tr>
                        <td></td> <!--For visual Space -->
                        <td><?php echo $row['Change_Request']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>

<?php
$conn->close();
?>