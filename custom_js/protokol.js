$(document).ready(function () {
    //$('#protokolTable').hide();
    showTable();

    // Initial DataTable initialization
    $('#protokolTable').dataTable({

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
    $('#protokolSelect').change(function () {
        var selectedProtokol = $(this).val();
        if (selectedProtokol !== "") {
            // Load new data into the DataTable (not reinitializing)
            // You may need to implement the logic to fetch and load new data based on the selection
            //$('#protokolTable').DataTable().ajax.reload();
        }
    });

    // Creating New Row
    var counter = 1;

    function addNewRow(clickedCell, protokolId) {
        var newRow = $(`
            <tr id="${counter}">
                <td>
                    <select class="form-select task-topic-select" style="width:100px;">
                        <option value="Topic" selected>Topic</option>
                        <option value="Task">Task</option>
                    </select>
                </td>
                <td class="contenteditable description" contenteditable="true"></td>
                <td class="contenteditable responsible" contenteditable="true">Responsible</td>
                <td style="width:200px;">
                    <input type="text" class="deadlineDatePicker" style="width:100px;">
                    <button class="asapBtn" role="button">ASAP</button>
                </td>
                <td>
                    <button class='button-12 addRow' role='button'>+</button> <button class='button-12 deleteRow' role='button'>-</button>
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
                newRow.find('.responsible').text('Topic Responsible');
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
        newRow.find('.contenteditable').on('blur', function () {
            saveToDatabase(newRow, gft, project, protokolId);
        });
    }


    $(document).ready(function () { //adding new row

        $(document).on('click', '.addRow', function () {
            var protokolId = $('#protokolSelect').val(); // Get the selected protokol_id
            if (protokolId) {
                addNewRow(this, protokolId);
                saveToDatabase();
            }
            else {
                alert("Please select or create a protokol to continue.")
            }

        });
    });


    function deleteRow(clickedCell) {
        var row = $(clickedCell).closest('tr');
        var rowId = row.data('id');
        var rowType = row.data('type');
    
        row.remove();
        $.ajax({
            type: 'POST',
            url: "actions.php",
            data: {
                rowId: rowId,
                rowType: rowType
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

    function saveToDatabase(newRow, gft, project) {
        var selectedOption = newRow.find('select').val();
        var content = newRow.find('td:eq(1)').text().trim();
        var responsible = newRow.find('td:eq(2)').text().trim();
        var ajaxData = {
            protokolId: $('#protokolSelect').val(),
            content: content,
            responsible: responsible,
            gft: gft,
            cr: project
        };

        if (selectedOption === "Task") {
            ajaxData.taskContent = content;
        } else if (selectedOption === "Topic") {
            ajaxData.topicContent = content;
        }

        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: ajaxData,
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }


    function showTable() {
        $('#protokolTable').show();
    }

    $('#createProtokolBtn').click(function () {
        $('#createProtokolModal').modal('show');
    });

    $('#createProtokolConfirmBtn').click(function () {
        var newProtokolName = $('#protokolName').val();
        var newProtokolDate = $('#protokolDate').val();
        var protokolid
        if (newProtokolName.trim() === '' || newProtokolDate.trim() === '') {
            alert('Please provide both protokol name and date.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: { protokol_name: newProtokolName, protokol_date: newProtokolDate },
            success: function (response) {
                alert(response);
                protokolid = response;
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });

        window.location.href = 'mt_protokol.php?protokol_id=' + protokolid;

    });

    $('#protokolSelect').change(function () {
        var selectedProtokolId = $(this).val();
        if (selectedProtokolId) {
            window.location.href = 'protokol.php?protokol_id=' + selectedProtokolId;
        }
    }
    );

    $('#personalTaskBtn').click(function () {
        var summary = $('#summary').val();
        var user_id = 1;  //example. later will be read from user_id
        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: { summary: summary, user_id: user_id },
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});
