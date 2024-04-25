<?php 
include 'C:\xampp\htdocs\Daimler\conn.php'; 
    if (isset($_POST['counter'])) {
        $counter = $_POST['counter'];

        $sql = "INSERT INTO mt_agenda (add_row, gft, topic, status, change_request, task, comment, milestone, responsible, start, duration)
                VALUES ('New Row', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', 0, 0)";

        if ($conn->query($sql) === TRUE) {
            echo "New record inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    } else {
        echo "Counter value is not set";
    }
?>