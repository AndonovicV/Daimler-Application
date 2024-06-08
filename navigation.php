<?php
include 'conn.php';
session_start(); // Start the session if not already started

// Check if the session variable is set
if (isset($_SESSION['selected_team'])) {
    $selected_team = $_SESSION['selected_team'];
    //echo($selected_team);
} else {
    $selected_team = ""; // Default value if not set
}


?>

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
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'mdt_list' ? 'active' : '' ?>" href="/Daimler/protokol.php">Protokoll</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'member_list' ? 'active' : '' ?>" href="./?page=member_list">Members</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'attendance' ? 'active' : '' ?>" href="/Daimler/attendance.php">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'attendance_report' ? 'active' : '' ?>" href="/Daimler/search.php">Search</a>
                </li>
            </ul>
        </div>
        <?php if(empty($selected_team) || $selected_team === ""): ?>
        <div class="error-message" style="margin-top: 10px; border-top: 2px solid red; padding-top: 5px; color: white;">
            Error, Please select a module team from DOMM main page to proceed
        </div>
        <?php endif; ?>
        <div class="selected-team" style="position: absolute; top: 0; right: 15px; line-height: 50px; color: rgba(255, 255, 255, 0.55);">
            <span><?= $selected_team ?></span>
        </div>
    </div>
    </nav>