$(document).ready(function () {
    $('#protokolTable').hide();
    //showTable();

    // Check if an agenda is selected and show the table if true
    var selectedAgendaId = $('#protokolSelect').val();
    if (selectedAgendaId) {
        $('#protokolTable').show();
    }

    // When the user selects an agenda from the dropdown
    $('#agendaSelect').change(function () {
        var selectedAgendaId = $(this).val();
        if (selectedAgendaId) {
            // Show the table and reload the page with the selected agenda_id
            $('#protokolTable').show();
            window.location.href = 'mt_agenda.php?agenda_id=' + selectedAgendaId;
        } else {
            // Hide the table if no agenda is selected
            $('#protokolTable').hide();
        }
    });
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
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: selectedAgendaId,
                        exportOptions: {
                            columns: ':not(:last-child)', // Exclude the last column
                            format: {
                                body: function(data, row, column, node) {
                                    if (column === 0 || column === 1) {
                                        var cleanText = $("<div>").html(data).text().trim();
                                        console.log("Cleaned text for column", column, ":", cleanText); // Debug statement
                                        return cleanText;
                                    }
    
                                    var firstColumnText = $($.parseHTML($('td', node.parentNode).eq(0).html())).text().trim();
                                    console.log("First column text:", firstColumnText); // Debug statement
    
                                    if (column === 2) {
                                        if (firstColumnText.includes("Task")) {
                                            var responsible = $(node).find('input[data-column="responsible"]').val();
                                            var deadline = $(node).find('input[data-column="deadline"]').val();
                                            var asapStatus = $(node).find('.asap-button').css('color') === 'rgb(255, 0, 0)' ? 'Yes' : 'No'; // Check for red color
                                            return `Responsible: ${responsible}, Deadline: ${deadline}, ASAP: ${asapStatus}`;
                                        } else if (firstColumnText.includes("Topic")) {
                                            var responsible = $(node).text().trim();
                                            return `Responsible: ${responsible}`;
                                        }
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
    
                            $('col', sheet).each(function (index) {
                                if (index === 1) {
                                    $(this).attr('width', 100);
                                    $(this).attr('customWidth', 1);
                                } else if (index === 2) {
                                    $(this).attr('width', 75);
                                    $(this).attr('customWidth', 1);
                                }
                            });
    
                            var fontIndex = $('fonts font', sheet).length;
                            var boldFont = '<font><b/><sz val="11"/><color rgb="000000"/><name val="Calibri"/></font>';
                            $('fonts', sheet).append(boldFont);
                            $('fonts', sheet).attr('count', fontIndex + 1);
    
                            var styleBase = $('cellXfs xf', sheet).length;
                            var boldStyle = `<xf numFmtId="0" fontId="${fontIndex}" fillId="0" borderId="0" xfId="0" applyFont="1"/>`;
                            $('cellXfs', sheet).append(boldStyle);
                            $('cellXfs', sheet).attr('count', styleBase + 1);
    
                            $('row', sheet).each(function () {
                                var row = $(this);
                                var firstCellText = $($('c t', row).first()).text().trim();
                                console.log("First cell text in row:", firstCellText); // Debug statement
                                if (firstCellText.includes("GFT")) {
                                    $('c', row).first().attr('s', styleBase);
                                }
                            });
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        filename: 'Protokol_PDF_' + selectedAgendaId,
                        exportOptions: {
                            columns: ':not(:last-child)', // Exclude the last column
                            format: {
                                    body: function(data, row, column, node) {
                                        var $node = $(node);
                                        var $currentRow = $node.closest('tr');
                                        var firstColumnText = $($.parseHTML($('td', node.parentNode).eq(1).html())).text().trim();
    
                                        // Check if the next row contains "No change requests for GFT"
                                        var $nextRow = $currentRow.next('tr');
                                        if ($nextRow.length > 0) {
                                            var nextFirstColumnText = $($.parseHTML($('td', $nextRow.get(0)).eq(1).html())).text().trim();
                                            if (nextFirstColumnText.includes("No change requests for GFT")) {
                                                $currentRow.addClass('exclude-row');
                                                $nextRow.addClass('exclude-row');
                                                return '';
                                            }
                                        }
    
                                        // If this row is marked for exclusion, return an empty string
                                        if ($currentRow.hasClass('exclude-row')) {
                                            return '';
                                        }
    
                                        if (column === 0 || column === 1) {
                                            var cleanText = $("<div>").html(data).text().trim();
                                            //console.log("Cleaned text for column", column, ":", cleanText); // Debug statement
                                            return cleanText;
                                        }
        
                                        var firstColumnText = $($.parseHTML($('td', node.parentNode).eq(0).html())).text().trim();
                                        //console.log("First column text:", firstColumnText); // Debug statement
        
                                        if (column === 2) {
                                            if (firstColumnText.includes("Task")) {
                                                var responsible = $(node).find('input[data-column="responsible"]').val();
                                                var deadline = $(node).find('input[data-column="deadline"]').val();
                                                var asapStatus = $(node).find('.asap-button').css('color') === 'rgb(255, 0, 0)' ? 'Yes' : 'No'; // Check for red color
                                                return `Responsible: ${responsible} | Deadline: ${deadline} | ASAP: ${asapStatus}`;
                                            } else if (firstColumnText.includes("Topic")) {
                                                var responsible = $(node).text().trim();
                                                return `Responsible: ${responsible}`;
                                            }
                                        }
                                        return data;
                                    }
                            },
                            customize: function(doc) {
                                var taskRowStyle = {
                                    fillColor: [255, 230, 230] // Light red background for tasks
                                };
                                var topicRowStyle = {
                                    fillColor: [230, 230, 255] // Light blue background for topics
                                };
    
                                for (var i = 1; i < doc.content[1].table.body.length; i++) {
                                    var row = doc.content[1].table.body[i];
                                    var firstCellText = row[0].text.trim();
                                    //console.log("First cell text in PDF row:", firstCellText); // Debug statement
    
                                    if (firstCellText.includes("GFT")) {
                                        row[0].style = { bold: true };
                                    }
                                    if (firstCellText.includes("Task")) {
                                        row.forEach(cell => {
                                            cell.fillColor = taskRowStyle.fillColor;
                                        });
                                    } else if (firstCellText.includes("Topic")) {
                                        row.forEach(cell => {
                                            cell.fillColor = topicRowStyle.fillColor;
                                        });
                                    }
                                }
    
                                // Set custom column widths
                                var colWidths = ['*', 100, 75]; // Adjust as necessary for your table
                                doc.content[1].table.widths = colWidths;
                            }
                        }
                    }
                ]
            }
        },
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

    // Triggers the virtual select
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

    // Warn if user selects filter before choosing protokol
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

    $(document).on('click', '#unselectFilterBtn', function () {
        var $row = $(this).closest('tr');
        var title = $row.find('td:eq(1)').text(); // Assuming the title is in the second column
        var agendaId = $('#protokolSelect').val(); // Get the currently selected agenda ID

        if (!agendaId) {
            alert("Please select an agenda first.");
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'actions.php',
            data: {
                title: title,
                agenda_id: agendaId,
                action: 'unselect'
            },
            success: function (response) {
                if (response.trim() === 'Success') {

                } else {
                    //This is actualy SUCCESS!
                    // it loads it as an error but it still works.
                    //alert('Failed to unselect the filter. ' + response);
                    $row.remove();
                }
            },
            error: function (xhr, status, error) {
                alert("Error: " + xhr.responseText);
            }
        });
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
        var fieldType = $cell.data('field'); // Read the data-field attribute

        $.ajax({
            url: 'saveContent.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id: Id,
                row_type: rowType,
                content: newValue,
                field_type: fieldType
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

$(document).ready(function () {
    // Load state from localStorage
    $('.asap-button').each(function () {
        var taskId = $(this).data('task-id');
        var isASAP = localStorage.getItem('asap-' + taskId) === 'true';

        if (isASAP) {
            $(this).css('color', 'red');
            $('#datepicker-' + taskId).hide();
        }
    });

    // Toggle ASAP button
    $('.asap-button').click(function () {
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
            success: function (response) {
                console.log('ASAP status updated successfully');
            },
            error: function (xhr, status, error) {
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

// Function to add a new row
async function addNewRow(type, clickedCell) {
    var gft = "";
    var project = "";
    var topic = "";
    var gftFound = false;
    var projectFound = false;
    var topicFound = false;
    var currentRow = $(clickedCell).closest('tr');

    while (currentRow.length > 0 && !(gftFound && projectFound && topicFound)) {
        var cells = currentRow.find('td:eq(0)');
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("GFT") && !gftFound) {
            var gftId = cells.find('.gft-id').val();
            gft = gftId ? gftId : cellContent;
            gftFound = true;
        } else if (cellContent.startsWith("CH") && !projectFound && !gftFound) {
            var changeRequestId = cells.find('.change-request-id').val();
            project = changeRequestId ? changeRequestId : cellContent;
            projectFound = true;
        } else if (cellContent.startsWith("Topic") && !topicFound && !projectFound && !gftFound) {
            var topicId = cells.find('.topic-id').val(); 
            topic = topicId;
            topicFound = true;
        }
        currentRow = currentRow.prev();
    }

    //project = project.substring("title for".length).trim();
    //gft = gft.substring("GFT".length).trim();

    console.log(type, gft, project, topic);

    await saveToDatabase(type, gft, project, topic);
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
    var lastTask = data.last_task_id;

    console.log('Last Task ID:', lastTaskId);
    console.log('Last Information ID:', lastInformationId);
    console.log('Last Assignment ID:', lastAssignmentId);
    console.log('Last Decision ID:', lastDecisionId);

    var newRow = $(`
        <tr id="${lastTask}" data-type="task" data-id="${lastTask}">
            <td class="task-row"><strong>Task</strong> <input type='hidden' class='task-id' value="${lastTask}"></td>
            <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid orange; max-width: 200px;"></td>
            <td style="background-color: #212529; width: 100px;">
                <input class="editabletasktopic-cell" data-column="responsible" type="text" style="background-color: #212529; border: 1px solid orange; width: 100%; color: grey;" placeholder="Enter responsible person">
                <br><br>
                <div class="flex-container">
                    <input class="editabletasktopic-cell new-datepicker-${lastTask}" data-column="deadline" type="text" style="background-color: #212529; border: 1px solid orange; width: 70%;" value="" placeholder="Select date">
                    <button class="asap-button" data-task-id="${lastTask}" style="width: 30%; color: white;">ASAP</button>
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
        <tr id="${lastInformationId}" data-type="I" data-id="${lastInformationId}">
            <td><strong>I</strong></td>
            <td class="editable-cell" data-field='content' contenteditable="true"></td>
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
            <td class="editable-cell" data-field='content' contenteditable="true"></td>
            <td class='editable-cell' data-field='responsible' contenteditable='true'></td> 
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
            <td class="editable-cell" data-field='content' contenteditable="true"></td>
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
    // Insert the new row into the table
    newRow.insertAfter($(cell).closest('tr'));

    // Initialize flatpickr for the new datepicker input
    flatpickr('.new-datepicker-' + lastTask, {
        dateFormat: 'Y-m-d',
    });
    // Initialize the ASAP button functionality for the newly added row
    initializeASAPButton(lastTask);
}
function updateASAPStatus(taskId, status) {
    $.ajax({
        url: 'update_asap_status.php',
        type: 'POST',
        data: { task_id: taskId, asap: status },
        success: function (response) {
            console.log('ASAP status updated successfully');
        },
        error: function (xhr, status, error) {
            console.error('Error updating ASAP status:', error);
        }
    });
}
function initializeASAPButton(taskId) {
    var button = $(`.asap-button[data-task-id="${taskId}"]`);
    button.click(function () {
        var $this = $(this);
        var datepicker = $this.closest('.flex-container').find('input[type="text"]');
        var isASAP = $this.text() === 'ASAP';

        if (isASAP) {
            $this.css('color', 'red');
            $this.text('ASAP'); //IT IS Asap
            datepicker.hide();
            localStorage.setItem('asap-' + taskId, 'true');
            updateASAPStatus(taskId, 1);
        } else {
            $this.css('color', 'white');
            $this.text('ASAP');
            datepicker.show();
            localStorage.setItem('asap-' + taskId, 'false');
            updateASAPStatus(taskId, 0);
        }
    });
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
                <td class="topic-row"><strong>Topic</strong> <input type='hidden' class='topic-id' value="${lastTopic}"> </td>
                <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid  #dfbaff;"></td>
                <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid  #dfbaff;"></td>
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

function saveToDatabase(newRow, gft, project, topic) {
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
        cr: project,
        topic: topic
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


//Delete Protokol
$(document).ready(function() {
    let deleteProtokolFocused = false;

    // Track when the deleteProtokolSelect is focused
    $('#deleteProtokolSelect').on('click', function () {
        deleteProtokolFocused = true;
    });

    // Add event listener to handle clicks outside the deleteProtokolSelect
    $(document).on('click', function (event) {
        if (deleteProtokolFocused && !$(event.target).closest('#deleteProtokolSelect').length) {
            deleteProtokolFocused = false;
            var selectedAgendaIds = $('#deleteProtokolSelect').val();
            if (selectedAgendaIds.length > 0 && confirm('Are you sure you want to delete the selected protocols?')) {
                deleteSelectedProtokols(selectedAgendaIds);
            }
            resetDeleteProtokolPlaceholder();
        }
    });

    function deleteSelectedProtokols(selectedAgendaIds) {
        $.ajax({
            type: "POST",
            url: "deleteAgenda.php",
            data: { agenda_ids: selectedAgendaIds },
            success: function(response) {
                var parsedResponse = JSON.parse(response);
                if (parsedResponse.success) {
                    alert('Protocols deleted successfully.');
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Error deleting protocols: ' + parsedResponse.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + status + " " + error);
            }
        });
    }

    function resetDeleteProtokolPlaceholder() {
        $('#deleteProtokolSelect').val(null).trigger('change');
    }
});

$(document).ready(function() {
    // Assuming the DataTable is stored in a variable 'agendaTable' from your initialization code.
    var agendaTable = $('#protokolTable').DataTable();

    // Function to trigger export buttons based on URL parameter
    function triggerExportButton(index) {
        var button = agendaTable.button(index); // Refer to the buttons by index
        if (button) {
            button.trigger(); // Trigger the specified button
        } else {
            console.log("Button index not valid:", index);
        }
    }

    // Parse URL parameters to determine if an export should be triggered
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('export')) {
        var exportParam = parseInt(urlParams.get('export'), 10);
        if (!isNaN(exportParam)) {
            triggerExportButton(exportParam); // Trigger the button based on the index
        } else {
            console.log("Export parameter is not a number:", urlParams.get('export'));
        }
    }
});

// FORWARD TASK / TOPIC
document.addEventListener('DOMContentLoaded', function() {
    var forwardModal = document.getElementById('forwardModal');
    var sendTaskBtn = document.getElementById('sendTaskBtn');

    // Handle click events for both task and topic forwarding buttons
    document.querySelectorAll('.forwardTaskBtns, .forwardTopicBtns').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var type = this.classList.contains('forwardTaskBtns') ? 'task' : 'topic';

            // Set the data attributes on the modal for later retrieval
            forwardModal.setAttribute('data-id', id);
            forwardModal.setAttribute('data-type', type);

            // Update modal title accordingly
            var modalTitle = forwardModal.querySelector('.modal-title');
            modalTitle.textContent = 'Forward ' + (type.charAt(0).toUpperCase() + type.slice(1)) + ' ID: ' + id;
        });
    });

    sendTaskBtn.addEventListener('click', function() {
        var id = forwardModal.getAttribute('data-id');
        var type = forwardModal.getAttribute('data-type');
        var selectedAgendaId = document.getElementById('agendaSelectTask').value;

        if (id && selectedAgendaId) {
            forwardItem(type, id, selectedAgendaId);
        } else {
            console.error('ID or Selected Agenda ID missing');
        }
    });
});

function forwardItem(type, id, selectedAgendaId) {
    var endpoint = 'forward_task_with_details.php'; // Assuming a single endpoint for forwarding tasks/topics
    var data = {
        agenda_id: selectedAgendaId
    };
    data[type + '_id'] = id; // Dynamically set the appropriate key based on type

    // Perform the AJAX request using jQuery for simplicity and better browser compatibility
    $.ajax({
        type: 'POST',
        url: endpoint,
        data: data,
        success: function(response) {
            console.log(`${type.charAt(0).toUpperCase() + type.slice(1)} and related details forwarded successfully`, response);
            // Implement additional UI feedback here
        },
        error: function(xhr, status, error) {
            console.error(`Failed to forward ${type}`, status, error);
        }
    });
}