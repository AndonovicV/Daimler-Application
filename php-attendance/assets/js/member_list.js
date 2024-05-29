$(document).ready(function(){
    $('#add_member').click(function(e){
        e.preventDefault()
        open_modal('member_form.php', `<?= isset($id) ? "Add New member" : "Update member" ?>`)
    })
    $('.edit_member').click(function(e){
        e.preventDefault()
        var id = $(this)[0].dataset?.id || ''
        open_modal('member_form.php', `<?= isset($id) ? "Create New member" : "Update member" ?>`, {id: id})
    })
    $('.delete_member').click(function(e){
        e.preventDefault()
        var id = $(this)[0].dataset?.id || ''
        start_loader()
        if(confirm(`Are you sure to delete the selected member? This action cannot be undone.`) == true){
            $.ajax({
                url: "./ajax-api.php?action=delete_member",
                method: "POST",
                data: { id : id},
                dataType: 'JSON',
                error: (error) => {
                    console.error(error)
                    alert('An error occurred.')
                },
                success:function(resp){
                    if(resp?.status != '')
                        location.reload();
                    else
                        end_loader();
                }
            })
        }else{
            end_loader();
        }
    })
})