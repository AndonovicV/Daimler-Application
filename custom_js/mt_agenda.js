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

    //Datatable Deadline Date Picker
    new DateTime(document.getElementById('deadlineDatePicker'), {
        format: 'D/M/YYYY'
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
    
    function addNewRow(clickedCell) {
        var newRow = $(`
            <tr id="${counter}">
                <td>
                    <select class="form-select" style ="width:100px;">
                        <option value="Topic" selected>Topic</option>
                        <option value="Task">Task</option>
                    </select>
                </td>
                <td class="contenteditable" contenteditable="true">Task Description</td>
                <td class="contenteditable" contenteditable="true">Responsible</td>
                <td><input id='deadlineDatePicker' type='text' value='Date' style = 'width:50px;margin-right: 5px;'><button>ASAP</button></td>
                <td><button style="text-align: center;" class="btn btn-primary addRow">New Row</button></td>
                <!-- <td><button style="text-align: center;" class="btn btn-danger deleteRow">Delete</button></td> -->
                <td><input type='checkbox'></td>
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
            } else if (cellContent.startsWith("title") && !projectFound) {
                project = cellContent;
                projectFound = true;
            }
            currentRow = currentRow.prev();
        }
        project = project.substring("title for".length).trim();
        gft = gft.substring("GFT".length).trim();
        alert(project)
        alert(gft)
        setTimeout(function() {
            saveToDatabase(newRow, gft, project, agendaId);
        }, 10000);
    }

    $(document).ready(function(){ //adding new row
        $(document).on('click', '.addRow', function(){
            addNewRow(this);
            saveToDatabase();
        });
    });

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

    function saveToDatabase(newRow, gft, cr, agendaId) {
        var selectedOption = newRow.find('select').val();
        var content = newRow.find('td:eq(1)').text().trim();
        var responsible = newRow.find('td:eq(2)').text().trim();
        var ajaxData = {};
        //alert(gft)
        //alert(cr)
        if (selectedOption === "Task") {
            ajaxData = {
                taskContent: content,
                taskResponsible: responsible,
                taskGft: gft, // Use taskGft instead of gft
                taskcr: cr, // Use taskProject instead of project
                agendaId:agendaId
            };
        } else if (selectedOption === "Topic") {
            ajaxData = {
                topicContent: content,
                topicResponsible: responsible,
                topicGft: gft, // Use taskGft instead of gft
                topiccr: cr, // Use taskProject instead of project
                agendaId:agendaId
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
