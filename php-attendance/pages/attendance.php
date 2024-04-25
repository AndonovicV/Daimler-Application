<div class="page-title mb-3">Manage Attendance</div>
<hr>
<?php 
// $memberList = $actionClass->list_member();
$mdtList = $actionClass->list_mdt();
$mdt_id = $_GET['mdt_id'] ?? "";
$meeting_date = $_GET['meeting_date'] ?? "";
$memberList = $actionClass->attendanceMembers($mdt_id, $meeting_date);

?>
<!-- <pre>
    <?php print_r($memberList) ?>
</pre> -->
<form action="" id="manage-attendance">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div id="msg"></div>
            <div class="card shadow mb-3">
                <div class="card-body rounded-0">
                    <div class="container-fluid">
                        <div class="row align-items-end">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="mdt_id" class="form-label">Module Team</label>
                                <select name="mdt_id" id="mdt_id" class="form-select" required="required">
                                    <option value="" disabled <?= empty($mdt_id) ? "selected" : "" ?>> -- Select Here -- </option>
                                    <?php if(!empty($mdtList) && is_array($mdtList)): ?>
                                    <?php foreach($mdtList as $row): ?>
                                        <option value="<?= $row['id'] ?>" <?= (isset($mdt_id) && $mdt_id == $row['id']) ? "selected" : "" ?>><?= $row['name'] ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="meeting_date" class="form-label">Date</label>
                                <input type="date" name="meeting_date" id="meeting_date" class="form-control" value="<?= $meeting_date ?? '' ?>" required="required">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(!empty($mdt_id) && !empty($meeting_date)): ?>
            <div class="card shadow mb-3">
                <div class="card-header rounded-0">
                    <div class="card-title">Attendance Sheet</div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="table-responsive">
                            <table id="attendance-tbl" class="table table-bordered">
                                <colgroup>
                                    <col width="40%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%">
                                </colgroup>
                                <thead class="bg-primary">
                                    <tr>
                                        <th class="text-center bg-transparent text-light">Members</th>
                                        <th class="text-center bg-transparent text-light">Present</th>
                                        <th class="text-center bg-transparent text-light">Late</th>
                                        <th class="text-center bg-transparent text-light">Absent</th>
                                        <th class="text-center bg-transparent text-light">Holiday</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-center px-2 py-1 text-dark-emphasis">Check/Uncheck All</th>
                                        <th class="text-center px-2 py-1 text-dark-emphasis">
                                            <div class="form-check d-flex w-100 justify-content-center">
                                                <input class="form-check-input checkAll" type="checkbox" id="PCheckAll">
                                                <label class="form-check-label" for="PCheckAll">
                                                </label>
                                            </div>
                                        </th>
                                        <th class="text-center px-2 py-1 text-dark-emphasis">
                                            <div class="form-check d-flex w-100 justify-content-center">
                                                <input class="form-check-input checkAll" type="checkbox" id="LCheckAll">
                                                <label class="form-check-label" for="LCheckAll">
                                                </label>
                                            </div>
                                        </th>
                                        <th class="text-center px-2 py-1 text-dark-emphasis">
                                            <div class="form-check d-flex w-100 justify-content-center">
                                                <input class="form-check-input checkAll" type="checkbox" id="ACheckAll">
                                                <label class="form-check-label" for="ACheckAll">
                                                </label>
                                            </div>
                                        </th>
                                        <th class="text-center px-2 py-1 text-dark-emphasis">
                                            <div class="form-check d-flex w-100 justify-content-center">
                                                <input class="form-check-input checkAll" type="checkbox" id="HCheckAll">
                                                <label class="form-check-label" for="HCheckAll">
                                                </label>
                                            </div>
                                        </th>
                                    </tr>
                                    <?php if(!empty($memberList) && is_array($memberList)): ?>
                                    <?php foreach($memberList as $row): ?>
                                        <tr class="member-row">
                                            <td class="px-2 py-1 text-dark-emphasis fw-bold">
                                                <input type="hidden" name="member_id[]" value="<?= $row['id'] ?>">
                                                <?= $row['name'] ?>
                                            </td>
                                            <td class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="1" id="status_p_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 1) ? "checked" : "" ?>>
                                                    <label class="form-check-label" for="status_p_<?= $row['id'] ?>">
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="2" id="status_l_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 2) ? "checked" : "" ?>>
                                                    <label class="form-check-label" for="status_l_<?= $row['id'] ?>">
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="3" id="status_a_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 3) ? "checked" : "" ?>>
                                                    <label class="form-check-label" for="status_a_<?= $row['id'] ?>">
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="4" id="status_h_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 4) ? "checked" : "" ?>>
                                                    <label class="form-check-label" for="status_h_<?= $row['id'] ?>">
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-2 py-1 text-center">No Member Listed Yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex w-100 justify-content-center align-items-center">
                <div class="col-lg-4 col-md-6 col-sm-12 col-12">
                    <button class="btn btn-primary rounded-pill w-100" type="submit">Save Attendance</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>
<script>
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
                }else if(id == 'LCheckAll'){
                    $('.status_check[value="2"]').prop('checked', true) 
                }else if(id == 'ACheckAll'){
                    $('.status_check[value="3"]').prop('checked', true) 
                }else if(id == 'HCheckAll'){
                    $('.status_check[value="4"]').prop('checked', true) 
                }
            }else{
                if(id == 'PCheckAll'){
                    $('.status_check[value="1"]').prop('checked', false) 
                }else if(id == 'LCheckAll'){
                    $('.status_check[value="2"]').prop('checked', false) 
                }else if(id == 'ACheckAll'){
                    $('.status_check[value="3"]').prop('checked', false) 
                }else if(id == 'HCheckAll'){
                    $('.status_check[value="4"]').prop('checked', false) 
                }
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
        var statuses = {'PCheckAll': 1, 'LCheckAll': 2, 'ACheckAll': 3, 'HCheckAll':4}
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
</script>