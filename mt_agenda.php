<?php 
include 'conn.php';
?>

<!DOCTYPE html>
<html lang="en">
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

    <!-- Custom JS -->
    <script src="custom_js/mt_agenda.js"></script>
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h3>Select or Create Agenda:</h3>
            <select id="agendaSelect" class="form-select mb-3">
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
                <option value="new">Create New Agenda</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table id="agendaTable" class="display">
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
                        <th>New Row</th>
                        <th>Delete Row</th>
                    </tr>
                </thead>
                <tbody id="agendaTableBody">
                    <?php
                    if (isset($_POST['agenda_id']) && $_POST['agenda_id'] != 'new') {
                        $agenda_id = $_POST['agenda_id'];
                        $sql = "SELECT * FROM mt_agenda WHERE agenda_id = $agenda_id";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr id='" . $row["item_id"] . "'>"; 
                                echo "<td>" . $row["item_id"] . "</td>";
                                echo "<td>" . $row["GFT"] . "</td>";
                                echo "<td>" . $row["Topic"] . "</td>";
                                echo "<td>" . $row["Status"] . "</td>";
                                echo "<td>" . $row["Change_Request"] . "</td>";
                                echo "<td>" . $row["Task"] . "</td>";
                                echo "<td>" . $row["Comment"] . "</td>";
                                echo "<td>" . $row["Milestone"] . "</td>";
                                echo "<td>" . $row["Responsible"] . "</td>";
                                echo "<td>" . $row["Start"] . "</td>";
                                echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
                                echo "<td><button class='btn btn-danger deleteRow'>Delete Row</button></td>";
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
