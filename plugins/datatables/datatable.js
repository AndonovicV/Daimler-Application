$(document).ready(function() {
    $('#agendaTable').DataTable({
        "order": []
    });

    var counter = 1; // Initialize counter outside the function

    // Event delegation for dynamically added rows
    $('#agendaTable').on('click', '.addRow', addNewRow);

    function addNewRow() {
        var newRowData = [
            '<button class="addRow">Add new row</button>',
            counter + '.1',
            counter + '.2',
            counter + '.3',
            counter + '.4',
            counter + '.5',
            counter + '.6'
        ];
        var parentRow = $(this).closest('tr');

        // Add the new row data to the DataTable
        var table = $('#agendaTable').DataTable(); 
        var newRow = table.row.add(newRowData).draw(false).node(); // Correct table ID
        $(newRow).insertAfter(parentRow);

        // Increment the counter for the next row
        counter++;
    }
});
