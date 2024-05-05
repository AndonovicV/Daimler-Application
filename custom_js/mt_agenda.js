// Handy GLOBAL variables
var table = new DataTable('#agendaTable');
var counter = 1;

// Datatable Settings
$(document).ready(function(){
    $('#agendaTable').dataTable({
        "order": [] //auto sort disabled
    });

$(document).on('click', '.addRow', function(){
    var currentRow = $(this).closest('tr'); // Get the current row
    var currentRowId = parseFloat(currentRow.attr('id')); // Get the ID of the current row
    
    var nextRow = currentRow.next(); // Get the next row
    var nextRowId = nextRow.attr('id'); // Get the ID of the next row
    
    if (nextRowId === undefined) {
        // If there is no next row, set nextRowId to currentRowId + 1
        nextRowId = currentRowId + 1;
    } else {
        nextRowId = parseFloat(nextRowId);
    }
    
    var meanId = (currentRowId + nextRowId) / 2;
    addNewRow(this, meanId);
    saveToDatabase(meanId);
});


    // Deleting new row
    $(document).on('click', '.deleteRow', function(){   
        deleteRow(this);
    });

});

// FUNCTIONS
function addNewRow(clickedCell, meanId) {
    var newRowHtml = `
        <tr id="${meanId}">
            <td contenteditable="true">${meanId}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td contenteditable="true">${counter}</td>
            <td><button class="btn btn-primary addRow">New Row</button></td>
            <td><button class="btn btn-danger deleteRow">Delete Row</button></td>
        </tr>
    `;
    counter++;

    $(newRowHtml).insertAfter($(clickedCell).closest('tr'));
}

function deleteRow(clickedCell) {
    var row = $(clickedCell).closest('tr');
    var rowId = row.attr('id');
    row.remove(); 
    table.row(row).remove().draw(); 
    $.ajax({
        type: 'POST',
        url: "actions.php", 
        data: {
           rowId: rowId
        },
        success: function(response) {
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

function saveToDatabase(meanId) {
    $.ajax({
        type: 'POST',
        url: "actions.php",
        data: {
            meanId: meanId,
            counter: counter
        },
        success: function(response) {
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}
