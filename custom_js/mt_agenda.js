src = "https://cdn.jsdelivr.net/npm/flatpickr"
src = "https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"
$(document).ready(function () {
    $('#agendaTable').hide();
    //showTable();
    
    // Check if an agenda is selected and show the table if true
    var selectedAgendaId = $('#agendaSelect').val();
    if (selectedAgendaId) {
        $('#agendaTable').show();
    }

    // When the user selects an agenda from the dropdown
    $('#agendaSelect').change(function () {
        var selectedAgendaId = $(this).val();
        if (selectedAgendaId) {
            // Show the table and reload the page with the selected agenda_id
            $('#agendaTable').show();
            window.location.href = 'mt_agenda.php?agenda_id=' + selectedAgendaId;
        } else {
            // Hide the table if no agenda is selected
            $('#agendaTable').hide();
        }
    });

    // Initial DataTable initialization
    $('#agendaTable').dataTable({
        "order": [],
        "paging": false, // Disable pagination
        "searchable": true,
        "bDestroy": true, // Ignores the error popup (cannot reinitialize), it works even with the error but purely for aesthetic purpose. Might delete later
        "bInfo": false, //removes "Showing entries"
        "layout": {
            "topStart": {
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: 'Agenda_Excel_' + selectedAgendaId,
                        exportOptions: {
                            columns: ':not(:first-child):not(:last-child)', // Exclude the first and last columns
                            format: {
                                body: function(data, row, column, node) {
                                    if (column === 0 || column === 1 || column === 2) {
                                        var cleanText = $("<div>").html(data).text().trim();
                                        console.log("Cleaned text for column", column, ":", cleanText); // Debug statement
                                        return cleanText;
                                    }

                                    var firstColumnText = $($.parseHTML($('td', node.parentNode).eq(1).html())).text().trim();
                                    console.log("First column text:", firstColumnText); // Debug statement

                                    if (column === 3) {
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
                                    if (column === 4) {
                                        if (firstColumnText.includes("Topic")) {
                                            var start = $(node).find('input[data-column="start"]').data('start-value');
                                            return `${start}`;
                                        }
                                    }
                                    if (column === 5) {
                                        if (firstColumnText.includes("Topic")) {
                                            var duration = $(node).find('input[data-column="duration"]').data('duration-value');
                                            return `${duration}`;
                                        }
                                    }
                                    if (column === 5) {
                                        if (firstColumnText.includes("Break")) {
                                            var duration = $(node).find('input[data-column="duration"]').data('duration-value');
                                            return `${duration}`;
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
                                    $(this).attr('width', 65);
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
                        filename: 'Agenda_PDF_' + selectedAgendaId,
                        exportOptions: {
                            columns: ':not(:first-child):not(:last-child)', // Exclude the first and last columns
                            format: {
                                body: function(data, row, column, node) {
                                    if (column === 0 || column === 1 || column === 2) {
                                        var cleanText = $("<div>").html(data).text().trim();
                                        console.log("Cleaned text for column", column, ":", cleanText); // Debug statement
                                        return cleanText;
                                    }

                                    var firstColumnText = $($.parseHTML($('td', node.parentNode).eq(1).html())).text().trim();
                                    console.log("First column text:", firstColumnText); // Debug statement

                                    if (column === 3) {
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
                                    if (column === 4) {
                                        if (firstColumnText.includes("Topic")) {
                                            var start = $(node).find('input[data-column="start"]').data('start-value');
                                            return `${start}`;
                                        }
                                    }
                                    if (column === 5) {
                                        if (firstColumnText.includes("Topic")) {
                                            var duration = $(node).find('input[data-column="duration"]').data('duration-value');
                                            return `${duration}`;
                                        }
                                    }
                                    if (column === 5) {
                                        if (firstColumnText.includes("Break")) {
                                            var duration = $(node).find('input[data-column="duration"]').data('duration-value');
                                            return `${duration}`;
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

        if (newAgendaName.trim() === '' || newAgendaDate.trim() === '') {
            alert('Please provide both agenda name and date.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'createAgenda.php',
            data: { agenda_name: newAgendaName, agenda_date: newAgendaDate },
            success: function (response) {
                var parsedResponse = JSON.parse(response);
                var newAgendaId = parsedResponse.agenda_id; // Assuming 'response' is the agenda_id returned from PHP
                // Redirect to the new agenda page
                window.location.href = 'mt_agenda.php?agenda_id=' + newAgendaId;
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

    // Filter out the change request on the X button
    $(document).on('click', '#unselectFilterBtn', function () {
        var $row = $(this).closest('tr');
        var title = $(this).closest('tr').data('title'); // Assuming the title is in the data-title attribute
        var agendaId = $('#agendaSelect').val(); // Get the selected agenda_id
        if (agendaId) {
            unselectFilter(title, agendaId);
        } else {
            alert("Please select or create an agenda to continue.");
        }

        function unselectFilter(title, agendaId) {
            $.ajax({
                type: "POST",
                url: "actions.php", // Your PHP script to handle the data
                data: { title: title, agenda_id: agendaId, action: 'unselect' },
                success: function (response) {
                    console.log(response); // Handle success response
                    $row.remove();
                    //location.reload();
                },
                error: function (xhr, status, error) {
                    console.error("An error occurred: " + status + " " + error);
                }
            });
        }
    });
});

$(document).ready(function () {
    $(document).on('blur', 'td[contenteditable=true]', function () {
        var $cell = $(this);
        var newValue = $cell.text();
        var rowId = $cell.closest('tr').attr('id');  // Use .attr('id') to get the row's ID attribute
        var cellIndex = $cell.index();
        var type = $cell.closest('tr').data('type');
        var columnName = (cellIndex === 2) ? 'name' : 'responsible';

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

// Initialize flatpickr on document ready
$(document).ready(function () {
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
    });
});

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
        var cells = currentRow.find('td:eq(1)');
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
            <td></td>
            <td class="task-row"><strong>Task</strong> <input type='hidden' class='task-id' value="${lastTask}"></td>
            <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid orange; max-width: 200px;"></td>
            <td style="background-color: #212529; width: 300px;">
                <input class="editabletasktopic-cell" data-column="responsible" type="text" style="background-color: #212529; border: 1px solid orange; width: 100%; color: grey;" placeholder="Enter responsible person">
                <br><br>
                <div class="flex-container">
                    <input class="editabletasktopic-cell new-datepicker-${lastTask}" data-column="deadline" type="text" style="background-color: #212529; border: 1px solid orange; width: 70%;" value="" placeholder="Select date">
                    <button class="asap-button" data-task-id="${lastTask}" style="width: 30%; color: white;">ASAP</button>
                </div>
            </td>
            <td style="width: 0px;"></td> <!-- Empty cell for Start column -->
            <td style="width: 0px;"></td> <!-- Empty cell for Duration column -->
            <td>
                <div class="button-container">
                    <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                    <div class="dropdown-menu">
                    <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                        <button class="dropdown-item" onclick="addTask(this)">Task</button>
                        <button class="dropdown-item" onclick="addBreak(this)">Break</button>

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
    button.click(function() {
        var $this = $(this);
        var datepicker = $this.closest('.flex-container').find('input[type="text"]');
        var isASAP = $this.text() === 'ASAP';

        if (isASAP) {
            $this.css('color', 'red');
            $this.text('ASAP');
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
            <td></td>
            <td class="topic-row"><strong>Topic</strong> <input type='hidden' class='topic-id' value="${lastTopic}"> </td>
                <td class="editabletasktopic-cell" contenteditable="true" style="border: 1px solid #dfbaff;"></td>
                <td class="editabletasktopic-cell" data-column="responsible" contenteditable="true" style="border: 1px solid #dfbaff;"></td>
                <td class="editabletasktopic-cell" style="border: 1px solid #dfbaff;">
                    <input type="text" class="timepicker" data-column="start" data-topic-id="${lastTopic}" placeholder="HH:MM" style="width: 80px;">
                </td> <!-- New Start column -->
                <td class="editabletasktopic-cell" style="border: 1px solid #dfbaff;">
                    <input type="text" class="duration-input"  data-column="duration" data-topic-id="${lastTopic}" placeholder="Duration (minutes)" style="width: 120px;">
                </td> <!-- New Duration column -->
                <td>
                    <div class="button-container">
                        <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                        <div class="dropdown-menu">
                        <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                            <button class="dropdown-item" onclick="addTask(this)">Task</button>
                            <button class="dropdown-item" onclick="addBreak(this)">Break</button>

                        </div>
                        <button class="button-12 deleteRow" role="button">-</button>
                        <button data-bs-toggle="modal" data-bs-target="#forwardModal" data-id="${lastTopic}" class="button-12 forwardTopicBtns" role="button">→</button>
                    </div>
                </td>
            </tr>
        `);
        
        newRow.insertAfter($(cell).closest('tr'));

        // Initialize flatpickr for the new datepicker and timepicker inputs
        flatpickr(newRow.find('.timepicker'), {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            onClose: function(selectedDates, dateStr, instance) {
                var topicId = instance.input.dataset.topicId;
                // Save the start time via AJAX
                updateStartTime(topicId, dateStr);
            }
        });

        $(document).on('blur', '.duration-input', function() {
            var $this = $(this);
            var $parent = $this.closest('tr'); // Adjust the selector to target the correct parent
            var topicId = $parent.data('topic-id'); // Assuming the topicId is stored on the parent
            var duration = $this.val();
            console.log("Topic ID:", topicId);
            console.log("Duration:", duration);
        
            if (topicId !== undefined && topicId !== '') {
                updateDuration(topicId, duration);
            } else {
                console.error("Topic ID is missing");
            }
        });
        
        
        

    } catch (error) {
        console.error('Error parsing JSON:', error);
        return;
    }
}


function saveToDatabase(newRow, gft, project, topic) {
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

// FORWARD TASK/TOPIC
document.addEventListener('DOMContentLoaded', function () {
    var forwardTaskBtns = document.querySelectorAll('.forwardTaskBtns');
    var forwardTopicBtns = document.querySelectorAll('.forwardTopicBtns');
    var forwardModal = document.getElementById('forwardModal');
    var sendTaskBtn = document.getElementById('sendTaskBtn');
    var createAgendaConfirmWithTaskBtn = document.getElementById('createAgendaConfirmWithTaskBtn');

    forwardTaskBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var taskId = this.getAttribute('data-id');
            forwardModal.setAttribute('data-task-id', taskId);

            var modalTitle = forwardModal.querySelector('.modal-title');
            modalTitle.textContent = 'Forward Task ID: ' + taskId;
        });
    });

    forwardTopicBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var topicId = this.getAttribute('data-id');
            forwardModal.setAttribute('data-topic-id', topicId);

            var modalTitle = forwardModal.querySelector('.modal-title');
            modalTitle.textContent = 'Forward Topic ID: ' + topicId;
        });
    });

    sendTaskBtn.addEventListener('click', function () {
        console.log("Send button clicked");
        var taskId = forwardModal.getAttribute('data-task-id');
        var topicId = forwardModal.getAttribute('data-topic-id');
        var selectedAgendaId = document.getElementById('agendaSelectTask').value;
        console.log('Task ID:', taskId);
        console.log('Topic ID:', topicId);
        console.log('Selected Agenda ID:', selectedAgendaId);

        var data = {};
        if (taskId) {
            data = {
                task_id: taskId,
                agenda_id: selectedAgendaId
            };
        } else {
            data = {
                topic_id: topicId,
                agenda_id: selectedAgendaId
            };
        }

        console.log('Data to send:', data);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'forwardtask.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Task successfully copied to the agenda');
                    location.reload();
                } else {
                    console.error('Failed to copy task to the agenda', xhr.status, xhr.responseText);
                }
            }
        };
        xhr.send(JSON.stringify(data));
    });

    createAgendaConfirmWithTaskBtn.addEventListener('click', function () {
        var newAgendaName = $('#newagendaDate').val();
        var newAgendaDate = $('#newagendaDate').val();

        $.ajax({
            type: 'POST',
            url: 'createAgenda.php',
            data: {
                agenda_name: newAgendaName,
                agenda_date: newAgendaDate
            },
            success: function (response) {
                var parsedResponse = JSON.parse(response);
                var newAgendaId = parsedResponse.agenda_id; // Extract the agenda_id from the JSON response
                console.log('New Agenda ID:', newAgendaId);

                var taskId = forwardModal.getAttribute('data-task-id');
                var topicId = forwardModal.getAttribute('data-topic-id');
                var data = {};

                if (taskId) {
                    data = {
                        task_id: taskId,
                        agenda_id: newAgendaId
                    };
                } else {
                    data = {
                        topic_id: topicId,
                        agenda_id: newAgendaId
                    };
                }

                console.log('Data to send:', data);

                $.ajax({
                    type: 'POST',
                    url: 'forwardtask.php',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        console.log('Task successfully copied to the new agenda');
                        window.location.href = 'mt_agenda.php?agenda_id=' + newAgendaId;
                    },
                    error: function (xhr, status, error) {
                        console.error('Failed to copy task to the new agenda', xhr.status, xhr.responseText);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});

// Delete mt agenda
$(document).ready(function() {
    let deleteAgendaFocused = false;

    // Track when the deleteAgendaSelect is focused
    $('#deleteAgendaSelect').on('click', function () {
        deleteAgendaFocused = true;
    });

    // Add event listener to handle clicks outside the deleteAgendaSelect
    $(document).on('click', function (event) {
        if (deleteAgendaFocused && !$(event.target).closest('#deleteAgendaSelect').length) {
            deleteAgendaFocused = false;
            var selectedAgendaIds = $('#deleteAgendaSelect').val();
            if (selectedAgendaIds.length > 0 && confirm('Are you sure you want to delete the selected agendas?')) {
                deleteSelectedAgendas(selectedAgendaIds);
            }
            resetDeleteAgendaPlaceholder();
        }
    });

    function deleteSelectedAgendas(selectedAgendaIds) {
        $.ajax({
            type: "POST",
            url: "deleteAgenda.php",
            data: { agenda_ids: selectedAgendaIds },
            success: function(response) {
                var parsedResponse = JSON.parse(response);
                if (parsedResponse.success) {
                    alert('Agendas deleted successfully.');
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Error deleting agendas: ' + parsedResponse.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + status + " " + error);
            }
        });
    }

    function resetDeleteAgendaPlaceholder() {
        $('#deleteAgendaSelect').val(null).trigger('change');
    }
});

//Timepicker
document.addEventListener('DOMContentLoaded', function () {
    flatpickr(".timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
});

// Minutepicker
document.addEventListener('DOMContentLoaded', function () {
    const durationSelects = document.querySelectorAll('.duration-select');
    durationSelects.forEach(select => {
        select.addEventListener('change', function () {
            console.log("Duration changed to: " + this.value + " minutes");
        });
    });
});

$(document).ready(function() {
    $('.timepicker').each(function() {
        var inputElement = this; // Reference to the input element for later use
        flatpickr(inputElement, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            onClose: function(selectedDates, dateStr, instance) {
                var topicId = $(inputElement).data('topic-id');
                if (dateStr !== "") {
                    $.ajax({
                        url: 'update_start_time.php', // Endpoint for updating start time
                        type: 'POST',
                        data: {
                            topic_id: topicId,
                            start: dateStr
                        },
                        success: function(response) {
                            console.log('Start time updated successfully:', response);
                            //alert('Start time saved successfully!');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to update start time:', status, error);
                            alert('Failed to save start time.');
                        }
                    });
                }
            }
        });
    });
});

$(document).ready(function() {
    // Save duration when input loses focus
    $('body').on('blur', '.duration-input', function() {
        var $this = $(this);
        var topicId = $this.data('topic-id');  // Retrieves the topic-id from the <tr> element
        var duration = $this.val();
        console.log(topicId);  // Fixed variable name for consistency
        console.log(duration);
        
        if (duration.trim() !== '') {  // Ensure non-empty input before sending
            $.ajax({
                url: 'update_duration.php',
                type: 'POST',
                data: {
                    topic_id: topicId,
                    duration: duration
                },
                success: function(response) {
                    // Optionally handle response data or notify the user of success here
                    console.log('Duration saved successfully!');
                },
                error: function(xhr, status, error) {
                    alert('Failed to save duration. Error: ' + error);
                }
            });
        }
    });
});


function updateStartTime(topicId, startTime) {
    $.ajax({
        url: 'update_start_time.php',
        type: 'POST',
        data: { topic_id: topicId, start: startTime },
        success: function(response) {
            console.log('Start time updated successfully:', response);
            //alert('Start time saved successfully!');
        },
        error: function(xhr, status, error) {
            console.error('Failed to update start time:', status, error);
            alert('Failed to save start time.');
        }
    });
}

function updateDuration(topicId, duration) {
    alert(topicId)
    alert(duration)

    $.ajax({
        url: 'update_duration.php',
        type: 'POST',
        data: { topic_id: topicId, duration: duration },
        success: function(response) {
            console.log('Duration updated successfully:', response);
            //alert('Duration saved successfully!');
        },
        error: function(xhr, status, error) {
            console.error('Failed to update duration:', status, error);
            alert('Failed to save duration.');
        }
    });
}
$(document).ready(function() {
    // Assuming the DataTable is stored in a variable 'agendaTable' from your initialization code.
    var agendaTable = $('#agendaTable').DataTable();

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

$(document).ready(function() {
    $('.order-input').change(function() {
        var gftId = $(this).data('gft-id');
        var orderValue = $(this).val();
        var agendaId = $('#agendaSelect').val();

        $.ajax({
            type: 'POST',
            url: 'save_gft_order.php',
            data: {
                gft_id: gftId,
                order_value: orderValue,
                agenda_id: agendaId
            },
            success: function(response) {
            },
            error: function(xhr, status, error) {
                alert('Failed to update order: ' + error);
            }
        });
    });
});













async function addBreak(cell) {
    var protokolId = $('#agendaSelect').val(); // Get the selected protokol_id
    console.log('Add Break button clicked, protokolId:', protokolId);

    // Ensure addNewRow completes before proceeding
    await addNewRow("Break", cell, protokolId);

    $.ajax({
        type: 'POST',
        url: 'add_break.php',
        data: {
            agenda_id: protokolId,
            gft: getGFT(cell),
            cr: getCR(cell)
        },
        success: function(response) {
            if (response !== 'error') {
                var newBreakId = response;

                var newRow = $(`
                    <tr id="${newBreakId}" data-type="break" data-id="${newBreakId}">
                        <td></td> <!-- Order Input -->
                        <td class="break-row"><strong>Break</strong> <input type='hidden' class='break-id' value="${newBreakId}"></td>
                        <td></td> <!-- Description -->
                        <td></td> <!-- Responsible -->
                        <td></td> <!-- Start -->
                        <td class="editabletasktopic-cell" style="border: 1px solid #00FFFF;">
                            <input type="number" class="duration-input" data-break-id="${newBreakId}" placeholder="minutes" style="width: 100%;">
                        </td> <!-- Duration -->
                        <td>
                            <div class="button-container">
                                <button class="button-12 dropdown-toggle" onclick="toggleDropdown(this)">+</button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" onclick="addTask(this)">Task</button>
                                    <button class="dropdown-item" onclick="addTopic(this)">Topic</button>
                                    <button class="dropdown-item" onclick="addBreak(this)">Break</button>
                                </div>
                                <button class="button-12 deleteRow" role="button">-</button>
                            </div>
                        </td> <!-- Actions -->
                    </tr>
                `);
                
                newRow.insertAfter($(cell).closest('tr'));
                
                // Attach blur event to save duration on change
                newRow.find('.duration-input').on('blur', function() {
                    var breakId = $(this).data('break-id');
                    var duration = $(this).val();
                    updateBreakDuration(breakId, duration);
                });
            } else {
                alert('Failed to add break');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to add break:', status, error);
        }
    });
}

// Function to update the break duration
function updateBreakDuration(breakId, duration) {
    $.ajax({
        url: 'update_break_duration.php',
        type: 'POST',
        data: {
            break_id: breakId,
            duration: duration
        },
        success: function(response) {
            console.log('Break duration updated successfully:', response);
        },
        error: function(xhr, status, error) {
            console.error('Failed to update break duration:', status, error);
        }
    });
}

// Helper function to get the GFT value
function getGFT(cell) {
    var gft = "";
    var currentRow = $(cell).closest('tr');
    while (currentRow.length > 0) {
        var cells = currentRow.find('td:eq(1)');
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("GFT")) {
            var gftId = cells.find('.gft-id').val();
            gft = gftId ? gftId : cellContent;
            break;
        }
        currentRow = currentRow.prev();
    }
    return gft;
}

// Helper function to get the CR value
function getCR(cell) {
    var cr = "";
    var currentRow = $(cell).closest('tr');
    while (currentRow.length > 0) {
        var cells = currentRow.find('td:eq(1)');
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("CH")) {
            var changeRequestId = cells.find('.change-request-id').val();
            cr = changeRequestId ? changeRequestId : cellContent;
            break;
        }
        currentRow = currentRow.prev();
    }
    return cr;
}
