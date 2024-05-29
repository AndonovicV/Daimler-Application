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

// Define an array of tables and the columns to search
$tables = [
    'change_requests' => ['title', 'project', 'lead_gft', 'lead_module_team'],
    'dept_tbl' => ['name'],
    'guests_tbl' => ['name'],
    'mt_agenda_list' => ['agenda_date', 'module_team'],
    'org_gfts' => ['name', 'moduleteam'],
    'tasks' => ['name', 'responsible', 'gft', 'cr'],
    'topics' => ['name', 'responsible', 'gft', 'cr'],
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
        $resultsHTML .= "<h2>Results from Table: $table</h2>";
        while ($row = $result->fetch_assoc()) {
            $resultsHTML .= "<p>";
            foreach ($row as $key => $value) {
                $resultsHTML .= "<strong>$key:</strong> $value<br>";
            }
            $resultsHTML .= "</p>";
        }
    } else {
        error_log("No results found for query: $sql");
    }
    if ($resultsHTML == "") {
        $resultsHTML .= "<p>No results found.</p>";
    }
}

$conn->close();

// Return results as HTML
echo $resultsHTML;
?>
