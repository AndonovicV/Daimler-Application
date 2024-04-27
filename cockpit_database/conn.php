<?php
$conn = mysqli_connect('localhost', 'root', '', 'dom_cockpit_dummy', '8888');

if (mysqli_connect_errno()) {
    printf("Connection failed: %s\n", mysqli_connect_error());
    exit();
}
/* change character set to utf8 */
if (!$conn->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $conn->error);
    exit();
}

// test if database is working
$test = $conn->query("SELECT version()");
if (!$test) {
    printf("Error: %s\n", $conn->error);
    exit();
}

?>
