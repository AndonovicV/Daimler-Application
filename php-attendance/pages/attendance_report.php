<div class="page-title mb-3">Attendance Report</div>
<hr>
<?php 
// $studentList = $actionClass->list_student();
$mdtList = $actionClass->list_mdt();
$mdt_id = $_GET['mdt_id'] ?? "";
$mdt_month = $_GET['mdt_month'] ?? "";
$memberList = $actionClass->attendancemembersMonthly($mdt_id, $mdt_month);
$monthLastDay = 0;
if(!empty($mdt_month)){
    $monthLastDay = date("d", strtotime("{$mdt_month}-1 -1 day -1 month")) ;
}
// echo $monthLastDay;
?>
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
                                <label for="mdt_month" class="form-label">Date</label>
                                <input type="month" name="mdt_month" id="mdt_month" class="form-control" value="<?= $mdt_month ?? '' ?>" required="required">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(!empty($mdt_id) && !empty($mdt_month)): ?>
            <div class="card shadow mb-3">
                <div class="card-body">
                    <div class="container-fluid">
                        <fieldset>
                            <legend class="h6"><strong>Legend:</strong></legend>
                            <div class="ps-4">
                                <div><span class="text-success fw-bold">P</span> <span class="ms-1">= Present</span></div>
                                <div><span class="text-body-emphasis fw-bold">L</span> <span class="ms-1">= Late</span></div>
                                <div><span class="text-danger fw-bold">A</span> <span class="ms-1">= Absent</span></div>
                                <div><span class="text-primary fw-bold">H</span> <span class="ms-1">= Holiday</span></div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-3">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="px-2 py-2 text-center bg-primary text-light fw-bolder"><?= date("F", strtotime($mdt_month)) ?></div>
                        <div class="table-responsive position-relative">
                            <table id="attendance-rpt-tbl" class="table table-bordered">
                                <thead>
                                    <tr class="bg-primary bg-opacity-75">
                                        <th class="text-center bg-primary text-light" style="width:300px !important">Members</th>
                                        <?php for($i=1; $i <= $monthLastDay; $i++): ?>
                                            <th class="text-center bg-transparent text-light" style="width:80px !important"><?= $i ?></th>
                                        <?php endfor; ?>
                                        <th class="text-center bg-primary text-light">TP</th>
                                        <th class="text-center bg-primary text-light">TL</th>
                                        <th class="text-center bg-primary text-light">TA</th>
                                        <th class="text-center bg-primary text-light">TH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($memberList) && is_array($memberList)): ?>
                                    <?php foreach($memberList as $row): ?>
                                        <tr class="member-row">
                                            <td class="px-2 py-1 text-dark-emphasis fw-bold">
                                                <input type="hidden" name="member_id[]" value="<?= $row['id'] ?>">
                                                <?= $row['name'] ?>
                                            </td>
                                            <?php 
                                            $tp = 0;
                                            $tl = 0;
                                            $ta = 0;
                                            $th = 0;
                                            ?>
                                            <?php for($i=1; $i <= $monthLastDay; $i++): ?>
                                                <td class="text-center px-2 py-1 text-dark-emphasis">
                                                    <?php 
                                                        $i = str_pad($i, 2, 0, STR_PAD_LEFT);
                                                        switch(($row['attendance'][$mdt_month."-".$i] ?? '')){
                                                            case 1:
                                                                echo "<span class='text-success fw-bold'>P</span>";
                                                                $tp += 1;
                                                                break;
                                                            case 2:
                                                                echo "<span class='text-body-emphasis fw-bold'>L</span>";
                                                                $tl += 1;
                                                                break;
                                                            case 3:
                                                                echo "<span class='text-danger fw-bold'>A</span>";
                                                                $ta += 1;
                                                                break;
                                                            case 4:
                                                                echo "<span class='text-primary fw-bold'>H</span>";
                                                                $th += 1;
                                                                break;
                                                        }
                                                    ?>
                                                </td>
                                            <?php endfor; ?>
                                            <th class="text-center bg-secondary text-light"><?= $tp ?></th>
                                            <th class="text-center bg-secondary text-light"><?= $tl ?></th>
                                            <th class="text-center bg-secondary text-light"><?= $ta ?></th>
                                            <th class="text-center bg-secondary text-light"><?= $th ?></th>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?= $monthLastDay + 5 ?>" class="px-2 py-1 text-center">No Member Listed Yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- Scripts -->
<script src="assets/js/attendance_report.js"></script>