$(document).ready(function () {


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
                    <select class="form-select task-topic-select" style="width:100px;">
                        <option value="Topic" selected>Topic</option>
                        <option value="Task">Task</option>
                    </select>
                </td>
                <td class="contenteditable description" contenteditable="true"></td>
                <td class="contenteditable responsible" contenteditable="true"></td>
                <td style="width:200px;">
                    <input type="text" class="deadlineDatePicker" style="width:100px;">
                    <button class="asapBtn" role="button">ASAP</button>
                </td>
                <td>
                    <button class="button-12 addRow" role="button">New Row</button>
                </td>
                <td><input type='checkbox'></td>
            </tr>
        `);
        counter++;

        newRow.insertAfter($(clickedCell).closest('tr'));

        // Initialize the date picker for the new deadline input
        new DateTime(newRow.find('.deadlineDatePicker')[0], {
            format: 'D/M/YYYY'
        });

        newRow.find('.task-topic-select').change(function () {
            var selectedOption = $(this).val();
            if (selectedOption === "Topic") {
                newRow.find('.description').text('Topic Description');
                newRow.find('.responsible').text('');
                newRow.find('.deadlineDatePicker').hide();
                newRow.find('.asapBtn').hide();
            } else if (selectedOption === "Task") {
                newRow.find('.description').text('Task Description');
                newRow.find('.responsible').text('Task Responsible');
                newRow.find('.deadlineDatePicker').show();
                newRow.find('.asapBtn').show();
            }
        });

        newRow.find('.task-topic-select').trigger('change');

        // Add toggle functionality for the ASAP button
        newRow.find('.asapBtn').click(function () {
            $(this).toggleClass('asap-active');
        });

        var gft = "";
        var project = "";
        var gftFound = false;
        var projectFound = false;
        var currentRow = newRow.prev();
        while (currentRow.length > 0 && !gftFound) {
            var cells = currentRow.find('td:eq(1)');
            var cellContent = cells.text().trim();
            if (cellContent.startsWith("GFT")) {
                gft = cellContent;
                gftFound = true;
            } else if (cellContent.startsWith("title") && !projectFound) {
                project = cellContent;
                projectFound = true;
            }
            currentRow = currentRow.prev();
        }
        project = project.substring("title for".length).trim();
        gft = gft.substring("GFT".length).trim();
        alert(project);
        alert(gft);
        setTimeout(function () {
            saveToDatabase(newRow, gft, project, agendaId);
        }, 10000);
    }


    $(document).ready(function () { //adding new row
        $(document).on('click', '.addRow', function () {
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
                agendaId: agendaId
            };
        } else if (selectedOption === "Topic") {
            ajaxData = {
                topicContent: content,
                topicResponsible: responsible,
                topicGft: gft, // Use taskGft instead of gft
                topiccr: cr, // Use taskProject instead of project
                agendaId: agendaId
            };
        }

        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: ajaxData, // Send the modified data object
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
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
                $.each(response, function (index, rowData) {
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

    $('#personalTaskBtn').click(function () {
        var summary = $('#summary').val();
        var user_id = 1;  //example. later will be read from user_id
        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: { summary: summary,user_id: user_id},
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});
