src="https://cdn.jsdelivr.net/npm/flatpickr"
src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"
$(document).ready(function () {
    //$('#agendaTable').hide();
    showTable();

    // Initial DataTable initialization
    $('#agendaTable').dataTable({
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

    // Initialize flatpickr for existing datepicker elements
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
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
        $('#agendaTable').show();
    }

    $('#createAgendaBtn').click(function () {
        $('#createAgendaModal').modal('show');
    });

    $('#createAgendaConfirmBtn').click(function () {
        var newAgendaName = $('#agendaDate').val();
        var newAgendaDate = $('#agendaDate').val();
        var agendaid
        if (newAgendaName.trim() === '' || newAgendaDate.trim() === '') {
            alert('Please provide both agenda name and date.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'createAgenda.php',
            data: { agenda_name: newAgendaName, agenda_date: newAgendaDate },
            success: function (response) {
                //alert(response);
                var agendaid = response; // Assuming 'response' is the agenda_id returned from PHP
                // Use the agendaid as needed, for example, redirect to a page using it
                window.location.href = 'mt_agenda.php?id=' + agendaid;
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
        
    });



    // Initialize flatpickr for existing datepicker elements
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
    });

    $('#agendaSelect').change(function () {
        var selectedAgendaId = $(this).val();
        if (selectedAgendaId) {
            window.location.href = 'mt_agenda.php?agenda_id=' + selectedAgendaId;
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



    //Triggers the virtual select
    VirtualSelect.init({
        multiple: true,
        search: true,
        ele: '#changeRequestSelect'
    });

    // Track when the filter is focused
    let filterDivFocused = false;
    $('#changeRequestSelect').on('click', function () {
        filterDivFocused = true;
    });

    // Warn if user selects filter before choosing agenda
    $(document).on('click', '#changeRequestSelect', function () {
        var agendaId = $('#agendaSelect').val(); // Get the selected agenda_id
        if (agendaId) {
           // Do stuff
        } else {
            alert("Please select or create an agenda to continue.");
            $('#changeRequestSelect').hide();
        }
    });

    // Add event listener to handle clicks outside the filterDiv
    $(document).on('click', function (event) {
        if (filterDivFocused && !$(event.target).closest('#filterDiv').length) {
            filterDivFocused = false;
            var selectedValues = $('#changeRequestSelect').val();
            sendFilterData(selectedValues);
        }
    });

    function sendFilterData(selectedValues) {
        $.ajax({
            type: "POST",
            url: "actions.php", // Your PHP script to handle the data
            data: { selected_titles: selectedValues },
            success: function (response) {
                console.log(response); // Handle success response
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error("An error occurred: " + status + " " + error);
            }
        });
    }
});


$(document).ready(function() {
    $(document).on('blur', 'td[contenteditable=true]', function() {
        var $cell = $(this);
        var newValue = $cell.text();
        var rowId = $cell.closest('tr').attr('id');  // Use .attr('id') to get the row's ID attribute
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



$(document).ready(function() {
    $(document).on('blur', 'input.editabletasktopic-cell', function() {
        var $cell = $(this);
        var newValue = $cell.val(); // Use .val() to get the value of the input
        var rowId = $cell.closest('tr').attr('id'); // Use .attr('id') to get the row's ID attribute
        var type = $cell.closest('tr').data('type');
        var columnName = $cell.data('column'); // Get the column name from data attribute

        // Only proceed if columnName is defined
        if (columnName) {
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
        }
    });
});

$(document).ready(function() {
    // Load state from localStorage
    $('.asap-button').each(function() {
        var taskId = $(this).data('task-id');
        var isASAP = localStorage.getItem('asap-' + taskId) === 'true';

        if (isASAP) {
            $(this).css('color', 'red');
            $('#datepicker-' + taskId).hide();
        }
    });

    // Toggle ASAP button
    $('.asap-button').click(function() {
        var taskId = $(this).data('task-id');
        var datepicker = $('#datepicker-' + taskId);
        var isASAP = datepicker.is(':visible');

        if (isASAP) {
            $(this).css('color', 'red');
            datepicker.hide();
            localStorage.setItem('asap-' + taskId, 'true');
            updateASAPStatus(taskId, 1);
        } else {
            $(this).css('color', 'white');
            datepicker.show();
            localStorage.setItem('asap-' + taskId, 'false');
            updateASAPStatus(taskId, 0);
        }
    });

    function updateASAPStatus(taskId, status) {
        $.ajax({
            url: 'update_asap_status.php',
            type: 'POST',
            data: { task_id: taskId, asap: status },
            success: function(response) {
                console.log('ASAP status updated successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error updating ASAP status:', error);
            }
        });
    }
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

// Initialize flatpickr on document ready
$(document).ready(function() {
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
    });
});

// Function to add a new row
async function addNewRow(type, clickedCell) {
    var gft = "";
    var project = "";
    var gftFound = false;
    var projectFound = false;
    var currentRow = $(clickedCell).closest('tr');

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

    await saveToDatabase(type, gft, project);
}

async function addTask(cell) {
    var protokolId = $('#agendaSelect').val();
    await addNewRow("Task", cell, protokolId);

    const response = await fetch('getlast.php?type=task');
    const text = await response.text();
    
    let data;
    try {
        data = JSON.parse(text);
    } catch (error) {
        console.error('Error parsing JSON:', error);
        return;
    }
    var lastTask = data.last_task_id;

    var newRow = $(`
    <tr id="${lastTask}" data-type="task" data-id="${lastTask}">
        <td><strong>Task</strong></td>
        <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid white; max-width: 200px;"></td>
        <td style="background-color: #212529 !important; width: 100px !important;">
            <input class="editabletasktopic-cell" data-column="responsible" type="text" style="background-color: #212529 !important; border: 1px solid white; width: 100%; color: grey;" placeholder="Enter responsible person">
            <br><br>
            <div class="flex-container">
                <input class="editabletasktopic-cell new-datepicker-${lastTask}" data-column="deadline" type="text" style="background-color: #212529 !important; border: 1px solid white; width: 70%;" value="" placeholder="Select date">
                <button class="asap-button" data-asap="0" style="width: 30%; color: white;">ASAP</button>
            </div>
        </td>
        <td>
            <div class="button-container">
                <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                <div class="dropdown-menu">
                    <button class="dropdown-item" onclick="addTask(this)">Task</button>
                    <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                </div>
                <button class="button-12 deleteRow" role="button">-</button>
                <button data-bs-toggle="modal" data-bs-target="#forwardModal" data-id="${lastTask}" class="button-12 forwardTaskBtns" role="button">→</button>
            </div>
        </td>
    </tr>
    `);

    // Insert the new row into the table
    newRow.insertAfter($(cell).closest('tr'));

    // Initialize flatpickr for the new datepicker input
    flatpickr('.new-datepicker-' + lastTask, {
        dateFormat: 'Y-m-d',
    });
}

async function addTopic(cell) {
    var protokolId = $('#agendaSelect').val(); // Get the selected protokol_id
    console.log('Add Topic button clicked, protokolId:', protokolId);

    // Ensure addNewRow completes before proceeding
    await addNewRow("Topic", cell, protokolId);

    const response = await fetch('getlast.php?type=topic');
    const text = await response.text();    
    console.log('Response Text:', text);  // Log the response text
        
    // Try parsing the response as JSON
    let data;
    try {
        data = JSON.parse(text);
        var lastTopic = data.last_id; 
        console.log('Last Topic ID:', lastTopic);
    
        var newRow = $(`
            <tr id="${lastTopic}" data-type="topic" data-id="${lastTopic}">
                <td><strong>Topic</strong></td>
                <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid white;"></td>
                <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid white;"></td>
                <td>
                    <div class="button-container">
                        <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" onclick="addTask(this)">Task</button>
                            <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                        </div>
                        <button class="button-12 deleteRow" role="button">-</button>
                        <button data-bs-toggle="modal" data-bs-target="#forwardModal" data-id="${lastTopic}" class="button-12 forwardTopicBtns" role="button">→</button>
                    </div>
                </td>
            </tr>
        `);
        newRow.insertAfter($(cell).closest('tr'));
    } catch (error) {
        console.error('Error parsing JSON:', error);
        return;
    }

}

function saveToDatabase(newRow, gft, project) {
    console.log(newRow);
    console.log(gft);
    console.log(project);

    var selectedOption = newRow;
    var content = "content";
    var responsible = "responsible";
    var ajaxData = {
        agendaId: $('#agendaSelect').val(),
        content: content,
        responsible: responsible,
        gft: gft,
        cr: project
    };

    console.log(ajaxData);

    if (selectedOption === "Task") {
        ajaxData.taskContent = content;
    } else if (selectedOption === "Topic") {
        ajaxData.topicContent = content;
    }

    // Return a promise
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: ajaxData,
            success: function (response) {
                console.log(response);
                resolve(response); // Resolve the promise with the response
                // location.reload();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                reject(error); // Reject the promise with the error
            }
        });
    });
}
