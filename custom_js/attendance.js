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

    $('#manage-attendance').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();
        $.ajax({
            url: 'save_attendance.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert('Attendance saved successfully.');
                } else {
                    alert('Error saving attendance.');
                }
            },
            error: function () {
                alert('Error saving attendance.');
            }
        });
    });
});
