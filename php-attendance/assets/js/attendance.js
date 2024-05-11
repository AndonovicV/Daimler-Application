$(document).ready(function(){
    checkAll_count()

    $('#mdt_id, #meeting_date').change(function(e){
        var mdt_id = $('#mdt_id').val()
        var meeting_date = $('#meeting_date').val()
        location.replace(`./?page=attendance&mdt_id=${mdt_id}&meeting_date=${meeting_date}`)
    })
    $('.status_check').change(function(){
        var member_id = $(this)[0].dataset?.id
        var isChecked = $(this).is(":checked")
        if(isChecked === true){
            $(`.status_check[data-id='${member_id}']`).prop("checked", false)
            $(this).prop("checked", true)
        }
        checkAll_count()
    })
    $('.checkAll').change(function(){
        var _this = $(this)
        var isChecked = $(this).is(":checked")
        var id = $(this).attr('id')
        if(isChecked === true){
            $('.checkAll').each(function(){
                if($(this).attr('id') != id&& $(this).is(":checked") == true){
                    $(this).prop("checked", false)
                }
            })
            $('.status_check').prop('checked', false)
            if(id == 'PCheckAll'){
                $('.status_check[value="1"]').prop('checked', true) 
            }else if(id == 'ACheckAll'){
                $('.status_check[value="2"]').prop('checked', true) 
            }else if(id == 'SCheckAll'){
                $('.status_check[value="3"]').prop('checked', true) 
            }
            // else if(id == 'HCheckAll'){
            //     $('.status_check[value="4"]').prop('checked', true) 
            // }
        }else{
            if(id == 'PCheckAll'){
                $('.status_check[value="1"]').prop('checked', false) 
            }else if(id == 'ACheckAll'){
                $('.status_check[value="2"]').prop('checked', false) 
            }else if(id == 'SCheckAll'){
                $('.status_check[value="3"]').prop('checked', false) 
            }
            // else if(id == 'HCheckAll'){
            //     $('.status_check[value="4"]').prop('checked', false) 
            // }
        }
    })
    $('#manage-attendance').submit(function(e){
        e.preventDefault()
        start_loader()
        var _this = $(this)
        $('#attendance-tbl .member-row').each(function(){
            var has_checks = $(this).find('.status_check:checked').length
            if(has_checks < 1){
                var name = $(this).find('td').first().text() || "";
                    name = String(name).trim();
                console.log(name)
                alert(`${name}'s attendance is not yet marked!`);
                end_loader()
                return false;
            }
        })
        $.ajax({
            url:'./ajax-api.php?action=save_attendance',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'JSON',
            error: (err) => {
                console.error(err)
                alert("An error occurred while saving the data. kindly reload this page.")
                end_loader();
            },
            success: function(resp){
                if(resp?.status == "success"){
                    location.reload()
                }else if(resp?.status == "error" && resp?.msg != ""){
                    var fd = $(flashdataHTML).clone()
                   fd.addClass('flashdata-danger')
                   fd.find('.flashdata-msg').html(resp.msg)
                    $('#msg').html(fd)
                    $('html, body').scrollTop(0)
                }else{
                    alert("An error occurred while saving the data. kindly reload this page.")
                }
                end_loader();
            }
        })
    })
})

function checkAll_count(){
    var statuses = {'PCheckAll': 1, 'ACheckAll': 2, 'SCheckAll': 3}
    $('.checkAll').each(function(){
        var id = $(this).attr('id')
        var checkedCount = $(`.status_check[value="${statuses[id]}"]:checked`).length
        var totalCount = $(`.status_check[value="${statuses[id]}"]`).length
        if(totalCount != checkedCount){
            $(this).prop('checked', false)
        }else{
            $(`#${id}`).prop('checked', true)
        }
    })
}