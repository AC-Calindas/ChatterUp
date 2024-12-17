<?php
session_start();//starts a new session or resumes an existing session, allowing you to store and retrieve session variables
include("connect.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //if something was posted
    $user_name = $_POST['user_name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        //save to database
        $user_id = random_num(20);
        $query = "INSERT INTO users (id, user_name, password) VALUES ('$user_id', '$user_name', '$password')";

        mysqli_query($conn, $query);

        header("Location: login.php");
        die;
    } else {
        echo "Please enter some valid information!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
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
    <div class="title">Signup</div>
    <form method="post">
        <input id="text" type="text" name="user_name" placeholder="Username" required><br>
        <input id="text" type="password" name="password" placeholder="Password" required><br>
        <input id="button" type="submit" value="Signup">
        <a href="login.php">Already have an account? Login</a>
    </form>
</div>
</body>
</html>