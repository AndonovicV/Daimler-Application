<?php
include 'conn.php';

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
    //echo($selected_team);
} else {
    $selected_team = ""; // Default value if not set
}


// Insert new row (Table) into mt_agenda_list table
if (isset($_POST['agenda_name'], $_POST['agenda_date']) && !empty($_POST['agenda_name']) && !empty($_POST['agenda_date'])) {
    $agendaName = $_POST['agenda_name'];
    $agendaDate = $_POST['agenda_date'];

    // Fetch the maximum item_id from mt_agenda table
    $maxItemIdQuery = "SELECT MAX(item_id) AS max_item_id FROM mt_agenda";
    $maxItemIdResult = $conn->query($maxItemIdQuery);
    if ($maxItemIdResult->num_rows > 0) {
        $row = $maxItemIdResult->fetch_assoc();
        $nextItemId = $row['max_item_id'] + 1;
    } else {
        $nextItemId = 1; // If no rows found, start from 1
    }

    // Insert new row into mt_agenda_list table
    $insertSql = "INSERT INTO mt_agenda_list (agenda_name, agenda_date, module_team) VALUES ('$agendaName', '$agendaDate', '$selected_team')";
    if ($conn->query($insertSql) === TRUE) {
        // Retrieve the auto-generated agenda_id
        $agendaId = $conn->insert_id;
        echo $agendaId; // Return the newly generated agenda_id
}
}
?>