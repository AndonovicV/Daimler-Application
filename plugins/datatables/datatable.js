// Handy GLOBAL variables
var table = new DataTable('#agendaTable');
var counter = 1;

// Datatable Settings
$(document).ready(function(){
    $('#agendaTable').dataTable( {
        "order": [] //auto sort diabled
    });
});


// TRIGGERS
$(document).ready(function(){ //addinng new row
    $(document).on('click', '.addRow', function(){
        addNewRow(this);
        saveToDatabase();
    });
});


    // Trigger to delete row when delete icon is clicked
    $(document).ready(function(){ //removing row
        $(document).on('click', '.icon-delete', function(){
            var row = $(this).closest('tr');
            table.row(row).remove().draw();
        });
    });

    
// FUNCTIONS
function addNewRow(clickedCell) {

    var newRowHtml = `
        <tr>
            <td></td>
            <td class='addRow' id='addRowCell`+ counter + `'>New Row</td>'>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td contenteditable="true">`+ counter + `</td>
            <td><img src="delete_icon.png" class="icon-delete" alt="Delete"></td>
        </tr>
        `
        counter++;

        $(newRowHtml).insertAfter($(clickedCell).closest('tr'));
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
