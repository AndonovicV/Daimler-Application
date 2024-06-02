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
                "buttons":[
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (typically the actions column)
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (typically the actions column)
                        }
                    }
                ]            
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


$(document).ready(function() {
    $('td[class=editabletasktopic-cell]').on('blur', function() {
        var $cell = $(this);
        var newValue = $cell.text();
        var rowId = $cell.closest('tr').data('id');
        var cellIndex = $cell.index();
        var type = $cell.closest('tr').data('type');
        var columnName = (cellIndex === 1) ? 'name' : 'responsible';

        $.ajax({
            url: 'update_cell.php',
            method: 'POST',
            data: {
                id: rowId,
                value: newValue,
                column: columnName,
                type: type
            },
            success: function(response) {
                console.log('Update successful');
            },
            error: function() {
                console.log('Update failed');
            }
        });
    });
});

function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropdown-toggle')) {
        const dropdowns = document.getElementsByClassName('dropdown-menu');
        for (let i = 0; i < dropdowns.length; i++) {
            const openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block') {
                openDropdown.style.display = 'none';
            }
        }
    }
}

function addNewRow(type, clickedCell, protokolId) {
    var gft = "";
    var project = "";
    var gftFound = false;
    var projectFound = false;
    var currentRow = $(clickedCell).closest('tr');
    console.log(currentRow)
    while (currentRow.length > 0 && !gftFound) {
        var cells = currentRow.find('td:eq(1)');
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("GFT")) {
            console.log(cellContent)
            gft = cellContent;
            gftFound = true;
        } else if (cellContent.startsWith("title") && !projectFound) {
            console.log(cellContent)
            project = cellContent;
            projectFound = true;
        }
        currentRow = currentRow.prev();
    }
    
    project = project.substring("title for".length).trim();
    gft = gft.substring("GFT".length).trim();
    saveToDatabase(type, gft, project, protokolId);
}

function addTask(cell) {
    var protokolId = $('#protokolSelect').val(); // Get the selected protokol_id
    //alert('Add Task button clicked!');
    addNewRow("Task", cell, protokolId);
}

function addTopic(cell) {
    var protokolId = $('#protokolSelect').val(); // Get the selected protokol_id
    //alert('Add Topic button clicked!');
    addNewRow("Topic", cell, protokolId);
}
function saveToDatabase(newRow, gft, project) {
    console.log(newRow)
    console.log(gft)
    console.log(project)

    var selectedOption = newRow;
    var content = "content";
    var responsible = "responsible";
    var ajaxData = {
        agendaId: $('#protokolSelect').val(),
        content: content,
        responsible: responsible,
        gft: gft,
        cr: project
    };

    console.log(ajaxData)

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
            location.reload();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}