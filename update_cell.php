<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $value = $_POST['value'];
    $column = $_POST['column'];
    $type = $_POST['type'];

    if ($type == 'topic') {
        $table = 'domm_topics';
    } else if ($type == 'task') {
        $table = 'domm_tasks';
    } else {
        echo 'Invalid type';
        exit;
    }

    if ($column == 'asap') {
        if ($value == false) {
            $value = 0;
        } else if ($value == true) {
            $value = 1;
        }
    }

    $sql = "UPDATE $table SET $column = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $value, $id);

    // Log the parameters
    error_log("With parameters: value=$value, id=$id");

    if ($stmt->execute()) {
        error_log("executing statement: " . $stmt);
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
