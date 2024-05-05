$(document).ready(function () {
    // Handy GLOBAL variables
    var table = new DataTable('#agendaTable');
    var counter = 1;

    // Datatable Settings
    $('#agendaTable').dataTable({
        "order": [] //auto sort disabled
    });

    // FUNCTIONS
    // Creating New Row
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
        </tr>`;
        counter++;
        $(newRowHtml).insertAfter($(clickedCell).closest('tr'));
    }

    // Deleting selected row
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
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    $(document).on('click', '.deleteRow', function () {
        deleteRow(this);
    });

    // Saving the changes to database
    function saveToDatabase(meanId) {
        $.ajax({
            type: 'POST',
            url: "actions.php",
            data: {
                meanId: meanId,
                counter: counter
            },
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Add  row with mean value
    $(document).on('click', '.addRow', function () {
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


    // Function to populate the table with agenda data
    function populateTable(agendaId) {
        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: { agenda_id: agendaId },
            success: function (response) {
                $('#agendaTableBody').html(response);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Event listener for agenda selection change
    $('#agendaSelect').change(function () {
        var selectedAgendaId = $(this).val();
        if (selectedAgendaId) {
            if (selectedAgendaId === 'new') {
                // Prompt user to enter new agenda name
                var newAgendaName = prompt('Enter the name for the new agenda:');
                if (newAgendaName !== null && newAgendaName.trim() !== '') {
                    // Create new agenda
                    $.ajax({
                        type: 'POST',
                        url: 'actions.php',
                        data: { agenda_name: newAgendaName },
                        success: function (response) {
                            // Retrieve newly created agenda ID and populate the table
                            populateTable(response);
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                        }
                    });
                } else {
                    // If agenda name is not provided, reset dropdown to default value
                    $(this).val('');
                }
            } else {
                // Otherwise, populate the table with the selected agenda's data
                populateTable(selectedAgendaId);
            }
        }
    });


    // Listen for cell edit events
    $('#agendaTable').on('dblclick', 'td[contenteditable=true]', function () {
        $(this).attr('contenteditable', 'true');
    });

    // Handle cell edit
    $('#agendaTable').on('blur', 'td[contenteditable=true]', function () {
        var cell = table.cell(this);
        var columnIdx = cell.index().column;
        var rowIdx = cell.index().row;
        var newValue = $(this).text();
        var columnTitle = table.column(columnIdx).header().innerHTML;

        // Send updated data to server
        $.ajax({
            url: 'update_cell_data.php',
            type: 'POST',
            data: {
                rowId: table.row(rowIdx).id(),
                columnTitle: columnTitle,
                newValue: newValue
            },
            success: function (response) {
                console.log('Cell data updated successfully:', response);
            },
            error: function (xhr, status, error) {
                console.error('Error updating cell data:', error);
            }
        });
    });
});