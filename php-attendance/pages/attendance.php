<div class="page-title mb-3 text-light">Manage Attendance</div>
<hr>
<?php
include 'db-connect.php';
<<<<<<< HEAD
// $memberList = $actionClass->list_member();
$mdtList = $actionClass->list_mdt();
$mdt_id = $_GET['mdt_id'] ?? "";
$meeting_date = $_GET['meeting_date'] ?? "";
$agendaList = $actionClass->list_agendas(); // Assuming you have a function to list agendas
$agenda_id = $_GET['agenda_id'] ?? "";
$memberList = $actionClass->attendanceMembersByAgenda($agenda_id); // You'll need to adjust this function
?>
<!-- <pre>
    <?php print_r($memberList) ?>
</pre> -->

<form action="" id="manage-attendance">
    <select name="agenda_id" id="agenda_id" class="form-select" required="required">
=======
$mdtList = $actionClass->list_mdt();
$mdt_id = $_GET['mdt_id'] ?? "";
$meeting_date = $_GET['meeting_date'] ?? "";
$agendaList = $actionClass->list_agendas();
$agenda_id = $_GET['agenda_id'] ?? "";
$memberList = $actionClass->attendanceMembersByAgenda($agenda_id);
?>

<form action="" id="manage-attendance">
    <select id="agenda_id" name="agenda_id" data-search="true" class="styled-select w-100 mb-3" required="required" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;"> <!-- This should work but it doesn't -->
>>>>>>> aly2
        <option value="" disabled <?= empty($agenda_id) ? "selected" : "" ?>> -- Select Agenda -- </option>
        <?php if (!empty($agendaList) && is_array($agendaList)) : ?>
            <?php foreach ($agendaList as $row) : ?>
                <option value="<?= $row['agenda_id'] ?>" <?= (isset($agenda_id) && $agenda_id == $row['agenda_id']) ? "selected" : "" ?>><?= $row['agenda_name'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
<<<<<<< HEAD
    <?php if (!empty($agenda_id)) : ?>
        <div class="card shadow mb-3">
            <div class="card-header rounded-0">
                <div class="card-title">Attendance Sheet</div>
=======
    <!--Activates Virtual Selector-->
    <script>
        VirtualSelect.init({
            ele: '#agenda_id'
        });
    </script>
    <?php if (!empty($agenda_id)) : ?>
        <div class="card shadow mb-3 dark-card">
            <div class="card-header rounded-0">
                <div class="card-title text-light">Attendance Sheet</div>
>>>>>>> aly2
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
<<<<<<< HEAD
                        <table id="attendance-tbl" class="table table-bordered">
=======
                        <table id="attendance-tbl" class="table table-bordered dark-table">
>>>>>>> aly2
                            <colgroup>
                                <col width="30%">
                                <col width="30%">
                                <col width="15%">
                                <col width="15%">
                                <col width="15%">
                            </colgroup>
<<<<<<< HEAD
                            <thead class="bg-primary">
=======
                            <thead>
>>>>>>> aly2
                                <tr>
                                    <th class="text-center bg-transparent text-light">Members</th>
                                    <th class="text-center bg-transparent text-light">Department</th>
                                    <th class="text-center bg-transparent text-light">Present</th>
                                    <th class="text-center bg-transparent text-light">Absent</th>
                                    <th class="text-center bg-transparent text-light">Substituted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($memberList) && is_array($memberList)) : ?>
                                    <?php foreach ($memberList as $row) : ?>
                                        <tr class="member-row">
                                            <td class="px-2 py-1 text-light-emphasis fw-bold">
                                                <input type="hidden" name="member_id[]" value="<?= $row['id'] ?>">
                                                <?= $row['name'] ?>
                                            </td>
<<<<<<< HEAD
                                            <td class="text-center px-2 py-1 text-dark-emphasis"><?= $row['dept'] ?></td>
                                            <td class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input" type="checkbox" name="status[<?= $row['id'] ?>]" value="1" id="status_p_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 1) ? "checked" : "" ?>>
=======
                                            <td class="text-center px-2 py-1 text-light-emphasis"><?= $row['dept'] ?></td>
                                            <td class="text-center px-2 py-1 text-light-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input" type="checkbox" name="status[<?= $row['id'] ?>]" value="1" id="status_p_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 1) ? "checked" : "" ?>>
                                                </div>
                                            </td>
                                            <td class="text-center px-2 py-1 text-light-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input" type="checkbox" name="status[<?= $row['id'] ?>]" value="2" id="status_a_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 2) ? "checked" : "" ?>>
>>>>>>> aly2
                                                </div>
                                            </td>
                                            <td class="text-center px-2 py-1 text-light-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
<<<<<<< HEAD
                                                    <input class="form-check-input" type="checkbox" name="status[<?= $row['id'] ?>]" value="2" id="status_a_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 2) ? "checked" : "" ?>>
                                                </div>
                                            </td>
                                            <td class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input" type="checkbox" name="status[<?= $row['id'] ?>]" value="3" id="status_s_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 3) ? "checked" : "" ?>>
                                                </div>
                                            </td>
=======
                                                    <input class="form-check-input" type="checkbox" name="status[<?= $row['id'] ?>]" value="3" id="status_s_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 3) ? "checked" : "" ?>>
                                                </div>
                                            </td>
>>>>>>> aly2
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
<<<<<<< HEAD
                                        <td colspan="5" class="px-2 py-1 text-center">No Member Listed Yet</td>
=======
                                        <td colspan="5" class="px-2 py-1 text-center text-light">No Member Listed Yet</td>
>>>>>>> aly2
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div></div>
<<<<<<< HEAD
        <h1 style="color:white; text-align:center;">Guest List</h1>
=======
        <h1 class="text-light text-center">Guest List</h1>
>>>>>>> aly2
        <hr>
        <?php
        $guestList = $actionClass->list_guest();
        ?>
        <div class="row justify-content-center">
<<<<<<< HEAD
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="card shadow">
=======
            <div class="col-lg-12">
                <div class="card shadow dark-card">
>>>>>>> aly2
                    <div class="card-header rounded-0">
                        <div class="d-flex w-100 justify-content-end align-items-center">
                            <button class="btn btn-sm rounded-0 btn-primary" type="button" id="add_guest"><i class="far fa-plus-square"></i> Add New</button>
                        </div>
                    </div>
                    <div class="card-body rounded-0">
                        <div class="container-fluid">
                            <div class="table-responsive">
<<<<<<< HEAD
                                <table class="table table-bordered table-hovered table-stripped">
=======
                                <table class="table table-bordered table-hovered table-striped dark-table">
>>>>>>> aly2
                                    <colgroup>
                                        <col width="20%">
                                        <col width="20%">
                                        <col width="15%">
                                        <col width="20%">
                                        <col width="25%">
                                    </colgroup>
<<<<<<< HEAD
                                    <thead class="bg-dark-subtle">
                                        <tr class="bg-transparent">
                                            <th class="bg-transparent text-center">Guest Name</th>
                                            <th class="bg-transparent text-center">Department</th>
                                            <th class="bg-transparent text-center">Substitute</th>
                                            <th class="bg-transparent text-center">Present</th>
                                            <th class="bg-transparent text-center">Actions</th>
=======
                                    <thead>
                                        <tr>
                                            <th class="text-center bg-transparent text-light">Guest Name</th>
                                            <th class="text-center bg-transparent text-light">Department</th>
                                            <th class="text-center bg-transparent text-light">Substitute</th>
                                            <th class="text-center bg-transparent text-light">Present</th>
                                            <th class="text-center bg-transparent text-light">Actions</th>
>>>>>>> aly2
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($guestList) && is_array($guestList)) : ?>
                                            <?php foreach ($guestList as $row) : ?>
                                                <tr>
<<<<<<< HEAD
                                                    <td class="px-2 py-1"><?= $row['name'] ?></td>
                                                    <td class="px-2 py-1"><?= $row['dept'] ?></td>
                                                    <td class="px-2 py-1"><?= $row['substitute'] ?></td>
=======
                                                    <td class="px-2 py-1 text-light-emphasis fw-bold"><?= $row['name'] ?></td>
                                                    <td class="text-center px-2 py-1 text-light-emphasis"><?= $row['dept'] ?></td>
                                                    <td class="px-2 py-1 text-light"><?= $row['substitute'] ?></td>
>>>>>>> aly2
                                                    <td class="text-center px-2 py-1">
                                                        <input class="form-check-input" type="checkbox" name="present[<?= $row['id'] ?>]" id="present_<?= $row['id'] ?>" <?= ($row['present'] == 'Yes') ? 'checked' : '' ?>>
                                                    </td>
                                                    <td class="text-center px-2 py-1">
                                                        <div class="input-group input-group-sm justify-content-center">
                                                            <button class="btn btn-sm btn-outline-primary rounded-0 edit_guest" type="button" data-id="<?= $row['id'] ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                                            <button class="btn btn-sm btn-outline-danger rounded-0 delete_guest" type="button" data-id="<?= $row['id'] ?>" title="Delete"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
<<<<<<< HEAD
                                                <th class="text-center px-2 py-1" colspan="5">No data found.</th>
=======
                                                <th class="text-center px-2 py-1 text-light" colspan="5">No data found.</th>
>>>>>>> aly2
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex w-100 justify-content-center align-items-center">
            <div class="col-lg-4 col-md-6 col-sm-12 col-12">
                <button class="btn btn-primary rounded-pill w-100" type="submit">Save Attendance</button>
            </div>
        </div>
    <?php endif; ?>
</form>

<!-- Scripts -->
<script src="assets/js/attendance.js"></script>
<script src="assets/js/guests.js"></script>
