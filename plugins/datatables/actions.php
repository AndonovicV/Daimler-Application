<?php 
include 'C:\xampp\htdocs\Daimler\conn.php';


// Add a new task
if (isset($_POST['taskContent']) && isset($_POST['taskResponsible']) && isset($_POST['taskGft']) && isset($_POST['taskProject'])) {
    $taskContent = $_POST['taskContent'];
    $taskResponsible = $_POST['taskResponsible'];
    $taskGft = $_POST['taskGft'];
    $taskProject = $_POST['taskProject'];

    $sql = "INSERT INTO tasks (title, responsible, GFT, PROJECT) VALUES ('$taskContent', '$taskResponsible', '$taskGft', '$taskProject')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New task added successfully";
    } else {
        echo "Error adding new task: " . $conn->error;
    }
}

// Add a new topic
if (isset($_POST['topicContent']) && isset($_POST['topicResponsible']) && isset($_POST['topicGft']) && isset($_POST['topicProject'])) {
    $topicContent = $_POST['topicContent'];
    $topicResponsible = $_POST['topicResponsible'];
    $topicGft = $_POST['topicGft'];
    $topicProject = $_POST['topicProject'];

    $sql = "INSERT INTO topics (title, responsible, GFT, PROJECT) VALUES ('$topicContent', '$topicResponsible', '$topicGft', '$topicProject')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New topic added successfully";
    } else {
        echo "Error adding new topic: " . $conn->error;
    }
}

// mt_agenda new row
if (isset($_POST['meanId']) && isset($_POST['counter'])) {
    $meanId = $_POST['meanId'];
    $counter = $_POST['counter'];

    $sql = "INSERT INTO mt_agenda (id, GFT, Topic, Status, Change_Request, Task, Comment, Milestone, Responsible, Start, New_Row, Delete_Row)
            VALUES ('$meanId', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', '$counter', 'Yes', 'No')";
    echo "SQL query: " . $sql; // Echo the SQL query
    if ($conn->query($sql) === TRUE) {
        echo "New record inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    //echo "Mean ID or Counter value is not set";
}

// mt_agenda delete row
if (isset($_POST['rowId'])) {
    $rowId = $_POST['rowId'];
    //echo $rowId; // Corrected variable name
    $sql = "DELETE FROM mt_agenda WHERE CONCAT(`mt_agenda`.`id`) = '$rowId'";
    echo "SQL query: " . $sql; // Echo the SQL query
    if ($conn->query($sql) === TRUE) {
        //echo "Row deleted successfully.";
    } else {
        //echo "Error deleting row: " . $conn->error;
    }
} else {
    //echo "RowId not provided.";
}
?>  