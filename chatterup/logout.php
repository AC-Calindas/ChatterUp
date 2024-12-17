<?php
session_start();//starts a new session or resumes an existing session, allowing you to store and retrieve session variables

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

header("Location: login.php");
die;
?>