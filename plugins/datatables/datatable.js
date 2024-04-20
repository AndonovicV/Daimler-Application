$(document).ready(function() {
    $('#agendaTable').DataTable();

    var counter = 1; // Initialize counter outside the function

    function addNewRow() {
        var newRowData = [
            counter + '.1',
            counter + '.2',
            counter + '.3',
            counter + '.4',
            counter + '.5'
        ];

        // Add the new row data to the DataTable
        var table = $('#agendaTable').DataTable(); // Correct table ID
        table.row.add(newRowData).draw();

        // Increment the counter for the next row
        counter++;
    }

    $('.addRow').on('click', addNewRow); // Use class selector for buttons
});