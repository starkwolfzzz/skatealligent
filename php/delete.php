<?php
require('connection.php');
require('functions.php');

if (!$conn) {
    echo 'connection problem';
}

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$url_components = parse_url($url);

parse_str($url_components['query'], $params);

$id = $params['id'];
$token = $params['token'];

$sql = "SELECT * FROM users where id = '$id'";
$result = mysqli_query($conn, $sql);
$countdata = mysqli_num_rows($result);

$sql2 = "SELECT * FROM users where verifytoken = '$token'";
$result2 = mysqli_query($conn, $sql2);
$countdata2 = mysqli_num_rows($result2);

if ($countdata == 0) {
    echo "Invalid id";
} else if ($countdata2 == 0) {
    echo "Invalid token";
} else {
    $sql3 = "DELETE FROM users where id = '$id' AND verifytoken = '$token'";
    $result3 = mysqli_query($conn, $sql3);

    if ($result3) {
        echo "Your account has been deleted";
    } else {
        echo "Your account has not been deleted";
    }
}
