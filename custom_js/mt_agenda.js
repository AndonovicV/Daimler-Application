$(document).ready(function () {
    //$('#agendaTable').hide();
    $('#modalBtn').hide();
    showTable();

    // Initial DataTable initialization
    $('#agendaTable').dataTable({
        "order": [],
        "paging": false, // Disable pagination
        "searchable": true,
        "bDestroy": true, // Ignores the error popup (cannot reinitialize), it works even with the error but purely for aesthetic purpose. Might delete later
        "layout": {
            "topStart": {
                "buttons": ['csvHtml5', 'pdfHtml5']
            }
        }
    });

    $('#agendaSelect').change(function () {
        var selectedAgenda = $(this).val();
        if (selectedAgenda !== "") {
            // Load new data into the DataTable (not reinitializing)
            // You may need to implement the logic to fetch and load new data based on the selection
            //$('#agendaTable').DataTable().ajax.reload();
            $('#modalBtn').show();
        }
    });

    // Creating New Row
    var counter = 1;

    function addNewRow(clickedCell, meanId, agendaId) {
        var newRowHtml = `
    <tr id="${meanId}" data-agenda-id="${agendaId}">
        <td contenteditable="true"></td>
        <td contenteditable="true">${counter}</td>
        <td contenteditable="true">${counter}</td>
        <td><button class="btn btn-primary addRow">New Row</button></td>
        <td><button class="btn btn-danger deleteRow">Delete Row</button></td>
    </tr>`;
        counter++;
        $(newRowHtml).insertAfter($(clickedCell).closest('tr'));
    }

    function deleteRow(clickedCell) {
        var row = $(clickedCell).closest('tr');
        var rowId = row.attr('id');
        row.remove();
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

    function saveToDatabase(meanId, agendaId) {
        $.ajax({
            type: 'POST',
            url: "actions.php",
            data: {
                meanId: meanId,
                counter: counter,
                agendaId: agendaId // Include agendaId in the data
            },
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    $(document).on('click', '.addRow', function () {
        var currentRow = $(this).closest('tr'); // Get the current row
        var currentRowId = parseFloat(currentRow.attr('id')); // Get the ID of the current row
        var agendaId = $('#agendaSelect').val(); // Get the selected agenda_id

        var nextRow = currentRow.next(); // Get the next row
        var nextRowId = nextRow.attr('id'); // Get the ID of the next row

        if (nextRowId === undefined) {
            // If there is no next row, set nextRowId to currentRowId + 1
            nextRowId = currentRowId + 1;
        } else {
            nextRowId = parseFloat(nextRowId);
        }

        var meanId = (currentRowId + nextRowId) / 2;
        addNewRow(this, meanId, agendaId); // Pass agendaId to addNewRow function
        saveToDatabase(meanId, agendaId); // Pass agendaId to saveToDatabase function
    });

    function populateTable(agendaId) {
        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: { agenda_id: agendaId },
            success: function (response) {
                //var table = $('#agendaTable').DataTable();
                table.clear().draw();
                $.each(response, function(index, rowData) {
                    table.row.add(rowData).draw();
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    function showTable() {
        $('#agendaTable').show();
    }

    $('#createAgendaBtn').click(function () {
        $('#createAgendaModal').modal('show');
    });

    $('#createAgendaConfirmBtn').click(function () {
        var newAgendaName = $('#agendaName').val();
        var newAgendaDate = $('#agendaDate').val();

        if (newAgendaName.trim() === '' || newAgendaDate.trim() === '') {
            alert('Please provide both agenda name and date.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: { agenda_name: newAgendaName, agenda_date: newAgendaDate },
            success: function (response) {
                populateTable(response);
                $('#agendaSelect').val(response);
                showTable();
                $('#createAgendaModal').modal('hide');
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    $('#agendaSelect').change(function () {
        var selectedAgendaId = $(this).val();
        if (selectedAgendaId) {
                populateTable(selectedAgendaId);
                showTable();
            }
        }
    );

    document.getElementById('agendaSelect').addEventListener('change', function () {
        var selectedAgendaId = this.value;
        $.ajax({
            type: 'POST',
            url: 'actions.php', // Create this PHP file to handle the request
            data: { selectedAgendaId: selectedAgendaId },
            success: function (response) {
                $('#personalTaskLabel').text(response);
            }
        });
    });
});
