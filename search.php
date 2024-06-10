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
        
    <div id="searchResults" class="mt-4" style="color: #777"></div>

    <script>
        $(document).ready(function() {
            $('#searchBox').keypress(function(event) {
                // Check if the Enter key is pressed (key code 13)
                if (event.keyCode === 13) {
                    // Prevent the default action of the Enter key (form submission)
                    event.preventDefault();
                    // Perform the search
                    performSearch();
                }
            });

            function performSearch() {
                var query = $('#searchBox').val();
                if (query) {
                    $.ajax({
                        url: 'searchfunction.php',
                        type: 'GET',
                        data: { query: query },
                        success: function(data) {
                            console.log("Data received from PHP:", data);  // Log raw data
                            $('#searchResults').html(data); // Display HTML response directly
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
