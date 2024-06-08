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
});
