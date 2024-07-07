<?php
include 'conn.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Make sure this path is writable

// Get the search query and filter
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';

$resultsHTML = '';
$found = false;

// Define an array of tables and the columns to search
$tables = [
    'departments' => ['department_name'],
    'domm_guests' => ['guest_name'],
    'mt_agenda_list' => ['agenda_date', 'module_team', 'agenda_id'],
    'org_gfts' => ['name', 'moduleteam'],
    'tasks' => ['name', 'responsible', 'gft', 'cr', 'asap', 'deadline'],
    'topics' => ['name', 'responsible', 'gft', 'cr'],
    'domm_information' => ['agenda_id', 'content', 'gft', 'cr'],
    'domm_assignment' => ['agenda_id', 'content', 'gft', 'cr'],
    'domm_decision' => ['agenda_id', 'content', 'gft', 'cr'],
];

// Filter the tables based on the selected filter
if ($filter) {
    if (array_key_exists($filter, $tables)) {
        $tables = [$filter => $tables[$filter]];
    } else {
        $tables = [];
    }
}

foreach ($tables as $table => $columns) {
    $searchConditions = [];
    foreach ($columns as $column) {
        $searchConditions[] = "$column LIKE '%$query%'";
    }
    $sql = "SELECT * FROM $table WHERE " . implode(' OR ', $searchConditions);
    
    // Log the SQL query
    error_log("Executing query: $sql");

    $result = $conn->query($sql);
    
    // Check for query errors
    if ($result === false) {
        error_log("Error executing query: " . $conn->error);
        continue;
    }
    
    if ($result && $result->num_rows > 0) {
        // Start a new section for the table's results
        $resultsHTML .= "<div class='table-results'>";
        $resultsHTML .= "<h2>Results from Table: $table</h2>";
        
        // Fetch and display results
        while ($row = $result->fetch_assoc()) {
            // Format each row of the result as a card
            if ($table === 'tasks') {
                $resultsHTML .= "<div id = 'search-task' class='card'>";
            }
            elseif ($table === 'topics') {
                    $resultsHTML .= "<div id = 'search-topic' class='card'>";
                }
            else {
                $resultsHTML .= "<div class='card'>";
            }
            $resultsHTML .= "<div class='card-body'>";
            foreach ($row as $key => $value) {
                if ($key === 'id' ||
                    ($table === 'tasks' && in_array($key, ['details', 'deleted'])) ||
                    ($table === 'topics' && $key === 'details') ||
                    (in_array($table, ['domm_information', 'domm_assignment', 'domm_decision']) && in_array($key, ['cr', 'task_id']))
                ) {
                    continue; // Skip the specified keys
                }
                $found = true;
                // Customize the formatting of each key-value pair
                if ($key === 'cr') {
                    // Make CR clickable links
                    $value = "<a href='cr.php?agenda_id=$value'>$value</a>";
                }
                if ($key === 'agenda_id') {
                    // Make agenda IDs clickable links
                    $value = "<a href='mt_agenda.php?agenda_id=$value'>$value</a>";
                }
                if ($table === 'tasks') {
                    if ($key === 'asap' && $value == 1) {
                        $resultsHTML .= "<p style='color: red;'><strong>ASAP</strong></p>";
                        continue;
                    } elseif ($key === 'deadline' && $row['asap'] == 1) {
                        continue;
                    } elseif ($key === 'asap' && $value == 0) {
                        continue;
                    }
                }
                $resultsHTML .= "<p><strong>$key:</strong> $value</p>";
            }
            $resultsHTML .= "</div>"; // Close card-body
            $resultsHTML .= "</div>"; // Close card
        }
        $resultsHTML .= "</div>"; // Close table-results
    }
}

if (!$found) {
    $resultsHTML .= "<p>No results found.</p>";
}

$conn->close();

// Return results as HTML
echo $resultsHTML;
?>
