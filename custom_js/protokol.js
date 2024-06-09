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
                "buttons": [
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
            var agendaId = $('#protokolSelect').val(); // Get the selected protokol_id
            if (agendaId) {
               // Do stuff
            } else {
                alert("Please select protokol to continue.");
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


    function deleteIADRow(clickedCell) {
        var row = $(clickedCell).closest('tr');
        var rowId = row.data('id');
        var rowType = row.data('type');

        row.remove();
        $.ajax({
            type: 'POST',
            url: "deleteIAD.php",
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
    $(document).on('click', '.deleteIADRow', function () {
        deleteIADRow(this);
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


$(document).ready(function () {
    $(document).on('blur', 'td[contenteditable=true]:not(.editable-cell)', function () {
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
            success: function (response) {
                console.log('Update successful');
            },
            error: function () {
                console.log('Update failed');
            }
        });
    });
});

$(document).ready(function () {
    $(document).on('blur', '.editable-cell', function () {
        var $cell = $(this);
        var newValue = $cell.text();
        var Id = $cell.closest('tr').data('id');
        var rowType = $cell.closest('tr').data('type');

        $.ajax({
            url: 'saveContent.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id: Id,
                row_type: rowType,
                content: newValue
            }),
            success: function (response) {
                console.log('Content saved successfully');
            },
            error: function () {
                console.error('Failed to save content');
            }
        });
    });
});

$(document).ready(function () {
    $(document).on('blur', 'input.editabletasktopic-cell', function () {
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
                success: function (response) {
                    console.log('Update successful');
                },
                error: function () {
                    console.log('Update failed');
                }
            });
        }
    });
});

$(document).on('click', '.asap-button', function () {
    var $button = $(this);
    var rowId = $button.closest('tr').attr('id'); // Use .attr('id') to get the row's ID attribute
    var type = $button.closest('tr').data('type');
    var currentAsap = $button.data('asap');
    var newAsap = currentAsap ? 0 : 1; // Toggle ASAP value

    // Update button appearance
    $button.data('asap', newAsap);
    $button.css('color', newAsap ? 'red' : 'white');

    // Send AJAX request to update ASAP value
    $.ajax({
        url: 'update_cell.php',
        method: 'POST',
        data: {
            id: rowId,
            value: newAsap,
            column: 'asap',
            type: type
        },
        success: function (response) {
            console.log('ASAP update successful');
        },
        error: function () {
            console.log('ASAP update failed');
        }
    });
});

function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
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

async function addNewRow(type, clickedCell) {
    var gft = "";
    var project = "";
    var gftFound = false;
    var projectFound = false;
    var currentRow = $(clickedCell).closest('tr');
    console.log(currentRow);

    while (currentRow.length > 0 && !gftFound) {
        var cells = currentRow.find('td:eq(1)');
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("GFT")) {
            console.log(cellContent);
            gft = cellContent;
            gftFound = true;
        } else if (cellContent.startsWith("title") && !projectFound) {
            console.log(cellContent);
            project = cellContent;
            projectFound = true;
        }
        currentRow = currentRow.prev();
    }

    project = project.substring("title for".length).trim();
    gft = gft.substring("GFT".length).trim();

    // Await the saveToDatabase call
    await saveToDatabase(type, gft, project);
}

$(document).ready(function () {
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            // Add any additional options here
        });
    });
});


async function addTask(cell) {
    const urlParams = new URLSearchParams(window.location.search);
    const protokolId = urlParams.get('protokol_id');

    console.log('Add Task button clicked, protokolId:', protokolId);

    // Ensure addNewRow completes before proceeding
    await addNewRow("Task", cell, protokolId);

    const response = await fetch('getlast.php?type=task');
    const text = await response.text();
    console.log('Response Text:', text);  // Log the response text

    // Try parsing the response as JSON
    let data;
    try {
        data = JSON.parse(text);
    } catch (error) {
        console.error('Error parsing JSON:', error);
        return;
    }

    if (data.error) {
        console.error('Error in response:', data.error);
        return;
    }

    var lastTaskId = data.last_task_id;
    var lastInformationId = data.last_information_id;
    var lastAssignmentId = data.last_assignment_id;
    var lastDecisionId = data.last_decision_id;

    console.log('Last Task ID:', lastTaskId);
    console.log('Last Information ID:', lastInformationId);
    console.log('Last Assignment ID:', lastAssignmentId);
    console.log('Last Decision ID:', lastDecisionId);

    var newRow = $(`
        <tr id="${lastTaskId}" data-type="task" data-id="${lastTaskId}">
            <td><strong>Task</strong></td>
            <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid white; max-width: 200px;"></td>
            <td style="background-color: #212529 !important; width: 100px !important;">
                <input class="editabletasktopic-cell" data-column="responsible" type="text" style="background-color: #212529 !important; border: 1px solid white; width: 100%;" value="">
                <br>
                <br>
                <input class="editabletasktopic-cell datepicker" data-column="deadline" type="text" style="background-color: #212529 !important; border: 1px solid white; width: 70%;" value=""><button class="asap-button" data-asap="0" style="width: 30%; color: white;">ASAP</button>
            </td>
            <td>
                <div class="button-container">
                    <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" onclick="addTask(this)">Task</button>
                        <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                    </div>
                    <button class="button-12 deleteRow" role="button">-</button>
                    <button data-bs-toggle="modal" data-bs-target="#forwardModal" data-id="${lastTaskId}" class="button-12 forwardTaskBtns" role="button">→</button>
                </div>
            </td>
        </tr>
        <tr id="${lastInformationId}" data-type="I" data-id="${lastInformationId}">
            <td><strong>I</strong></td>
            <td class="editable-cell" contenteditable="true"></td>
            <td></td>
            <td>
                <div class="button-container">
                    <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" onclick="addTask(this)">Task</button>
                        <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                        <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                        <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                        <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                    </div>
                    <button class="button-12 deleteRow" role="button">-</button>
                </div>
            </td>
        </tr>
        <tr id="${lastAssignmentId}" data-type="A" data-id="${lastAssignmentId}">
            <td><strong>A</strong></td>
            <td class="editable-cell" contenteditable="true"></td>
            <td></td>
            <td>
                <div class="button-container">
                    <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" onclick="addTask(this)">Task</button>
                        <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                        <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                        <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                        <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                    </div>
                    <button class="button-12 deleteRow" role="button">-</button>
                </div>
            </td>
        </tr>
        <tr id="${lastDecisionId}" data-type="D" data-id="${lastDecisionId}">
            <td><strong>D</strong></td>
            <td class="editable-cell" contenteditable="true"></td>
            <td></td>
            <td>
                <div class="button-container">
                    <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" onclick="addTask(this)">Task</button>
                        <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                        <button class='dropdown-item' onclick=\"addnew('I', this)\">Information</button>
                        <button class='dropdown-item' onclick=\"addnew('A', this)\">Assignment</button>
                        <button class='dropdown-item' onclick=\"addnew('D', this)\">Decision</button>
                    </div>
                    <button class="button-12 deleteRow" role="button">-</button>
                </div>
            </td>
        </tr>
    `);
    // Re-initialize flatpickr on the newly added input elements
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
        // Add any additional options here
    });
    newRow.insertAfter($(cell).closest('tr'));
}

async function addTopic(cell) {
    const urlParams = new URLSearchParams(window.location.search);
    const protokolId = urlParams.get('protokol_id');

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
    const urlParams = new URLSearchParams(window.location.search);
    const protokolId = urlParams.get('protokol_id');

    var selectedOption = newRow;
    var content = "content";
    var responsible = "responsible";
    var ajaxData = {
        agendaId: protokolId,
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