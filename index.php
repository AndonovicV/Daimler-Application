<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website</title>

    <!--Link to Bootstrap CSS--> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--Link to Bootstrap JS--><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!--Link to jQuery--> <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!--Make sure to load CSS & JS AFTER Bootstrap and jQuery-->
    <!--Link to CSS--> <link rel="stylesheet" href="style.css"> 
    <!--Link to JS--> <script src="function.js"></script>
</head>
<body> 
    <p>Aly is a bitch</p>
    <p id="blueParag" class="blueParag">I am Blue</p>
    <!-- Vanila JS <button id="redBtn" type="button" class="btn btn-primary" onclick="redFunction()"> Red </button> -->
    <button id="redBtn" type="button" class="btn btn-primary"> Red </button>

    <h2>Login Form</h2>
    <form id="loginForm" action="db_action.php" method="POST">
        <label for="name">First name, Last name:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <div id="errorDisplay"></div>
</body>
</html>