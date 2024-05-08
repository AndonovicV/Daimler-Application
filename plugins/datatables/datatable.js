// Handy GLOBAL variables
var table = new DataTable('#agendaTable', {
    columnDefs: [
        {
            targets: [2, 3],
            orderable: false
        }
    ]
});
var counter = 1;

// Datatable Settings
$(document).ready(function(){
    $('#agendaTable').dataTable( {
        "order": [],
        "paging": false // Disable pagination
        ,searchable: false
    });
});


// TRIGGERS
$(document).ready(function(){ //adding new row
    $(document).on('click', '.addRow', function(){
        addNewRow(this);
        saveToDatabase();
    });
});

$(document).ready(function(){ //deleting new row
    $(document).on('click', '.deleteRow', function(){
        deleteRow(this);
    });
});


    
function addNewRow(clickedCell) {
    var newRow = $(`
        <tr id="${counter}">
            <td>
                <select class="form-select">
                    <option value="Topic" selected>Topic</option>
                    <option value="Task">Task</option>
                </select>
            </td>
            <td class="contenteditable" contenteditable="true">Placeholder</td>
            <td class="contenteditable" contenteditable="true">Responsible</td>
            <td><button style="text-align: center;" class="btn btn-primary addRow">New Row</button></td>
            <td><button style="text-align: center;" class="btn btn-danger deleteRow">Delete</button></td>
        </tr>
    `);
    counter++;

    newRow.insertAfter($(clickedCell).closest('tr'));


    // Find GFT and Project names
    var gft = "";
    var project = "";
    var gftFound = false; // Track if GFT name is found
    var projectFound = false; // Track if GFT name is found
    var currentRow = newRow.prev();
    // Search for GFT and Project names in preceding rows
    while (currentRow.length > 0 && !gftFound) {
        var cells = currentRow.find('td:eq(1)'); // Only search in the 2nd column
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("GFT")) {
            gft = cellContent;
            gftFound = true; // Set flag to true if GFT name is found
        } else if (cellContent.startsWith("Project") && !projectFound) {
            project = cellContent;
            projectFound = true;
        }
        currentRow = currentRow.prev();
    }
    project = project.substring("Project".length).trim();
    gft = gft.substring("GFT".length).trim();
    alert(project)
    alert(gft)
    setTimeout(function() {
        saveToDatabase(newRow, gft, project);
    }, 10000);
}

function deleteRow(clickedCell) {
    var row = $(clickedCell).closest('tr');
    row.remove(); 
    table.row(row).remove().draw(); 
    counter--; 

    $.ajax({
        type: 'POST',
        url: 'deleteRow.php', 
        data: {
            rowId: row.attr('id') 
        },
        success: function(response) {
            //alert("Row with ID " + row.attr('id') + " deleted successfully");
            //console.log('Row deleted successfully.');
            row.remove();
        },
        error: function(xhr, status, error) {
            //alert("Error deleting row: " + error);
            console.error('Error deleting row:', error);
        }
    });
}

function saveToDatabase(newRow, gft, project) {
    var selectedOption = newRow.find('select').val();
    var content = newRow.find('td:eq(1)').text().trim();
    var responsible = newRow.find('td:eq(2)').text().trim();
    var ajaxData = {};
    alert(gft)
    alert(project)
    if (selectedOption === "Task") {
        ajaxData = {
            taskContent: content,
            taskResponsible: responsible,
            taskGft: gft, // Use taskGft instead of gft
            taskProject: project // Use taskProject instead of project
        };
    } else if (selectedOption === "Topic") {
        ajaxData = {
            topicContent: content,
            topicResponsible: responsible,
            topicGft: gft, // Use topicGft instead of gft
            topicProject: project // Use topicProject instead of project
        };
    }

    $.ajax({
        type: 'POST',
        url: 'actions.php',
        data: ajaxData, // Send the modified data object
        success: function(response) {
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}