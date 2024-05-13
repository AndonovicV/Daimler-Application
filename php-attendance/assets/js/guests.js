$(document).ready(function(){
    $('#add_guest').click(function(e){
        e.preventDefault()
        open_modal('guest_form.php', `<?= isset($id) ? "Add New guest" : "Update guest" ?>`)
    })
    $('.edit_guest').click(function(e){
        e.preventDefault()
        var id = $(this)[0].dataset?.id || ''
        open_modal('guest_form.php', `<?= isset($id) ? "Create New guest" : "Update guest" ?>`, {id: id})
    })
    $('.delete_guest').click(function(e){
        e.preventDefault()
        var id = $(this)[0].dataset?.id || ''
        start_loader()
        if(confirm(`Are you sure to delete the selected guest? This action cannot be undone.`) == true){
            $.ajax({
                url: "./ajax-api.php?action=delete_guest",
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