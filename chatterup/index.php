<?php
session_start(); //starts a new session or resumes an existing session, allowing you to store and retrieve session variables
include("connect.php"); //includes external PHP files for database connection
include("function.php");

// calls a function check_login to verify if the user is logged in. It returns user details if authenticated
$user = check_login($conn); 

// fetch user details 
$user_id = $user['id'];
$user_name = $user['user_name'];

// fetch users to create message
//sql statement (stmt) to statement to fetch all users except the logged-in one
// bind_param method binds the user ID to the query, preventing SQL injection
$stmt = $conn->prepare("SELECT id, user_name FROM users WHERE id != ?"); 
$stmt->bind_param("i", $user_id);
$stmt->execute();
$users = $stmt->get_result();

// fetch message threads
// retrieves the receiver_id from the URL parameters. If not set, defaults to 0
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
if ($receiver_id > 0) { //if a valid receiver ID is provided, fetches the messages exchanged between the logged-in user and the selected user
    $stmt = $conn->prepare("
        SELECT m.id, m.message, m.sender_id, m.created_at, u.user_name
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    "); // another sql statement to retrieve messages where the logged-in user is either the sender or receiver, ordered by the timestamp
    $stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
    $stmt->execute();
    $messages = $stmt->get_result();

    // fetch the other user's name
    $stmt = $conn->prepare("SELECT user_name FROM users WHERE id = ?"); //retrieves the username of the user being messaged
    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $other_user = $result->fetch_assoc();
}

// fetch all conversations for the logged-in user 
// and determining the other participant in each conversation
$conversations_stmt = $conn->prepare("
    SELECT DISTINCT CASE 
        WHEN sender_id = ? THEN receiver_id 
        ELSE sender_id 
    END AS other_user_id
    FROM messages 
    WHERE sender_id = ? OR receiver_id = ?
");
$conversations_stmt->bind_param("iii", $user_id, $user_id, $user_id);
$conversations_stmt->execute();
$conversations = $conversations_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Thread</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        #container {
            background-color: #2b2b2b;
            border-radius: 10px;
            padding: 20px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        h2 {
            color: #ff4d4d;
            margin-bottom: 20px;
        }

        .user {
            margin-bottom: 10px;
        }

        .user a {
            color: #ff4d4d;
            text-decoration: none;
        }

        .user a:hover {
            text-decoration: underline;
        }

        #chatbox {
            margin-top: 20px;
        }

        #chatbox textarea {
            width: 100%;
            height: 60px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ff4d4d;
            background-color: #1a1a1a;
            color: #fff;
            padding: 10px;
            resize: none;
        }

        #chatbox button {
            background-color: #ff4d4d;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        #chatbox button:hover {
            background-color: #e64545;
        }

        .message-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .message {
            background-color: #3b3b3b;
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            max-width: 70%;
            color: #fff;
        }

        .message.user1 {
            background-color: #ff4d4d;
            align-self: flex-end;
        }

        .message .timestamp {
            font-size: 12px;
            color: #ccc;
        }

        #logout {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        #logout a {
            color: #ff4d4d;
            text-decoration: none;
            font-size: 14px;
        }

        #logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="logout">
            <a href="logout.php">Logout</a> <!-- logouts -->
        </div>

        <div id="users"> <!-- sisplays a list of users, excluding the logged-in user. 
            Each user is a link that, when clicked, sets the receiver_id in the URL -->
            <h2>Users (Logged in as: <?= htmlspecialchars($user_name) ?>)</h2>
            <?php while ($row = $users->fetch_assoc()): ?>
                <div class="user">
                    <a href="?receiver_id=<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['user_name']) ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <div id="chatbox"> <!--displays the chat interface if a receiver is selected, including a text area for typing messages and a submit button-->
            <?php if ($receiver_id > 0): ?>
            <h2>Chatting with: <?= htmlspecialchars($other_user['user_name']) ?></h2>
            <form method="POST" action="message.php">
                <textarea name="message" placeholder="Type your message..." required></textarea>
                <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                <button type="submit">Send</button>
            </form>
            <?php endif; ?>
        </div>

        <div id="messages"> <!--displays the message history between the logged-in user and the selected receiver,
             formatting messages differently based on the sender. -->
            <h2>Messages</h2>
            <div class="message-container">
            <?php
                if ($receiver_id > 0 && $messages->num_rows > 0) {
                    while ($row = $messages->fetch_assoc()):
            ?>
                <div class="message <?= $row['sender_id'] == $user_id ? 'user1' : 'user2' ?>">
                    <span class="username"><strong><?= htmlspecialchars($row['user_name']) ?>:</strong></span>
                    <?= htmlspecialchars($row['message']) ?>
                    <div class="timestamp">Sent at: <?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?></div>
                </div>
            <?php
                    endwhile;
                } else {
                    echo "No messages found.";
                }
            ?>
            </div>
        </div>
    </div>
</body>
</html>
