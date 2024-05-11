<nav class="navbar navbar-expand-lg navbar-light bg-dark sticky-top" data-bs-theme="dark">
    <div class="container-fluid">
        <!-- <a class="navbar-brand" href="#">Deckblatt</a> -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'home' ? 'active' : '' ?>" href="/Daimler/index.php">DOMM</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'mdt_list' ? 'active' : '' ?>" href="/Daimler/mt_agenda.php">MT Agenda</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'member_list' ? 'active' : '' ?>" href="./?page=member_list">Members</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'attendance' ? 'active' : '' ?>" href="./?page=attendance">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'attendance_report' ? 'active' : '' ?>" href="./?page=attendance_report">Report</a>
                </li>
            </ul>
        </div>
    </div>
    </nav>