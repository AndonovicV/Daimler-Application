<?php 
include 'C:\xampp\htdocs\Daimler\conn.php';

// Fetch all module teams
$sql_module_teams = "SELECT * FROM org_moduleteams";
$result_module_teams = $conn->query($sql_module_teams);

// Fetch all GFTs
$sql_gfts = "SELECT * FROM org_gfts";
$result_gfts = $conn->query($sql_gfts);

// Fetch projects for each GFT
$sql_spec_book = "SELECT GFT, Project FROM spec_book";
$result_spec_book = $conn->query($sql_spec_book);
$gft_projects = array();

while ($row_spec_book = $result_spec_book->fetch_assoc()) {
    $gft_projects[$row_spec_book['GFT']][] = $row_spec_book['Project'];
}
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

<!-- Dropdown list of module teams -->
<select id="moduleTeamSelect" onchange="filterGFTs()">
    <option value="">Select Module Team</option>
    <?php
    while ($row_module_team = $result_module_teams->fetch_assoc()) {
        echo "<option value='" . $row_module_team["name"] . "'>" . $row_module_team["name"] . "</option>";
    }
    ?>
</select>

<table id="agendaTable" class="display">
    <thead>
        <tr>
            <th>Module team</th>
            <th>Type</th>
            <th>New Row</th>
            <th>Delete Row</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row_gft = $result_gfts->fetch_assoc()) {
            echo "<tr class='gftRow' data-moduleteam='" . $row_gft["moduleteam"] . "'>"; 
            echo "<td>" . $row_gft["moduleteam"] . "</td>";
            echo "<td><strong>GFT " . $row_gft["name"] . "</td>";
            echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
            echo "<td></td>";
            echo "</tr>";
            if (isset($gft_projects[$row_gft["name"]])) {
                foreach ($gft_projects[$row_gft["name"]] as $project) {
                    echo "<tr class='projectRow' data-moduleteam='" . $row_gft["moduleteam"] . "' style='display:none;'>";
                    echo "<td></td>"; 
                    echo "<td colspan='1'>Project $project</td>"; 
                    echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
                    echo "<td><button class='btn btn-danger deleteRow'>Delete Row</button></td>";
                    echo "</tr>";
                }
            }
        }
        ?>
    </tbody>
</table>

<script>
function filterRows(moduleTeam) {
    var gftRows = document.getElementsByClassName("gftRow");
    var projectRows = document.getElementsByClassName("projectRow");
    
    for (var i = 0; i < gftRows.length; i++) {
        if (moduleTeam === "" || gftRows[i].getAttribute("data-moduleteam") === moduleTeam) {
            gftRows[i].style.display = "table-row";
        } else {
            gftRows[i].style.display = "none";
        }
    }
    
    for (var j = 0; j < projectRows.length; j++) {
        if (moduleTeam === "" || projectRows[j].getAttribute("data-moduleteam") === moduleTeam) {
            projectRows[j].style.display = "table-row";
        } else {
            projectRows[j].style.display = "none";
        }
    }
}

document.getElementById("moduleTeamSelect").addEventListener("change", function() {
    var selectedModuleTeam = this.value;
    filterRows(selectedModuleTeam);
});
</script>

</body>
</html>
