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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="plugins\bootstrap-5.3.3-dist\css\bootstrap.min.css" rel="stylesheet">
    <script src="plugins\bootstrap-5.3.3-dist\js\bootstrap.min.js"></script>
    <link rel="stylesheet" href="plugins\datatables\datatables.min.css">
    <script src="plugins\datatables\datatables.min.js"></script>

    <!-- Custom JS -->
    <script src="custom_js/mt_agenda.js"></script>
</head>

<body>
<table id="agendaTable" class="display">
<thead>
<style>
/* Style for editable cells */
td[contenteditable="true"] {
    border: 1px solid #ccc; /* Add border */
    padding: 5px; /* Add padding */
    border-radius: 3px; /* Add border radius */
    background-color: #fff; /* Set background color */
}

td[contenteditable="true"]:focus {
    outline: none; /* Remove outline on focus */
    border-color: #007bff; /* Change border color on focus */
}
</style>
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
        <tbody>
            <?php
            
            $sql = "SELECT * FROM mt_agenda";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='" . $row["id"] . "'>"; 
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["gft"] . "</td>";
                    echo "<td>" . $row["topic"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>" . $row["change_request"] . "</td>";
                    echo "<td>" . $row["task"] . "</td>";
                    echo "<td>" . $row["comment"] . "</td>";
                    echo "<td>" . $row["milestone"] . "</td>";
                    echo "<td>" . $row["responsible"] . "</td>";
                    echo "<td>" . $row["start"] . "</td>";
                    echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
                    echo "<td><button class='btn btn-danger deleteRow'>Delete Row</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "0 results";
            }
            ?>
        </tbody>
</table>