<?php
include_once('navigation.php');
include 'conn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="custom_css/attendance.css">
    <link rel="stylesheet" href="plugins/virtual_select/virtual-select.min.css">
    <script src="plugins/virtual_select/virtual-select.min.js"></script>
</head>

<body style="background-color: #222; color: #fff;">

    <div class="container mt-5">
        <div class="page-title mb-3 text-light text-center">Manage Attendance</div>
        <hr>

        <h1 style="color: #777" class='mt-4'>Meeting</h1>
        <select id="protokolSelect" data-search="true" class="styled-select w-100 mb-3" style="background-color: #333 !important; color: #fff !important; border: 1px solid #444 !important; border-radius: 4px !important; height: 40px!important; text-align-last: center!important;">
            <option value="">Select protocol...</option>
            <?php
            $sql = "SELECT * FROM mt_agenda_list WHERE module_team = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('s', $selected_team);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($row["agenda_id"] == $selectedAgendaId) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' $selected>"
                            . htmlspecialchars($row["agenda_name"]) . " (" . htmlspecialchars($row["agenda_date"]) . ")"
                            . "</option>";
                    }
                }

                $stmt->close();
            } else {
                echo "Error: " . $conn->error;
            }
            ?>
        </select>
        <script>
            VirtualSelect.init({
                ele: '#protokolSelect'
            });
        </script>

        <!-- Placeholder for the tables -->
        <div id="tables-container" style="display: none;">
            <form action="" id="manage-attendance">
            <input type="hidden" name="agenda_id" value="">
                <div class="card shadow mb-3 dark-card">
                    <div class="card-header rounded-0">
                        <div class="card-title text-light">Attendance Sheet</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendance-tbl" class="table table-bordered table-hover dark-table">
                                <colgroup>
                                    <col width="30%">
                                    <col width="30%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center bg-transparent text-light">Members</th>
                                        <th class="text-center bg-transparent text-light">Department</th>
                                        <th class="text-center bg-transparent text-light">Present</th>
                                        <th class="text-center bg-transparent text-light">Absent</th>
                                        <th class="text-center bg-transparent text-light">Substituted</th>
                                    </tr>
                                </thead>
                                <tbody id="attendance-tbl-body">
                                    <!-- Rows will be inserted here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>
                <h1 class="text-light text-center">Guest List</h1>
                <hr>

                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card shadow dark-card">
                            <div class="card-header rounded-0">
                                <div class="d-flex w-100 justify-content-end align-items-center">
                                    <button class="btn btn-sm rounded-0 btn-primary" type="button" id="add_guest"><i class="far fa-plus-square"></i> Add New</button>
                                </div>
                            </div>
                            <div class="card-body rounded-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped dark-table">
                                        <colgroup>
                                            <col width="20%">
                                            <col width="20%">
                                            <col width="15%">
                                            <col width="20%">
                                            <col width="25%">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th class="text-center bg-transparent text-light">Guest Name</th>
                                                <th class="text-center bg-transparent text-light">Department</th>
                                                <th class="text-center bg-transparent text-light">Substitute</th>
                                                <th class="text-center bg-transparent text-light">Present</th>
                                                <th class="text-center bg-transparent text-light">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="guest-list-tbl-body">
                                            <!-- Rows will be inserted here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="custom_js/attendance.js"></script>
</body>
</html>