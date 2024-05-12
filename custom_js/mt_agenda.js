$(document).ready(function () {
    $('#agendaTable').hide();
    $('#modalBtn').hide();

    $('#agendaSelect').change(function () {
        var selectedAgenda = $(this).val();
        if (selectedAgenda !== "") {
            var table = $('#agendaTable').dataTable({
                responsive: true,
                "bDestroy": true, //Ignores the error pop up (cannot reinitialize), it works even with the error but puerly for estetic purpose. Might delete later
                layout: {
                    topStart: {
                        buttons: ['csvHtml5', 'pdfHtml5']
                    }
                },
            });
            $('#modalBtn').show();
        }
    });

    // var table = $('#mt_agenda_test2').dataTable({
    //     "order": [] //auto sort disabled
    // });

    // Handy GLOBAL variables
    var counter = 1;
    // FUNCTIONS
    // Creating New Row
    // Creating New Row
    function addNewRow(clickedCell, meanId, agendaId) {
        var newRowHtml = `
    <tr id="${meanId}" data-agenda-id="${agendaId}">
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
    // Saving the changes to database
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


    // Add  row with mean value
    // Add row with mean value
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

    // Function to show the table
    function showTable() {
        $('#agendaTable').show();
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
                            // Show the table after it's populated
                            showTable();
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
                // Show the table after it's populated
                showTable();
            }
        }
    });


    //PERSONAL TASK MODAL
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