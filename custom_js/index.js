$(document).ready(function () {
    document.getElementById('agendaSelect').addEventListener('change', function () {
        var selectedAgendaId = this.value;
        $.ajax({
            type: 'POST',
            url: 'actions.php', // Create this PHP file to handle the request
            data: { selectedAgendaId: selectedAgendaId },
            success: function (response) {
                $('#personalTaskLabel').text(response);
            }
        });
    });
});