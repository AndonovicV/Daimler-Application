<?php
include 'conn.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Make sure this path is writable

// Get the search query
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

$resultsHTML = '';
$found = false; // Initialize found variable

// Define an array of tables and the columns to search
$tables = [
    'change_requests' => ['title', 'project', 'lead_gft', 'lead_module_team'],
    'dept_tbl' => ['name'],
    'guests_tbl' => ['name'],
    'mt_agenda_list' => ['agenda_date', 'module_team', 'agenda_id'], // Include agenda_id here
    'org_gfts' => ['name', 'moduleteam'],
    'tasks' => ['name', 'responsible', 'gft', 'cr'],
    'topics' => ['name', 'responsible', 'gft', 'cr'],
    'information' => ['agenda_id', 'content', 'gft', 'cr'],
    'assignment' => ['agenda_id', 'content', 'gft', 'cr'],
    'decision' => ['agenda_id', 'content', 'gft', 'cr'],

];

// Iterate through each table and perform the search
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
            $resultsHTML .= "<div class='card'>";
            $resultsHTML .= "<div class='card-body'>";
            foreach ($row as $key => $value) {
                $found = true;
                // Customize the formatting of each key-value pair
                if ($key === 'cr' or $key === 'ID') {
                    // Make agenda IDs clickable links
                    $value = "<a href='cr.php?agenda_id=$value'>$value</a>";
                }
                if ($key === 'agenda_id') {
                    // Make agenda IDs clickable links
                    $value = "<a href='mt_agenda.php?agenda_id=$value'>$value</a>";
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
