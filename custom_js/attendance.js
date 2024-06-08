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
            url: 'save_attendance.php',
            type: 'POST',
            data: {
                agenda_id: agendaId,
                'member_id[]': [memberId],
                ['status[' + memberId + ']']: status
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

    $('#attendance-tbl-body').on('change', 'input[type="checkbox"]', function () {
        var agendaId = $('input[name="agenda_id"]').val();
        var memberId = $(this).closest('tr').find('input[name="member_id[]"]').val();
        var status = $(this).is(':checked') ? $(this).val() : 0;

        // Uncheck other checkboxes in the same row
        $(this).closest('tr').find('input[type="checkbox"]').not(this).prop('checked', false);

        updateAttendance(agendaId, memberId, status);
    });
});
