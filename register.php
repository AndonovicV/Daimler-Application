<?php
// Include database connection file here
include 'db_conn.php';

//REGISTER
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Insert user data into the database
    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
    
    // Execute SQL query
    $result = mysqli_query($conn, $sql);
    // Check if query was successful
    if ($result) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
    $conn->close();
}
?>

