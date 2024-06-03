async function addnew(type, cell) {
    //alert("MOEW");
    const urlParams = new URLSearchParams(window.location.search);
    const protokolId = urlParams.get('protokol_id');

    console.log('Add New button clicked, protokolId:', protokolId);

    var clickedCell = $(cell);
    var currentRow = clickedCell.closest('tr');
    var lastTaskId = null;

    while (currentRow.length > 0) {
        var cells = currentRow.find('td:eq(0)'); // Select the first column
        var cellContent = cells.text().trim();
        if (cellContent.startsWith("Task")) {
            console.log(cellContent);
            lastTaskId = currentRow.attr('data-id'); // Get the id from the row
            break;
        }
        currentRow = currentRow.prev();
    }

    if (lastTaskId) {
        var newRow = $(`
            <tr id="task-${lastTaskId}" data-type="${type}" data-id="${lastTaskId}">
                <td><strong>${type}</strong></td>
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
        newRow.insertAfter(clickedCell.closest('tr'));
        saveIAD(lastTaskId, type);
    } else {
        console.error('No previous task ID found.');
    }
}


async function saveIAD(taskId, rowType) {
    const postData = {
        task_id: taskId,
        row_type: rowType,
        content: ""
    };

    try {
        const response = await fetch('protokolspecific.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(postData)
        });

        if (!response.ok) {
            throw new Error('Failed to save content');
        }

        const result = await response.text();
        console.log(result); 

    } catch (error) {
        console.error('Error:', error.message);
    }
}
