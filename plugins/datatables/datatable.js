$(document).ready(function() {
    $('#agendaTable').DataTable({
        "order": []
    });
});

$(document).ready(function(){
    $(document).on('click', '.addRow', function(){
        addNewRow(this);
        saveToDatabase()
    });
});

var counter = 1;

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
            <td contenteditable="true">`+ counter + `</td>
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
