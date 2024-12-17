<?php
session_start();//starts a new session or resumes an existing session, allowing you to store and retrieve session variables
include("connect.php");//includes external PHP files for database connection
include("function.php");

// check if the user is logged in
$user = check_login($conn);

$user_id = $user['id']; //retrieves these info that the user entered from POST method
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];


$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())"); //a SQL statement to insert a new message into the messages table. The sender_id represents the user sending the message, receiver_id is the intended recipient, and message is the content of the message. The NOW() function automatically records the current timestamp
$stmt->bind_param("iis", $user_id, $receiver_id, $message); //to bind parameters  to check if $user_id, $receiver_id, are integers and $message if it's a text
$stmt->execute();

header("Location: index.php?receiver_id=$receiver_id");
exit(); //it exits the function after the message is sent
?>
