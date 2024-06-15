$(document).ready(function () {
    $('#protokolSelect').on('change', function () {
        var agendaId = $(this).val();
        $('input[name="agenda_id"]').val(agendaId);  // Set agenda_id in the form
        if (agendaId) {
            $.ajax({
                url: 'fetch_attendance.php',
                type: 'GET',
                data: { agenda_id: agendaId },
                success: function (response) {
                    $('#tables-container').show();
                    var data = JSON.parse(response);
                    $('#attendance-tbl-body').html(data.attendance);
                    $('#guest-list-tbl-body').html(data.guest_list);
                },
                error: function () {
                    alert('Error retrieving data.');
                }
            });
        } else {
            $('#tables-container').hide();
        }
    });

    function updateAttendance(agendaId, memberId, status) {
        $.ajax({
            url: 'update_attendance.php',
            type: 'POST',
            data: {
                agenda_id: agendaId,
                member_id: memberId,
                status: status,
                checkbox_name: 'status[' + memberId + ']'
            },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === 'error') {
                    alert('Error updating attendance: ' + data.message);
                }
            },
            error: function () {
                alert('Error updating attendance.');
            }
        });
    }

    function updateGuestAttendance(agendaId, guestId, present) {
        $.ajax({
            url: 'update_attendance.php',
            type: 'POST',
            data: {
                agenda_id: agendaId,
                member_id: guestId, // Using member_id as it is reused in update_attendance.php
                status: present ? 1 : 0,
                checkbox_name: 'present[' + guestId + ']'
            },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === 'error') {
                    alert('Error updating guest attendance: ' + data.message);
                }
            },
            error: function () {
                alert('Error updating guest attendance.');
            }
        });
    }

    $('#attendance-tbl-body').on('change', 'input[type="checkbox"]', function () {
        var agendaId = $('input[name="agenda_id"]').val();
        var memberId = $(this).closest('tr').find('input[name="member_id[]"]').val();
        var status = $(this).is(':checked') ? $(this).val() : 0;

        // Uncheck other checkboxes in the same row
        $(this).closest('tr').find('input[type="checkbox"]').not(this).prop('checked', false);

        updateAttendance(agendaId, memberId, status);
    });

    $('#guest-list-tbl-body').on('change', 'input[type="checkbox"]', function () {
        var agendaId = $('input[name="agenda_id"]').val();
        var guestId = $(this).closest('tr').find('input[name="guest_id"]').val();
        var present = $(this).is(':checked');

        updateGuestAttendance(agendaId, guestId, present);
    });

    $('#attendance-tbl-body, #guest-list-tbl-body').on('dblclick', 'td.editable', function () {
        var $this = $(this);
        var originalText = $this.text();
        var input = $('<input type="text" class="edit-input" />').val(originalText);
        $this.html(input);
        input.focus();

        input.on('blur', function () {
            var newText = $(this).val();
            $this.text(newText);

            var agendaId = $('input[name="agenda_id"]').val();
            var row = $this.closest('tr');
            var guestId = row.find('input[name="guest_id"]').val();
            var department = row.find('.department').text();
            var substitute = row.find('.substitute').text();

            $.ajax({
                url: 'save_attendance.php',
                type: 'POST',
                data: {
                    agenda_id: agendaId,
                    guest_id: guestId,
                    department: department,
                    substitute: substitute
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status === 'error') {
                        alert('Error saving data: ' + data.message);
                        $this.text(originalText); // Revert text on error
                    }
                },
                error: function () {
                    alert('Error saving data.');
                    $this.text(originalText); // Revert text on error
                }
            });
        });
    });

    // Delete guest event
    $('#guest-list-tbl-body').on('click', '#deleteBtnGuest', function () {
        var $row = $(this).closest('tr');
        var guestId = $row.find('input[name="guest_id"]').val();
        var agendaId = $('input[name="agenda_id"]').val();

        if (confirm('Are you sure you want to delete this guest?')) {
            $.ajax({
                url: 'delete_guest.php',
                type: 'POST',
                data: { guest_id: guestId, agenda_id: agendaId },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        $row.remove();
                    } else {
                        alert('Error deleting guest: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error deleting guest.');
                }
            });
        }
    });

    // Open modal for adding new guest
    $('#add_guest').on('click', function () {
        $('#addGuestModal').modal('show');
    });

    // Add guest event
    $('#saveGuestBtn').on('click', function () {
        var guestName = $('#guestNameInput').val();
        var department = $('#guestDepartmentInput').val();
        var substitute = $('#guestSubstituteInput').val();
        var agendaId = $('input[name="agenda_id"]').val();

        if (guestName) {
            $.ajax({
                url: 'add_guest.php',
                type: 'POST',
                data: {
                    agenda_id: agendaId,
                    guest_name: guestName,
                    department: department,
                    substitute: substitute
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        var newRow = `
                            <tr>
                                <td class='guest_name editable' class='px-2 py-1 text-light-emphasis fw-bold'>
                                    <input type='hidden' name='guest_id' value='${data.guest_id}'>
                                    ${data.guest_name}
                                </td>
                                <td class='department editable' class='text-center px-2 py-1 text-light-emphasis'>${data.department}</td>
                                <td class='substitute editable' class='px-2 py-1 text-light'>${data.substitute}</td>
                                <td class='text-center px-2 py-1'>
                                    <input class='form-check-input' type='checkbox' name='present[${data.guest_id}]' id='present_${data.guest_id}' data-status='1'>
                                </td>
                                <td class='text-center px-2 py-1'>
                                    <div class='btn-group' role='group' aria-label='Basic example'>
                                        <button class='btn btn-sm btn-outline-danger rounded-0' type='button' id='deleteBtnGuest' title='Delete'><i class='fas fa-trash'></i></button>
                                    </div>
                                </td>
                            </tr>`;
                        $('#guest-list-tbl-body').append(newRow);
                        $('#addGuestModal').modal('hide');
                    } else {
                        alert('Error adding guest: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error adding guest.');
                }
            });
        } else {
            alert('Please enter a guest name.');
        }
    });

    // Check all present checkboxes in attendance table
    $('#checkAllPresent').on('change', function () {
        var checked = $(this).is(':checked');
        $('#attendance-tbl-body input[type="checkbox"][name*="status"]').each(function () {
            if ($(this).val() == 1) {
                $(this).prop('checked', checked);
                var agendaId = $('input[name="agenda_id"]').val();
                var memberId = $(this).closest('tr').find('input[name="member_id[]"]').val();
                updateAttendance(agendaId, memberId, checked ? 1 : 0);
            }
        });
    });

    // Check all absent checkboxes in attendance table
    $('#checkAllAbsent').on('change', function () {
        var checked = $(this).is(':checked');
        $('#attendance-tbl-body input[type="checkbox"][name*="status"]').each(function () {
            if ($(this).val() == 2) {
                $(this).prop('checked', checked);
                var agendaId = $('input[name="agenda_id"]').val();
                var memberId = $(this).closest('tr').find('input[name="member_id[]"]').val();
                updateAttendance(agendaId, memberId, checked ? 2 : 0);
            }
        });
    });

    // Check all substituted checkboxes in attendance table
    $('#checkAllSubstituted').on('change', function () {
        var checked = $(this).is(':checked');
        $('#attendance-tbl-body input[type="checkbox"][name*="status"]').each(function () {
            if ($(this).val() == 3) {
                $(this).prop('checked', checked);
                var agendaId = $('input[name="agenda_id"]').val();
                var memberId = $(this).closest('tr').find('input[name="member_id[]"]').val();
                updateAttendance(agendaId, memberId, checked ? 3 : 0);
            }
        });
    });

    // Check all present checkboxes in guest list table
    $('#checkAllGuestPresent').on('change', function () {
        var checked = $(this).is(':checked');
        $('#guest-list-tbl-body input[type="checkbox"][name*="present"]').each(function () {
            $(this).prop('checked', checked);
            var agendaId = $('input[name="agenda_id"]').val();
            var guestId = $(this).closest('tr').find('input[name="guest_id"]').val();
            updateGuestAttendance(agendaId, guestId, checked);
        });
    });
});
