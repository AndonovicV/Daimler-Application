<?php 
include 'db_conn.php';
//LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    
    $result = mysqli_query($conn, $sql);

    // Check if query returned any rows
    if ($result && mysqli_num_rows($result) > 0) {
        header("Location: startingPage.php");
    } else {
        echo "Invalid username or password!";
    }
    $conn->close();
}
?>