<?php
function check_login($conn)
{
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = '$user_id' LIMIT 1";

        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    //redirect to login
    header("Location: login.php");
    die;
}

function random_num($length) //random number function with $lenght parameter to specify length for random numbers
{
    $text = "";
    if ($length < 5) { //to check for random number if it's 5
        $length = 5;
    }

    $len = rand(4, $length); //generates a random length between 4 and the specified $length using the rand function

    for ($i = 0; $i < $len; $i++) { //initializes an empty string $text and uses a for loop to append random digits (from 0 to 9) to the string. The loop runs $len times, creating a random number of the specified length
        $text .= rand(0, 9);
    }

    return $text;
}