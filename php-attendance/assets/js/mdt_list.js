$(document).ready(function(){
    $('#add_mdt').click(function(e){
        e.preventDefault()
        open_modal('mdt_form.php', `<?= isset($id) ? "Create New Module Team" : "Update Module Team" ?>`)
    })
    $('.edit_mdt').click(function(e){
        e.preventDefault()
        var id = $(this)[0].dataset?.id || ''
        open_modal('mdt_form.php', `<?= isset($id) ? "Create New Module Team" : "Update Module Team" ?>`, {id: id})
    })
    $('.delete_mdt').click(function(e){
        e.preventDefault()
        var id = $(this)[0].dataset?.id || ''
        start_loader()
        if(confirm(`Are you sure to delete the selected Module Team? This action cannot be undone.`) == true){
            $.ajax({
                url: "./ajax-api.php?action=delete_mdt",
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