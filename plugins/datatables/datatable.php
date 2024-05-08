<?php 
include 'C:\xampp\htdocs\Daimler\conn.php';

// Fetch all module teams
$sql_module_teams = "SELECT DISTINCT lead_module_team FROM package";
$result_module_teams = $conn->query($sql_module_teams);

// Fetch all GFTs
$sql_gfts = "SELECT DISTINCT lead_gft as name, lead_module_team as moduleteam FROM package";
$result_gfts = $conn->query($sql_gfts);

// Fetch projects for each GFT
$sql_spec_book = "SELECT GFT, Project FROM spec_book";
$result_spec_book = $conn->query($sql_spec_book);
$gft_projects = array();

while ($row_spec_book = $result_spec_book->fetch_assoc()) {
    $gft_projects[$row_spec_book['GFT']][] = $row_spec_book['Project'];
}

// Fetch tasks
$sql_tasks = "SELECT * FROM tasks";
$result_tasks = $conn->query($sql_tasks);

// Fetch topics
$sql_topics = "SELECT * FROM topics";
$result_topics = $conn->query($sql_topics);
?>

// Fetch titles for each project
$sql_titles = "SELECT project, title FROM package";
$result_titles = $conn->query($sql_titles);
$project_titles = array();

while ($row_titles = $result_titles->fetch_assoc()) {
    $project_titles[$row_titles['project']][] = $row_titles['title'];
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
    <link rel="stylesheet" href="datatables.css">
    <script src="datatables.min.js"></script>
    <!-- Custom JS -->
    <script src="datatable.js"></script>
</head>
<body>
<style>
    #agendaTable th.actions {
        text-align: center; /* Align the content in the center */
        column-span: 2; /* Span the header across two columns */
    }

    #agendaTable {
        margin: 0 auto !important; /* Center the table */
        width: 50% !important; /* Set the table width */
    }

    th,
    td {
        padding: 8px; /* Adjust cell padding */
    }

    .addRow,
    .deleteRow {
        padding: 4px 8px; /* Adjust button padding */
    }

        /* Center and style search box */
    .dataTables_filter input {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        display: block;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    /* Center select module team dropdown */
    #moduleTeamSelect {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        display: block;
        padding: 8px;
        border-radius: 5px;
        border: 2px solid #ccc;
    }

    .contenteditable {
        border-radius: 5px;
        border: 2px solid #ccc;
    }

</style>

<!-- Dropdown list of module teams -->
<select id="moduleTeamSelect" onchange="filterGFTs()" style="text-align: center;">
    <option value="">Select Module Team</option>
    <?php
    while ($row_module_team = $result_module_teams->fetch_assoc()) {
        echo "<option value='" . $row_module_team["lead_module_team"] . "'>" . $row_module_team["lead_module_team"] . "</option>";
    }
    ?>
</select>


<table id="agendaTable" class="display">
    <thead>
        <tr>
            <th>Module team</th>
            <th >Type</th>
            <th>Responsible</th>
            <th class="actions">Actions</th>
            <th></th>
        </tr>
    </thead>
    <tbody>        
        <?php
        while ($row_gft = $result_gfts->fetch_assoc()) {
            echo "<tr class='gftRow' data-moduleteam='" . $row_gft["moduleteam"] . "'>"; 
            echo "<td>" . $row_gft["moduleteam"] . "</td>";
            echo "<td><strong>GFT " . $row_gft["name"] . "</td>";
            echo "<td></td>";
            echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
            echo "<td></td>";
            echo "</tr>";
            
            if (isset($gft_projects[$row_gft["name"]])) {
                foreach ($gft_projects[$row_gft["name"]] as $project) {
                    echo "<tr class='projectRow' data-moduleteam='" . $row_gft["moduleteam"] . "' style='display:none;'>";
                    echo "<td></td>"; 
                    echo "<td colspan='1'>Project $project</td>";
                    echo "<td></td>";
                    echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }
            }
        
            if (isset($project_titles[$row_gft["name"]])) {
                // Debugging: Print the current GFT name
                echo "<script>console.log('Current GFT: " . $row_gft["name"] . "');</script>";
                
                foreach ($project_titles[$row_gft["name"]] as $title) {
                    // Debugging: Print the current title
                    echo "<script>console.log('Current Title: " . $title . "');</script>";
                    echo "<tr class='titleRow' data-moduleteam='" . $row_gft["moduleteam"] . "' style='display:none;'>";
                    echo "<td></td>"; 
                    echo "<td colspan='1'>$title</td>";
                    echo "<td></td>";
                    echo "<td><button class='btn btn-primary addRow'>New Row</button></td>";
                    echo "<td></td>";
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
        var titleRows = document.getElementsByClassName("titleRow");

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

        for (var k = 0; k < titleRows.length; k++) {
            if (moduleTeam === "" || titleRows[k].getAttribute("data-moduleteam") === moduleTeam) {
                titleRows[k].style.display = "table-row";
            } else {
                titleRows[k].style.display = "none";
            }
        }
    }

// Run filterRows function once when the page is loaded
document.addEventListener("DOMContentLoaded", function() {
    filterRows("");
});

// Add event listener to moduleTeamSelect for filtering rows
document.getElementById("moduleTeamSelect").addEventListener("change", function() {
    var selectedModuleTeam = this.value;
    filterRows(selectedModuleTeam);
});
</script>

</body>
</html>
