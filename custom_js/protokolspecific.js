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
        try {
            const newid = await saveIAD(lastTaskId, type); // Wait for the new ID

            var newRow = $(`
                <tr id="${newid}" data-type="${type}" data-id="${newid}">
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
        } catch (error) {
            console.error('Error:', error.message);
        }
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

        const result = await response.json();
        if (result.status === 'success') {
            console.log('Content saved successfully');
            console.log('New ID:', result.id); // Log the new ID
            return result.id; // Return the new ID
        } else {
            throw new Error(result.message);
        }

    } catch (error) {
        console.error('Error:', error.message);
        return null; // Return null in case of error
    }
}
