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
                                <input type="datetime-local" name="meeting_date" id="meeting_date" class="form-control" value="<?= $meeting_date ?? '' ?>" required="required">
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
                                    <!-- <col width="40%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%"> -->
                                </colgroup>
                                <thead class="bg-primary"> 
                                    <tr>
                                        <th class="text-center bg-transparent text-light">Department</th>
                                        <th class="text-center bg-transparent text-light">Members</th>
                                        <th class="text-center bg-transparent text-light">Present</th>
                                        <!-- <th class="text-center bg-transparent text-light">Late</th> -->
                                        <!-- <th class="text-center bg-transparent text-light">Absent</th>
                                        <th class="text-center bg-transparent text-light">Holiday</th> -->
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
                                        <!-- <th class="text-center px-2 py-1 text-dark-emphasis">
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
                                        </th> -->
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
                                            <!-- <td class="text-center px-2 py-1 text-dark-emphasis">
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
                                        </tr> -->
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="px-2 py-1 text-center">No Member Listed Yet</td>
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

<hr>

<h1 style="color:white; text-align:center;">Guest List</h1>
<hr>
<?php 
$guestList = $actionClass->list_guest();
?>
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card shadow">
            <div class="card-header rounded-0">
                <div class="d-flex w-100 justify-content-end align-items-center">
                    <button class="btn btn-sm rounded-0 btn-primary" type="button" id="add_guest"><i class="far fa-plus-square"></i> Add New</button>
                </div>
            </div>
            <div class="card-body rounded-0">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hovered table-stripped">
                            <colgroup>
                                <col width="10%">
                                <col width="10%">
                                <col width="60%">
                                <col width="20%">
                            </colgroup>
                            <thead class="bg-dark-subtle">
                                <tr class="bg-transparent">
                                    <th class="bg-transparent text-center">Department</th>
                                    <th class="bg-transparent text-center">ID</th>
                                    <th class="bg-transparent text-center">Name</th>
                                    <th class="bg-transparent text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php if(!empty($guestList) && is_array($guestList)): ?>
                                <?php foreach($guestList as $row): ?>
                                    <tr>
                                        <td class="px-2 py-1"><?= $row['dept'] ?></td>
                                        <td class="text-center px-2 py-1"><?= $row['id'] ?></td>
                                        <td class="px-2 py-1"><?= $row['name'] ?></td>
                                        <td class="text-center px-2 py-1">
                                            <div class="input-group input-group-sm justify-content-center">
                                                <button class="btn btn-sm btn-outline-primary rounded-0 edit_guest" type="button" data-id="<?= $row['id'] ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-outline-danger rounded-0 delete_guest" type="button" data-id="<?= $row['id'] ?>" title="Delete"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <th class="text-center px-2 py-1" colspan="4">No data found.</th>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Scripts -->
<script src="assets/js/attendance.js"></script>
<script src="assets/js/guests.js"></script>