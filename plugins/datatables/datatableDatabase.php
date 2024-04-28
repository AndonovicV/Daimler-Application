<?php 
include 'C:\xampp\htdocs\Daimler\conn.php'; 
echo "SLAYY.";
    if (isset($_POST['counter'])) {
        $counter = $_POST['counter'];

      $sql = "INSERT INTO mt_agenda_aly ( type, responsible, start, duration)
                VALUES ( '$counter', '$counter', 0, 0)";

        if ($conn->query($sql) === TRUE) {
            echo "New record inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    } else {
        echo "Counter value is not set";
    }


?>