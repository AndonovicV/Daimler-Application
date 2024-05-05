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


    
// FUNCTIONS
function addNewRow(clickedCell) {
    var newRowHtml = `
        <tr id="${counter}">
            <td></td>
            <td class = "contenteditable" contenteditable="true">`+ "Placeholder" + `</td>
            <td class = "contenteditable" contenteditable="true">`+ "Responsible" + `</td>
            <td><button style="text-align: center;" class="btn btn-primary addRow">New Row</button></td>
            <td><button style="text-align: center;" class="btn btn-danger deleteRow">Delete</button></td>
        </tr>
    `;
    counter++;

    $(newRowHtml).insertAfter($(clickedCell).closest('tr'));
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



function saveToDatabase() {
    $.ajax({
        type: 'POST',
        url: 'datatableDatabase.php',
        data: {
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
