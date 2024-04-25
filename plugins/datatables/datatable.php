<?php 
include 'C:\xampp\htdocs\Daimler\conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datatable</title>
    <!-- Libraries -->
    <link href="..\bootstrap-5.3.3-dist\css\bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="..\bootstrap-5.3.3-dist\js\bootstrap.min.js"></script>
    <link rel="stylesheet" href="datatables.min.css">
    <script src="datatables.min.js"></script>
    <!-- Custom JS -->
    <script src="datatable.js"></script>
</head>
<body>

<table id="agendaTable" class="display">
<thead>
            <tr>
                <th>ID</th>
                <th>New Row</th>
                <th>GFT</th>
                <th>Topic</th>
                <th>Status</th>
                <th>Change Request</th>
                <th>Task</th>
                <th>Comment</th>
                <th>Milestone</th>
                <th>Responsible</th>
                <th>Start</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            $sql = "SELECT * FROM mt_agenda";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td class='addRow' id='addRowCell'>". $row["add_row"] . "</td>";
                    echo "<td>" . $row["gft"] . "</td>";
                    echo "<td>" . $row["topic"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>" . $row["change_request"] . "</td>";
                    echo "<td>" . $row["task"] . "</td>";
                    echo "<td>" . $row["comment"] . "</td>";
                    echo "<td>" . $row["milestone"] . "</td>";
                    echo "<td>" . $row["responsible"] . "</td>";
                    echo "<td>" . $row["start"] . "</td>";
                    echo "<td><button type='button' class='btn btn-danger btn-sm icon-delete'><i class='bi bi-trash'></i></button></td>";
                    echo "</tr>";
                }
            } else {
                echo "0 results";
            }
            ?>
        </tbody>
</table>