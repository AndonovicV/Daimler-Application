<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $value = $_POST['value'];
    $column = $_POST['column'];
    $type = $_POST['type'];

    if ($type == 'topic') {
        $table = 'topics';
    } else if ($type == 'task') {
        $table = 'tasks';
    } else {
        echo 'Invalid type';
        exit;
    }

    $sql = "UPDATE $table SET $column = ? WHERE id = ?";
    // Log the SQL statement
    error_log("Preparing SQL statement: " . $sql);

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $value, $id);

    // Log the parameters
    error_log("With parameters: value=$value, id=$id");

    if ($stmt->execute()) {
        echo 'Success';
    } else {
        // Log the error
        error_log("Error executing statement: " . $stmt->error);
        echo 'Error';
    }

    $stmt->close();
    $conn->close();
}
?>
