<?php
session_start();
require_once(realpath(__DIR__.'/../classes/actions.class.php'));
$actionClass = new Actions();
if(isset($_POST['id'])){
  $member = $actionClass->get_member($_POST['id']);
  extract($member);
}
$classList = $actionClass->list_mdt();
?>
<div class="container-fluid">
    <form id="member-form" method="POST">
      <input type="hidden" name="id" value="<?= $id ?? "" ?>">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <label for="mdt_id" class="form-label">Module Team Name & Subject</label>
                    <select type="text" class="form-select" id="mdt_id" name="mdt_id" required="required">
                      <option value="" <?= !isset($id) ? "selected" : "" ?> disabled> -- Select Module Team Here -- </option>
                      <?php if(!empty($classList) && is_array($classList)): ?>
                      <?php foreach($classList as $row): ?>
                        <option value="<?= $row['id'] ?>" <?= (isset($mdt_id) && $mdt_id == $row['id']) ? "selected" : "" ?>><?= $row['name'] ?></option>
                      <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Member Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $name ?? "" ?>" required="required">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
  $('#member-form').submit(function(e){
    e.preventDefault()
    var _this = $(this)
    start_loader();
    $(uniModal).find('flashdata').remove()
    var flashData = $('<div>')
    flashData.addClass('flashdata mb-3')
    flashData.html(`<div class="d-flex w-100 align-items-center flex-wrap">
                      <div class="col-11 flashdata-msg"></div>
                      <div class="col-1 text-center">
                          <a href="javascript:void(0)" onclick="this.closest('.flashdata').remove()" class="flashdata-close"><i class="far fa-times-circle"></i></a>
                      </div>
              </div>`);
    $.ajax({
      url: "ajax-api.php?action=save_member",
      method: "POST",
      data: $(this).serialize(),
      dataType:'JSON',
      error: (err)=>{
        flashData.find('.flashdata-msg').text(`An error occured!`)
        flashData.addClass('flashdata-danger')
        _this.prepend(flashData)
        end_loader();
        console.warn(err)
      },
      success: function(resp){
        if(resp?.status == 'success'){
          location.reload()
        }else{
          if(resp?.msg != ''){
            flashData.find('.flashdata-msg').text(`${resp?.msg}`)
            flashData.addClass('flashdata-danger')
            _this.prepend(flashData)
            end_loader();
          }
        }
      }
    })
  })
</script>