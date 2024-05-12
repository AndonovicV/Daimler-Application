const uniModal = document.getElementById('uniModal')
const loaderHTML = `<div id="pre-loader"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><div>`
const flashdataHTML = document.createElement('div');
       flashdataHTML.classList.add('flashdata')
       flashdataHTML.classList.add('mb-3')
       flashdataHTML.innerHTML = `<div class="d-flex w-100 align-items-center flex-wrap">
           <div class="col-11 flashdata-msg"></div>
           <div class="col-1 text-center">
               <a href="javascript:void(0)" onclick="this.closest('.flashdata').remove()" class="flashdata-close"><i class="far fa-times-circle"></i></a>
           </div>
       </div>`;
function start_loader(){
    if($('#pre-loader').length > 0)
        $('#pre-loader').remove()
    $('body').prepend(loaderHTML)
    $('body, html').css('overflow', 'hidden')
}
function end_loader(){
    if($('#pre-loader').length > 0)
        $('#pre-loader').remove()
    $('body, html').css('overflow', 'auto')
}
function open_modal($modalURL="", $modalTitle="", $data = {}, $size = "modal-md"){
    if($modalURL == "")
        return false;
        $(uniModal).find('.modal-dialog').removeClass('modal-sm')
        $(uniModal).find('.modal-dialog').removeClass('modal-md')
        $(uniModal).find('.modal-dialog').removeClass('modal-lg')
        $(uniModal).find('.modal-dialog').removeClass('modal-xl')
        if($size != "")
        $(uniModal).find('.modal-dialog').addClass($size)

        $(uniModal).find('#uniModalLabel').html($modalTitle)

        $(uniModal).find('.modal-body').html(`<div class="container-fluid"><div class="text-center text-dark-emphasis"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div></div></div>`)
        $(uniModal).find('.modal-footer button[type="submit"]').hide()
        $(uniModal).modal('show')
        $.ajax({
            url: `./modals/${$modalURL}`,
            method: 'POST',
            data: $data,
            error: (err) => {
                console.error(err)
                alert("An error occurred while fetching the modal content! Kindly reload this page.")
            },
            success:function(resp){
                $(uniModal).find('.modal-body').html(resp) 
                var form = $(uniModal).find('.modal-body form')[0].getAttribute('id') || "";
                if(form != ""){
                    $(uniModal).find('.modal-footer button[type="submit"]').attr('form', form)
                    $(uniModal).find('.modal-footer button[type="submit"]').show()
                }
            }
        })

}

(function(){
    $(uniModal).on('hidden.bs.modal', function(){
        $(uniModal).find('.modal-body').html('')
        $(uniModal).find('#uniModalLabel').html('')
    })
    $('body').on('scroll', function(){
        console.log('test')
    })
})
