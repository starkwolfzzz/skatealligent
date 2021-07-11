<?php

function check_login($conn)
{
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM Users WHERE user_id = '$id' limit 1";

        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    //redirect to login
    header("Location: account.html");
    die;
}

function random_num($length)
{
    $text = "";
    if ($length < 5) {
        $length = 5;
    }

    $len = rand(4, $length);

    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }

    return $text;
}

function logout()
{
    if (isset($_SESSION['user_id'])) {
        unset($_SESSION['user_id']);
    }

    return true;
}

function alert($message)
{
    echo "<script>alert('$message');</script>";
}

function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function checkSession($session)
{
    require('connection.php');
    $sql = "SELECT * FROM users WHERE session = '$session';";
    $query = mysqli_query($conn, $sql);
    $check = mysqli_num_rows($query);
    if ($check > 0) {
        return "session is correct";
    } else {
        return "session is incorrect";
    }
}
