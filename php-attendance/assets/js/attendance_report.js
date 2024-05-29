$(document).ready(function(){
    $('#mdt_id, #mdt_month').change(function(e){
        var mdt_id = $('#mdt_id').val()
        var mdt_month = $('#mdt_month').val()
        location.replace(`./?page=attendance_report&mdt_id=${mdt_id}&mdt_month=${mdt_month}`)
    })
})