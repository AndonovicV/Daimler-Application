<?php
include_once('navigation.php');
include 'conn.php';

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
} else {
    $selected_team = ""; // Default value if not set
}

$conn->close();
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

    <!-- Custom CSS -->
    <link href="custom_css\mt_agenda.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="custom_js/search.js"></script>

</head>

<body>
<div class="container">
<div class="container mt-5">
    <h1 style="color: #777" class='mt-4'>Search</h1>
    <div class="d-flex mb-3">
        <input 
            type="text" 
            id="searchBox" 
            class="w-100" 
            style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align: center!important;" 
            placeholder="Press Enter to search..." />
    </div>
    
    <div class="d-flex mb-3">
        <select id="filterBox" class="w-100" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important;">
            <option value="">All</option>
            <option class = 'topic-row' value="topics">Topics</option>
            <option class = 'task-row'value="tasks">Tasks</option>
            <option value="information">Information</option>
            <option value="assignment">Assignment</option>
            <option value="decision">Decision</option>
        </select>
    </div>
        
    <div id="searchResults" class="mt-4" style="color: #777"></div>

    <script>
        $(document).ready(function() {
            $('#searchBox').keypress(function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    performSearch();
                }
            });

            $('#filterBox').change(function() {
                performSearch();
            });

            function performSearch() {
                var query = $('#searchBox').val();
                var filter = $('#filterBox').val();
                if (query) {
                    $.ajax({
                        url: 'searchfunction.php',
                        type: 'GET',
                        data: { query: query, filter: filter },
                        success: function(data) {
                            console.log("Data received from PHP:", data);
                            $('#searchResults').html(data);
                        },
                        error: function() {
                            $('#searchResults').html('<p>An error occurred while searching.</p>');
                        }
                    });
                } else {
                    $('#searchResults').html('<p>Please enter a search query.</p>');
                }
            }
        });
    </script>
</div>
</div>

</body>

</html>
