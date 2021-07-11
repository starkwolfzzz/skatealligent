<?php
require('connection.php');
require('functions.php');

if (!$conn) {
    echo 'connection problem';
}

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$url_components = parse_url($url);

parse_str($url_components['query'], $params);

$email = $params['email'];
$token = $params['token'];

$sql = "SELECT * FROM users where email = '$email'";
$result = mysqli_query($conn, $sql);
$countdata = mysqli_num_rows($result);

$sql2 = "SELECT * FROM users where verifytoken = '$token'";
$result2 = mysqli_query($conn, $sql2);
$countdata2 = mysqli_num_rows($result2);

if ($countdata == 0) {
    echo "Invalid Email";
} else if ($countdata2 == 0) {
    echo "Invalid token";
} else {
    $sql3 = "SELECT * FROM users where email = '$email' AND verifytoken = '$token' AND verified = 1";
    $result3 = mysqli_query($conn, $sql3);
    $countdata3 = mysqli_num_rows($result3);

    if($countdata3 == 0){
        $verifyEmailQ = mysqli_query($conn, "UPDATE users SET verified = 1 WHERE email = '$email' and verifytoken = '$token'");

        if ($verifyEmailQ) {
            echo "Your email is now verified";
        }
    } else {
        echo "Your email was already verified";
    }
}
