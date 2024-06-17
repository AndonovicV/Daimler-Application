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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
    <script src="plugins/virtual_select/virtual-select.min.js"></script>
</head>

<body style="background-color: #222; color: #fff;">

    <div class="container mt-5">
        <hr>
        <div class="page-title mb-3 text-light text-center" style="text-align: center; margin-top: 40px;">
            <h1 style="color: #777; display: inline-block; margin-bottom: 20px;">Manage Attendance</h1>
            <select id="protokolSelect" data-search="true" class="styled-select w-50 mb-3" style="background-color: #333; color: #fff; border: 1px solid #444; border-radius: 4px; height: 40px; display: inline-block; text-align-last: center;">
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
                                echo "<option value='" . htmlspecialchars($row["agenda_id"]) . "' data-name='" . htmlspecialchars($row["agenda_name"]) . "' data-date='" . htmlspecialchars($row["agenda_date"]) . "' $selected>"
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
        </div>

        <script>
            VirtualSelect.init({
                ele: '#protokolSelect'
            });

            document.getElementById('protokolSelect').addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('tables-container').style.display = 'block';
                } else {
                    document.getElementById('tables-container').style.display = 'none';
                }
            });

            function exportPDF() {
                const selectedOption = document.querySelector('#protokolSelect');
                if (!selectedOption || !selectedOption.value) {
                    alert('Please select a protocol before exporting.');
                    return;
                }

                
                const agendaName = selectedOption.textContent.trim().split(' (')[0];
                const agendaDate = selectedOption.textContent.trim().split(' (')[1];
                const fileName = agendaName ? `${agendaName}.pdf` : 'attendance.pdf';

                var docDefinition = {
                    content: [
                        { text: 'Attendance List', style: 'header' },
                        { text: `Agenda: ${agendaName}`, style: 'subheader' },
                        {
                            table: {
                                headerRows: 1,
                                widths: [ '*', '*', '*', '*', '*' ],
                                body: [
                                    [ 'Members', 'Department', 'Present', 'Absent', 'Substituted' ],
                                    ...Array.from(document.querySelectorAll('#attendance-tbl tbody tr')).map(row => [
                                        row.cells[0].innerText,
                                        row.cells[1].innerText,
                                        row.cells[2].querySelector('input').checked ? 'Yes' : 'No',
                                        row.cells[3].querySelector('input').checked ? 'Yes' : 'No',
                                        row.cells[4].querySelector('input').checked ? 'Yes' : 'No'
                                    ])
                                ]
                            }
                        },
                        { text: 'Guest List', style: 'header', margin: [0, 20, 0, 10] },
                        {
                            table: {
                                headerRows: 1,
                                widths: [ '*', '*', '*', '*' ],
                                body: [
                                    [ 'Guest Name', 'Department', 'Substitute', 'Present' ],
                                    ...Array.from(document.querySelectorAll('#guest-list-tbl-body tr')).map(row => [
                                        row.cells[0].innerText,
                                        row.cells[1].innerText,
                                        row.cells[2].innerText,
                                        row.cells[3].querySelector('input').checked ? 'Yes' : 'No'
                                    ])
                                ]
                            }
                        }
                    ],
                    styles: {
                        header: {
                            fontSize: 22,
                            bold: true
                        },
                        subheader: {
                            fontSize: 18,
                            bold: true,
                            margin: [0, 10, 0, 5]
                        }
                    }
                };

                pdfMake.createPdf(docDefinition).download(fileName);
            }
        </script>

        <!-- Placeholder for the tables -->
        <div id="tables-container" style="display: none;">
            <hr>
            <h1 class="text-light text-center">Attendance List</h1>
            <button onclick="exportPDF()" class="btn btn-success">Export as PDF</button>
            <hr>
            <form action="" id="manage-attendance">
                <input type="hidden" name="agenda_id" value="">
                <div class="card shadow mb-3 dark-card">
                    <div class="card-header rounded-0"></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendance-tbl" class="table table-bordered table-hover dark-table">
                                <colgroup>
                                    <col width="30%">
                                    <col width="30%">
                                    <col width="13%">
                                    <col width="13%">
                                    <col width="19%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center bg-transparent text-light">Members</th>
                                        <th class="text-center bg-transparent text-light">Department</th>
                                        <th class="text-center bg-transparent text-light">
                                            Present <input type="checkbox" id="checkAllPresent">
                                        </th>
                                        <th class="text-center bg-transparent text-light">
                                            Absent <input type="checkbox" id="checkAllAbsent">
                                        </th>
                                        <th class="text-center bg-transparent text-light">
                                            Substituted <input type="checkbox" id="checkAllSubstituted">
                                        </th>
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
                                            <col width="25%">
                                            <col width="25%">
                                            <col width="25%">
                                            <col width="25%">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th class="text-center bg-transparent text-light">Guest Name</th>
                                                <th class="text-center bg-transparent text-light">Department</th>
                                                <th class="text-center bg-transparent text-light">Substitute</th>
                                                <th class="text-center bg-transparent text-light">
                                                    Present <input type="checkbox" id="checkAllGuestPresent">
                                                </th>
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
