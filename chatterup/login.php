<?php
session_start();//starts a new session or resumes an existing session, allowing you to store and retrieve session variables
include("connect.php"); // include the file that establishes the db connection

if (isset($_SESSION['user_id'])) { //checks if the session variable user_id is set,and if the user is already logged in. If yes, it shows that the user to the index.php page and exits 
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {//checks if the request method is POST, showing that the login form has been submitted
    $user_name = $_POST['user_name']; //retrieves these info that the user entered from POST method
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_name = ?"); //prepares a SQL statement($stmt) to select user details from the "users" table where the user_name matches the input. The bind_param method binds the username to the query
    $stmt->bind_param("s", $user_name);
    $stmt->execute(); //executes the prepared statement and retrieves the result set
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { //to check if any rows were returned, showing that the username exists in the database
        $row = $result->fetch_assoc(); //fetches the user data as an associative array , the password_verify function checks if the entered password matches the hashed password stored in the database
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; //if the password is correct, the user's ID is stored in the SESSION, and the user is redirected to index.php
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Incorrect password"; //shows an error message if the password is not matched or incorrect
        }
    } else {
        $error_message = "Incorrect username"; //shows an error message if the username is not matched or incorrect
    }

    $stmt->close(); //closes the statment to free up memory or resources preventing web not loading or delaying
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<style type="text/css">
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #1b1b1b;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    #box {
        background-color: #2e2e2e;
        border: 2px solid #800000; 
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        width: 350px;
        padding: 30px;
        color: white;
    }

    #box .title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
        color: #ff4d4d;
    }

    #error-message {
        color: #ff4d4d; 
        background-color: #442222;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
    }

    #text {
        height: 35px;
        border-radius: 5px;
        padding: 8px;
        border: solid thin #aaa;
        width: calc(100% - 16px);
        margin-bottom: 20px;
        background-color: #444;
        color: white;
        border: 1px solid #800000; 
    }

    #text:focus {
        border-color: #ff4d4d; 
        outline: none;
        box-shadow: 0 0 5px #ff4d4d;
    }

    #button {
        padding: 10px 20px;
        width: 100%;
        color: white;
        background-color: #800000;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    #button:hover {
        background-color: #ff4d4d; 
    }

    a {
        color: #ff4d4d;
        text-decoration: none;
        font-size: 14px;
        display: block;
        text-align: center;
        margin-top: 10px;
    }

    a:hover {
        text-decoration: underline;
    }
</style>


<div id="box">
    <div class="title">Login</div>

    <?php if (isset($error_message)): ?> <!--to check if an error was occured when logging in-->
        <div id="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="post">
        <input id="text" type="text" name="user_name" placeholder="Username" required><br>
        <input id="text" type="password" name="password" placeholder="Password" required><br>
        <input id="button" type="submit" value="Login">
        <a href="signup.php">Don't have an account? Signup</a>
    </form>
</div>







</body>
</html>
