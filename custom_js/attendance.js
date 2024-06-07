$(document).ready(function () {
    $('#protokolSelect').on('change', function () {
        var agendaId = $(this).val();
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
});