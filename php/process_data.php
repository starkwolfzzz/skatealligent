<?php
require('connection.php');
require('functions.php');

if (isset($_POST['regUsername'])) {
    $allok = true;

    $name = $_POST['regUsername'];
    $email = $_POST['regEmail'];
    $password = $_POST['regPassword'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $salt = md5(generateRandomString(rand(5, 50)));

        $pass = md5(md5($password) . $salt);

        $token = md5($email);

        $sql1 = "SELECT * FROM users WHERE email = '$email';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $sql2 = "SELECT * FROM users WHERE username = '$name';";

        $result2 = mysqli_query($conn, $sql2);

        $rows2 = mysqli_num_rows($result2);

        if ($rows2 > 0) {
            echo "username is in use";
        } else if ($rows1 > 0) {
            echo "email is in use";
        } else if ($rows1 == 0 && $rows2 == 0) {
            $sql = "INSERT INTO users(username, email, password, salt, verifytoken) VALUES('$name', '$email', '$pass', '$salt' , '$token')";
            $sql3 = "INSERT INTO addresses() VALUES()";
            $result = mysqli_query($conn, $sql);
            $result3 = mysqli_query($conn, $sql3);

            $subject = "Skate Alligent Account Email Verification";
            $message = '<div style="padding: 8px;">
            <link rel="preconnect" href="https://fonts.gstatic.com">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
            <p align="left"><strong><strong><span style="font-family: Arial; font-weight: 600;"><span style="font-size: 30px;"><span style="font-weight: bolder;">SKATE ALLIGENT</span></span></span></strong></strong></p>
            <p><span style="font-family: Arial;"><span style="font-size: 16px;">Hi ' . $name . ',<br> <br> Welcome and thanks for signing up.<br> To activate your account, <a href="http://skatealligent.tk/php/verify.php?email=' . $email . '&token=' . $token . '" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/php/verify.php?email=' . $email . '&token=' . $token . '">please click here.<br><br></a> Please also take some time to review our <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/privacy-policy.html">privacy policy</a> and <a href="http://skatealligent.tk/terms-of-use.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/terms-of-use.html">terms of service</a>. </span></span></p>
            <a style="color: white; text-decoration: none; background-color: black; padding: 15px; background-color: black; border-radius: 50px; text-align: center; vertical-align: middle;" href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"><span style="font-family: Arial; font-size: 16px; font-weight: bolder;">skatealligent.tk</span></a>
            <p><span style="font-family: Arial;"><span style=" font-size: 12px;">To change your account preferences, please visit your <a href="http://skatealligent.tk/account.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/account.html">settings page</a>.</span></span></p>
            <table style="width: 210px; height: 20px; background-color: transparent; color: black;" cellspacing="0" cellpadding="0" align="left">
            <tbody>
            <tr>
            <td width="23"><a href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"> <img src="https://i.ibb.co/w4ZQ0bD/black-Icon.png" width="23" height="23"></a></td>
            <td></a>&nbsp;<a style="font-family: Arial; font-weight: 500; color: black;">© 2021 SKATE ALLIGENT</a></td>
            </tr>
            </tbody>
            </table>';
            $message = wordwrap($message, 70);
            $header = "MIME-Version: 1.0" . "\r\n";
            $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $header .= 'From: noreply@skatealligent.tk' . "\r\n";
            //$headers .= 'Cc: myboss@example.com' . "\r\n";

            $sent = mail($email, $subject, $message, $header);
            if ($sent) {
                if ($result && $result3) echo "registeration success";
                else echo "registeration unsuccessful";
            } else {
                echo "email wasn't sent";
            }
        }
    }
}

if (isset($_POST['loginUsername'])) {
    $allok = true;

    $name = $_POST['loginUsername'];
    $password = $_POST['loginPassword'];


    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $logsql = "SELECT * FROM users WHERE username = '$name';";

        $logresult = mysqli_query($conn, $logsql);

        $logrows = mysqli_num_rows($logresult);

        $arrayOfSalts = [];
        $arrayOfPasswords = [];
        $arrayOfIds = [];
        $foundpass = false;

        if ($logrows > 0) {
            while ($row = mysqli_fetch_assoc($logresult)) {
                array_push($arrayOfSalts, $row['salt']);
                array_push($arrayOfPasswords, $row['password']);
                array_push($arrayOfIds, $row['id']);
            }

            for ($i = 0; $i < count($arrayOfSalts); $i++) {
                if ($arrayOfPasswords[$i] == md5(md5($password) . $arrayOfSalts[$i])) {
                    $foundpass = true;
                    $logsql2 = "SELECT * FROM users WHERE username = '$name' AND password = '$arrayOfPasswords[$i]' AND verified = 1;";

                    $logresult2 = mysqli_query($conn, $logsql2);

                    $logrows2 = mysqli_num_rows($logresult2);

                    if ($logrows2 > 0) {
                        $session = md5(md5($name) . md5($arrayOfPasswords[$i]));
                        $sql = "UPDATE users SET session = '$session' WHERE id = '$arrayOfIds[$i]';";
                        $result = mysqli_query($conn, $sql);

                        if ($result) {
                            if (isset($_POST['localcart'])) {
                                $sqls = "SELECT * FROM users WHERE id = '$arrayOfIds[$i]';";

                                $results = mysqli_query($conn, $sqls);

                                $rowss = mysqli_num_rows($results);

                                $cart;
                                $cartQuan;

                                if ($rowss > 0) {
                                    while ($rowf = mysqli_fetch_assoc($results)) {
                                        $cart = $rowf['cart'];
                                        $cartQuan = $rowf['cartCount'];
                                    }

                                    if ($cart != "") {
                                        $list = explode(", ", $cart);
                                        $list2 = explode(", ", $cartQuan);

                                        $listcart = explode(",", $_POST['localcart']);
                                        $listcartquan = explode(",", $_POST['localcartquan']);

                                        for($o = 0; $o < count($listcart); $o++){
                                            if (in_array($listcart[$o], $list)) {
                                                $list2[array_search($listcart[$o], $list)] = $list2[array_search($listcart[$o], $list)] + $listcartquan[$o];
                                            } else {
                                                array_push($list, $listcart[$o]);
                                                array_push($list2, $listcartquan[$o]);
                                            }
                                        }

                                        $cart = implode(", ", $list);
                                        $cartQuan = implode(", ", $list2);
                                    } else {
                                        $cart = implode(", ", explode(",", $_POST['localcart']));
                                        $cartQuan = implode(", ", explode(",", $_POST['localcartquan']));
                                    }

                                    $sqlx = "UPDATE users SET cart = '" . $cart . "', cartCount = '" . $cartQuan . "' WHERE id = " . $arrayOfIds[$i] . ";";

                                    $resultx = mysqli_query($conn, $sqlx);

                                    if ($resultx) {
                                        echo $session;
                                        echo ', login successful';
                                    } else {
                                        echo 'login unsuccessful';
                                    }
                                } else {
                                    echo 'login unsuccessful';
                                }
                            } else {
                                echo $session;
                                echo ', login successful';
                            }
                        } else {
                            echo 'login unsuccessful';
                        }
                    } else {
                        echo 'not verified';
                    }
                }
            }

            if (!$foundpass) {
                echo 'password is invalid';
            }
        } else {
            echo 'username is invalid';
        }
    }
}

if (isset($_POST['emailEmail'])) {
    $allok = true;

    $email = $_POST['emailEmail'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ressql = "SELECT * FROM users WHERE email = '$email';";

        $resresult = mysqli_query($conn, $ressql);

        $resrows = mysqli_num_rows($resresult);

        if ($resrows > 0) {
            $code = generateRandomString(6);
            $sqlr = "UPDATE users SET resetcode = '$code' WHERE email = '$email';";
            $resultr = mysqli_query($conn, $sqlr);

            if ($resultr) {
                $subject = "Skate Alligent Account Password Reset";
                $message = '<div style="padding: 8px;">
                <link rel="preconnect" href="https://fonts.gstatic.com">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
                <p align="left"><strong><strong><span style="font-family: Arial; font-weight: 600;"><span style="font-size: 30px;"><span style="font-weight: bolder;">SKATE ALLIGENT</span></span></span></strong></strong></p>
                <p><span style="font-family: Arial;"><span style="font-size: 16px;">Hi ' . mysqli_fetch_assoc($resresult)['username'] . ',<br> <br> Your reset code is: <a style="font-weight: bolder"> ' . $code . '</a><br><br> Please also take some time to review our <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/privacy-policy.html">privacy policy</a> and <a href="http://skatealligent.tk/terms-of-use.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/terms-of-use.html">terms of service</a>. </span></span></p>
                <a style="color: white; text-decoration: none; background-color: black; padding: 15px; background-color: black; border-radius: 50px; text-align: center; vertical-align: middle;" href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"><span style="font-family: Arial; font-size: 16px; font-weight: bolder;">skatealligent.tk</span></a>
                <p><span style="font-family: Arial;"><span style=" font-size: 12px;">To change your account preferences, please visit your <a href="http://skatealligent.tk/account.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/account.html">settings page</a>.</span></span></p>
                <table style="width: 210px; height: 20px; background-color: transparent; color: black;" cellspacing="0" cellpadding="0" align="left">
                <tbody>
                <tr>
                <td width="23"><a href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"> <img src="https://i.ibb.co/w4ZQ0bD/black-Icon.png" width="23" height="23"></a></td>
                <td></a>&nbsp;<a style="font-family: Arial; font-weight: 500; color: black;">© 2021 SKATE ALLIGENT</a></td>
                </tr>
                </tbody>
                </table>';
                $message = wordwrap($message, 70);
                $header = "MIME-Version: 1.0" . "\r\n";
                $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $header .= 'From: noreply@skatealligent.tk' . "\r\n";
                //$headers .= 'Cc: myboss@example.com' . "\r\n";

                $sent = mail($email, $subject, $message, $header);
                if ($sent) {
                    echo 'reset successful';
                } else {
                    echo "email wasn't sent";
                }
            } else {
                echo 'reset unsuccessful';
            }
        } else {
            echo 'email is invalid';
        }
    }
}

if (isset($_POST['code'])) {
    $allok = true;

    $code = $_POST['code'];
    $newPassword = $_POST['newPassword'];
    $repeatNewPassword = $_POST['repeatNewPassword'];
    $email = $_POST['passEmail'];

    if ($newPassword != $repeatNewPassword) $allok = false;

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ressql = "SELECT * FROM users WHERE email = '$email';";

        $resresult = mysqli_query($conn, $ressql);

        $resrows = mysqli_num_rows($resresult);

        $resetcode;
        $salt;

        while ($row = mysqli_fetch_assoc($resresult)) {
            $resetcode = $row['resetcode'];
            $salt = $row['salt'];
        }


        if ($resrows > 0 && $resetcode == $code) {

            $pass = md5(md5($newPassword) . $salt);
            $sqlp = "UPDATE users SET password = '$pass' WHERE email = '$email';";
            $sqlpa = "UPDATE users SET resetcode = '' WHERE email = '$email';";
            $resultp = mysqli_query($conn, $sqlp);
            $resultpa = mysqli_query($conn, $sqlpa);

            if ($resultp && $resultpa) {
                echo 'password change successful';
            } else {
                echo 'password change unsuccessful';
            }
        } else {
            echo 'code is invalid';
        }
    } else {
        echo "passwords don't match";
    }
}

if (isset($_POST['newUser'])) {
    $allok = true;

    $id = $_POST['newUserId'];
    $name = $_POST['newUser'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ssql = "SELECT * FROM users where username = '" . $name . "';";
        $rresult = mysqli_query($conn, $ssql);
        $rows = mysqli_num_rows($rresult);

        if ($rows == 0) {
            $sql = "UPDATE users SET username = '$name' WHERE id = '$id';";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo 'change successful';
            } else {
                echo 'change unsuccessful';
            }
        } else {
            echo "username already exists";
        }
    }
}

if (isset($_POST['newEmail'])) {
    $allok = true;

    $id = $_POST['newEmailId'];
    $name = $_POST['newEmail'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ssql = "SELECT * FROM users where email = '" . $name . "';";
        $rresult = mysqli_query($conn, $ssql);
        $rows = mysqli_num_rows($rresult);

        if ($rows == 0) {
            $sql = "UPDATE users SET email = '$name' WHERE id = '$id';";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo 'change successful';
            } else {
                echo 'change unsuccessful';
            }
        } else {
            echo "email already exists";
        }
    }
}

if (isset($_POST['newPass'])) {
    $allok = true;

    $id = $_POST['newPassId'];
    $name = $_POST['newPass'];
    $currPass = $_POST['currPass'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ssql = "SELECT * FROM users where id = '" . $id . "';";
        $rresult = mysqli_query($conn, $ssql);
        $rows = mysqli_num_rows($rresult);

        $salt;
        $pass;

        if ($rows > 0) {
            while ($row = mysqli_fetch_assoc($rresult)) {
                $salt = $row['salt'];
                $pass = $row['password'];
            }

            if ($pass == md5(md5($currPass) . $salt)) {
                $sql = "UPDATE users SET password = '" . md5(md5($name) . $salt) . "' WHERE id = '$id';";

                $result = mysqli_query($conn, $sql);

                if ($result) {
                    echo 'change successful';
                } else {
                    echo 'change unsuccessful';
                }
            } else {
                echo "current password is incorrect";
            }
        }
    }
}

if (isset($_POST['newNum'])) {
    $allok = true;

    $id = $_POST['newNumId'];
    $name = $_POST['newNum'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ssql = "SELECT * FROM users where pnumber = '" . $name . "';";
        $rresult = mysqli_query($conn, $ssql);
        $rows = mysqli_num_rows($rresult);

        if ($rows == 0) {
            $sql = "UPDATE users SET pnumber = '$name' WHERE id = '$id';";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo 'change successful';
            } else {
                echo 'change unsuccessful';
            }
        } else {
            echo "number already exists";
        }
    }
}

if (isset($_POST['deleteAccId'])) {
    $allok = true;

    $id = $_POST['deleteAccId'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $token = md5(generateRandomString(100));

        $sql1 = "SELECT * FROM users WHERE id = '$id';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $name;
        $email;

        if ($rows1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $name = $row['username'];
                $email = $row['email'];
            }
        }

        if ($rows1 > 0) {
            $sql = "UPDATE users SET verifytoken = '$token' WHERE id = '$id';";
            $result = mysqli_query($conn, $sql);

            $subject = "Skate Alligent Account Account Deletion";
            $message = '<div style="padding: 8px;">
            <link rel="preconnect" href="https://fonts.gstatic.com">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
            <p align="left"><strong><strong><span style="font-family: Arial; font-weight: 600;"><span style="font-size: 30px;"><span style="font-weight: bolder;">SKATE ALLIGENT</span></span></span></strong></strong></p>
            <p><span style="font-family: Arial;"><span style="font-size: 16px;">Hi ' . $name . ',<br> <br> Sorry to hear you are planning to delete your account, if you have any complaints you can email as at <a href="mailto:noreply@skatealligent.tk">noreply@skatealligent.tk</a> instead.<br> To delete your account, <a href="http://skatealligent.tk/php/delete.php?id=' . $id . '&token=' . $token . '" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/php/delete.php?email=' . $email . '&token=' . $token . '">please click here.<br><br></a> Please also take some time to review our <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/privacy-policy.html">privacy policy</a> and <a href="http://skatealligent.tk/terms-of-use.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/terms-of-use.html">terms of service</a>. </span></span></p>
            <a style="color: white; text-decoration: none; background-color: black; padding: 15px; background-color: black; border-radius: 50px; text-align: center; vertical-align: middle;" href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"><span style="font-family: Arial; font-size: 16px; font-weight: bolder;">skatealligent.tk</span></a>
            <p><span style="font-family: Arial;"><span style=" font-size: 12px;">To change your account preferences, please visit your <a href="http://skatealligent.tk/account.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/account.html">settings page</a>.</span></span></p>
            <table style="width: 210px; height: 20px; background-color: transparent; color: black;" cellspacing="0" cellpadding="0" align="left">
            <tbody>
            <tr>
            <td width="23"><a href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"> <img src="https://i.ibb.co/w4ZQ0bD/black-Icon.png" width="23" height="23"></a></td>
            <td></a>&nbsp;<a style="font-family: Arial; font-weight: 500; color: black;">© 2021 SKATE ALLIGENT</a></td>
            </tr>
            </tbody>
            </table>';
            $message = wordwrap($message, 70);
            $header = "MIME-Version: 1.0" . "\r\n";
            $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $header .= 'From: noreply@skatealligent.tk' . "\r\n";
            //$headers .= 'Cc: myboss@example.com' . "\r\n";

            $sent = mail($email, $subject, $message, $header);
            if ($sent) {
                if ($result) echo "deletion success";
                else echo "deletion unsuccessful";
            } else {
                print_r(error_get_last());
                echo "email wasn't sent" . error_get_last()['message'];
            }
        }
    }
}

if (isset($_POST['cartUserId'])) {
    $allok = true;

    $id = $_POST['cartUserId'];
    $prodId = $_POST['prodId'];
    $quantity = $_POST['cartProdQuan'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $token = md5(generateRandomString(100));

        $sql1 = "SELECT * FROM users WHERE id = '$id';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $cart;
        $cartQuan;

        if ($rows1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $cart = $row['cart'];
                $cartQuan = $row['cartCount'];
            }

            if ($cart != "") {
                $list = explode(", ", $cart);
                $list2 = explode(", ", $cartQuan);

                if (in_array($prodId, $list)) {
                    $list2[array_search($prodId, $list)] = $list2[array_search($prodId, $list)] + $quantity;
                } else {
                    array_push($list, $prodId);
                    array_push($list2, $quantity);
                }

                $cart = implode(", ", $list);
                $cartQuan = implode(", ", $list2);
            } else {
                $cart = $prodId;
                $cartQuan = $quantity;
            }

            $sql = "UPDATE users SET cart = '" . $cart . "', cartCount = '" . $cartQuan . "' WHERE id = " . $id . ";";

            $result = mysqli_query($conn, $sql);


            if ($result) {
                echo "added to cart";
            } else {
                echo "couldn't add to cart";
            }
        } else {
            echo "couldn't find user";
        }
    }
}

if (isset($_POST['cartItemId'])) {
    $allok = true;

    $id = $_POST['cartItemId'];
    $prodID = $_POST['cartItemID'];
    $quantity = $_POST['cartItemQuan'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $token = md5(generateRandomString(100));

        $sql1 = "SELECT * FROM users WHERE id = '$id';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $cart;
        $cartQuan;

        if ($rows1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $cart = $row['cart'];
                $cartQuan = $row['cartCount'];
            }

            $list = explode(", ", $cart);
            $list2 = explode(", ", $cartQuan);

            unset($list2[array_search($prodID, $list)]);
            unset($list[array_search($prodID, $list)]);

            $cart = implode(", ", $list);
            $cartQuan = implode(", ", $list2);

            $sql = "UPDATE users SET cart = '" . $cart . "', cartCount = '" . $cartQuan . "' WHERE id = " . $id . ";";

            $result = mysqli_query($conn, $sql);


            if ($result) {
                echo "removed from cart";
            } else {
                echo "couldn't remove from cart";
            }
        }
    }
}

if (isset($_POST['cartQuanUId'])) {
    $allok = true;

    $id = $_POST['cartQuanUId'];
    $prodID = $_POST['cartQuanID'];
    $quantity = $_POST['cartQuant'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $token = md5(generateRandomString(100));

        $sql1 = "SELECT * FROM users WHERE id = '$id';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $cart;
        $cartQuan;

        if ($rows1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $cart = $row['cart'];
                $cartQuan = $row['cartCount'];
            }

            $list = explode(", ", $cart);
            $list2 = explode(", ", $cartQuan);

            $list2[array_search($prodID, $list)] = $quantity;

            $cart = implode(", ", $list);
            $cartQuan = implode(", ", $list2);

            $sql = "UPDATE users SET cart = '" . $cart . "', cartCount = '" . $cartQuan . "' WHERE id = " . $id . ";";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo "Changed Quantity";
            } else {
                echo "couldn't Change Quantity";
            }
        }
    }
}

if (isset($_POST['uid'])) {
    $allok = true;

    $id = $_POST['uid'];
    $shipping = $_POST['orderShipping'];
    $comments = $_POST['orderComments'];

    $adId = $_POST['adId'];
    $apartment = $_POST['apartment'];
    $building = $_POST['building'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $region = $_POST['region'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $sql = "UPDATE addresses SET apartment = '$apartment', building = '$building', street = '$street', city = '$city', region = '$region' WHERE id = '$adId';";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            if (isset($_POST['uid'])) {
                $uid = $_POST['uid'];
                $nom = $_POST['number'];
                $fnom = $_POST['FName'];
                $lnom = $_POST['LName'];

                $sql2 = "UPDATE users SET pnumber = '$nom', FName = '$fnom', LName = '$lnom' WHERE id = '$uid';";

                $result2 = mysqli_query($conn, $sql2);
                if ($result2) {
                    $allok = true;
                } else {
                    $allok = false;
                }
            } else $allok = false;
        } else {
            echo $allok = false;
        }
    }

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $sql1 = "SELECT * FROM users WHERE id = '$id';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $price;
        $products;
        $productsNames = [];
        $productsPrices = [];
        $productsSales = [];
        $productsStocks = [];
        $quantity;

        if ($rows1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $products = $row['cart'];
                $quantity = $row['cartCount'];
            }

            $products = explode(", ", $products);
            $quantity = explode(", ", $quantity);

            for ($i = 0; $i < count($products); $i++) {
                $sql2 = "SELECT * FROM Products WHERE id = '$products[$i]';";

                $result2 = mysqli_query($conn, $sql2);

                $rows2 = mysqli_num_rows($result2);

                if ($rows2 > 0) {
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                        array_push($productsNames, $row2['Name']);
                        array_push($productsPrices, $row2['Price']);
                        array_push($productsSales, $row2['Sale']);
                        array_push($productsStocks, $row2['Stock']);
                    }
                }
            }

            $names = implode(", ", $productsNames);

            $subtotalPrice = intval($shipping);
            for ($i = 0; $i < count($productsPrices); $i++) {
                $subtotalPrice += $productsPrices[$i] * $quantity[$i];
            }

            $sale;
            for ($i = 0; $i < count($productsSales); $i++) {
                $subtotalPrice -= $productsSales[$i] * $quantity[$i];
            }

            $totalPrice = $subtotalPrice - $sale;

            $names = urlencode($names);

            $comments = urlencode($comments);

            $quan = implode(", ", $quantity);

            $sql = "INSERT INTO orders (Products, price, quantity, status, userid, comments) VALUES ('$names', '$totalPrice', '$quan', 'Placed', '$id', '$comments');";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                $result3s = [];
                for ($i = 0; $i < count($products); $i++) {
                    $stock = $productsStocks[$i] - $quantity[$i];
                    if ($stock < 0) {
                        $understock = $quantity[$i] + $stock;
                        $stock = $productsStocks[$i] - $understock;
                    }

                    $sql3 = "UPDATE Products SET Stock = '$stock' WHERE ID = " . $products[$i] . ";";

                    $result3 = mysqli_query($conn, $sql3);
                    array_push($result3s, strval($result3));
                }

                $result3s = implode(", ", $result3s);

                if (strpos($result3s, "false") == null) {
                    $sql4 = "UPDATE users SET cart = '', cartCount = '' WHERE id = " . $id . ";";

                    $result4 = mysqli_query($conn, $sql4);
                    if ($result4) {

                        $sqluser = "SELECT * FROM users WHERE id = " . $id . ";";
                        $sqluseresult = mysqli_query($conn, $sqluser);
                        if ($sqluseresult) {
                            $uname;
                            $uemail;
                            $uphone;
                            $uaddress;
                            $date;
                            $oid;
                            while ($rowuser = mysqli_fetch_assoc($sqluseresult)) {
                                $uname = $rowuser['FName'] . " " . $rowuser['LName'];
                                $uemail = $rowuser['email'];
                                $uphone = $rowuser['pnumber'];
                            }
                            $sqlad = "SELECT * FROM addresses WHERE id = " . $id . ";";
                            $sqladresult = mysqli_query($conn, $sqlad);
                            if ($sqladresult) {
                                while ($rowad = mysqli_fetch_assoc($sqladresult)) {
                                    $uaddress = $rowad['apartment'] . ', ' . $rowad['building'] . ', ' . $rowad['street'] . ', ' . $rowad['city'] . ', ' . $rowad['region'];
                                }
                                $sqldate = "SELECT * FROM orders WHERE userid = " . $id . " ORDER BY id Desc;";
                                $sqldateresult = mysqli_query($conn, $sqldate);
                                $iti = 0;
                                if ($sqldateresult) {
                                    while ($rowdate = mysqli_fetch_assoc($sqldateresult)) {
                                        if ($iti == 0) {
                                            $date = substr($rowdate['date'], 0, strpos($rowdate['date'], " "));
                                            $oid = $rowdate['id'];
                                        }
                                        $iti++;
                                    }

                                    $sqlord = "SELECT * FROM orders WHERE userid = " . $id . " ORDER BY id Desc;";
                                    $queryord = mysqli_query($conn, $sqlord);
                                    $rowsord = mysqli_num_rows($queryord);

                                    $it;

                                    $productList = [];
                                    while ($roword = mysqli_fetch_assoc($queryord)) {
                                        if ($it == 0) {
                                            $product;
                                            $quant;

                                            $prods = urldecode($roword['Products']);
                                            $quant = $roword['quantity'];

                                            $quant = explode(", ", $quant);
                                            $product = explode(", ", $prods);;
                                            for ($i = 0; $i < count($product); $i++) {
                                                $sqlprod = "SELECT * FROM Products WHERE Name = '" . $product[$i] . "';";
                                                $queryprod = mysqli_query($conn, $sqlprod);
                                                $rowsprod = mysqli_num_rows($queryprod);
                                                if ($rowsprod > 0) {
                                                    while ($rowprods = mysqli_fetch_assoc($queryprod)) {
                                                        $productItem;
                                                        if ($rowprods['Sale'] > 0) {
                                                            $productItem = '<p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">';
                                                            $productItem .= '<span style="display:block;font-size:13px;font-weight:normal;">' . $quant[$i] . "x " . $product[$i] . '</span><br> EGP ' . (($rowprods['Price'] * $quant[$i]) - ($rowprods['Sale'] * $quant[$i])) . ' <s style="color:grey">EGP ' . ($rowprods['Price'] * $quant[$i]) . '</s>';
                                                            $productItem .= '</p>';
                                                        } else {
                                                            $productItem = '<p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">';
                                                            $productItem .= '<span style="display:block;font-size:13px;font-weight:normal;">' . $quant[$i] . "x " . $product[$i] . '</span><br> EGP ' . ($rowprods['Price'] * $quant[$i]) . '';
                                                            $productItem .= '</p>';
                                                        }
                                                        array_push($productList, $productItem);
                                                    }
                                                }
                                            }
                                        }
                                        $it++;
                                    }
                                    $msg =                         '<body style="background-color:#e2e1e0;font-family: Open Sans, sans-serif;font-size:100%;font-weight:400;line-height:1.4;color:#000;padding-top:50px">
                                    <table style="max-width:670px;margin:50px auto 10px;background-color:#fff;padding:50px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);-moz-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24); border-top: solid 10px #160a22;">
                                      <thead>
                                        <tr>
                                          <th style="text-align:left;"><a href="http://skatealligent.tk" target="_blank"><img style="max-width: 50px;" src="https://i.ibb.co/821hSZW/black-Logo.png" alt="Skate Alligent"></a></th>
                                          <th style="text-align:right;font-weight:400;">' . $date . '</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td style="height:35px;"></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="border: solid 1px #ddd; padding:10px 20px;">
                                            <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Order status</span><b style="color:green;font-weight:normal;margin:0">Placed</b></p>
                                            <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Order ID</span> ' . $oid . '</p>
                                            <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Order amount</span> EGP ' . $totalPrice . '</p>
                                            <p style="font-size:14px;margin:0 0 0 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Track Status</span> <a href="http://skatealligent.tk/account.html?pg=orders" target="_blank">Click Here</a></p>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td style="height:35px;"></td>
                                        </tr>
                                        <tr>
                                          <td style="width:50%;padding:20px;vertical-align:top">
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px">Name</span> ' . $uname . '</p>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Email</span> ' . $uemail . '</p>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Phone Number</span> +20-' . substr($uphone, 1) . '</p>
                                          </td>
                                          <td style="width:50%;padding:20px;vertical-align:top">
                                            <br>
                                            <br>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Address</span> ' . $uaddress . '</p>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;">
                                          </td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="font-size:20px;padding:30px 15px 0 15px;">Items</td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="padding:15px;">';

                                    for ($i = 0; $i < count($productList); $i++) {
                                        $msg .= $productList[$i];
                                    }

                                    $msg .= '<p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">
                                                <span style="display:block;font-size:13px;font-weight:normal;">Shipping</span> <br>EGP ' . intval($shipping) . '
                                                </p>
                                                <p style="font-size:14px;margin:0;padding:10px;">
                                            <br>Comments: ' . urldecode($comments) . '
                                            </p>
                                            </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding:15px;">
                                            <p style="font-size:14px;margin:0;padding:10px;text-align:center;font-weight:bold;">
                                                We will be contacting you on +20-' . substr($uphone, 1) . ' soon to confirm your order.
                                                </p>
                                            </td>
                                                </tr>
                                        </tbody>
                                        <tfooter>
                                            <tr>
                                            <td colspan="2" style="font-size:14px;padding:50px 15px 0 15px;">
                                                <strong style="display:block;margin:0 0 10px 0;">Regards<br><br><a href="http://skatealligent.tk" target="_blank" style="text-decoration: none; color: black">Skate Alligent</a></strong>
                                                <b>Phone:</b> <a href="tel:+201008835438">+20-1008835438</a><br>
                                                <b>Email:</b> <a href="mailto:contact@skatealligent.tk">contact@skatealligent.tk</a>
                                            </td>
                                            </tr>
                                        </tfooter>
                                        </table>
                                        <div class="footer" style="width: auto; text-align: center; margin-top: 20px; display: block">
                                        <p style="font-size:13px; margin-top: 20px; display: block;">Need help? <a href="mailto:contact@skatealligent.tk" target="_blank" style="color: #037aee;">contact@skatealligent.tk</a></p>
                                        <a style="font-size:13px; margin-top: 20px; display: block;">© 2021, Skate Alligent, All rights reserved.</a>
                                        <p style="font-size:13px; margin-top: 10px; display: block;"><a href="http://skatealligent.tk/terms-of-use.html" target="_blank" style="color: #037aee;">Terms of Service</a> | <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" style="color: #037aee;">Privacy Policy</a></p><br>
                                        </div>
                                    </body>';
                                    $subject = "Skate Alligent Order $oid Receipt";
                                    $msg = wordwrap($msg, 70);
                                    $header = "MIME-Version: 1.0" . "\r\n";
                                    $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                    $header .= 'From: noreply@skatealligent.tk' . "\r\n";
                                    //$headers .= 'Cc: myboss@example.com' . "\r\n";

                                    $sent = mail($uemail, $subject, $msg, $header);
                                    if ($sent) {
                                        $sent2 = mail("mfergany101@gmail.com", $subject, $msg, $header);
                                        if ($sent2) {
                                            echo "Placed Order";
                                        } else {
                                            echo "couldn't Place Order";
                                            echo " no 8";
                                        }
                                    } else {
                                        echo "couldn't Place Order";
                                        echo " no 7";
                                    }
                                } else {
                                    echo "couldn't Place Order";
                                    echo " no 6";
                                }
                            } else {
                                echo "couldn't Place Order";
                                echo " no 5";
                            }
                        } else {
                            echo "couldn't Place Order";
                            echo " no 4";
                        }
                    } else {
                        echo "couldn't Place Order";
                        echo " no 3";
                    }
                } else {
                    echo "couldn't Place Order";
                    echo " no 2";
                }
            } else {
                echo "couldn't Place Order";
                echo " no 1";
            }
        }
    }
}

if (isset($_POST['newFName'])) {
    $allok = true;

    $id = $_POST['newFNameId'];
    $name = $_POST['newFName'];
    $lname = $_POST['newLName'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ssql = "SELECT * FROM users where id = " . $id . ";";
        $rresult = mysqli_query($conn, $ssql);
        $rows = mysqli_num_rows($rresult);

        if ($rows > 0) {
            $sql = "UPDATE users SET FName = '$name', LName = '$lname' WHERE id = '$id';";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo 'change successful';
            } else {
                echo 'change unsuccessful';
            }
        } else {
            echo 'change unsuccessful';
        }
    }
}

if (isset($_POST['checkEmailExists'])) {
    $allok = true;

    $email = $_POST['checkEmailExists'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $ssql = "SELECT * FROM users where email = '" . $email . "';";
        $rresult = mysqli_query($conn, $ssql);
        $rows = mysqli_num_rows($rresult);

        if ($rows > 0) {
            echo 'address is in use';
        } else {
            echo 'address is ok';
        }
    }
}

if (isset($_POST['setOrderEmail'])) {
    $allok = true;

    $id;
    $email = $_POST['setOrderEmail'];
    $session = md5(md5($email) . md5(generateRandomString(rand(5, 500))));
    echo $session . ", ";
    $shipping = $_POST['orderShipping'];
    $comments = $_POST['orderComments'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $salt = md5(generateRandomString(rand(5, 50)));

        $token = md5($email);

        $sql = "INSERT INTO users(email, salt, verifytoken, session) VALUES('$email', '$salt' , '$token', '$session')";
        $sql3 = "INSERT INTO addresses() VALUES()";
        $result = mysqli_query($conn, $sql);
        $result3 = mysqli_query($conn, $sql3);

        $subject = "Skate Alligent Account Setup";
        $message = '<div style="padding: 8px;">
            <link rel="preconnect" href="https://fonts.gstatic.com">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
            <p align="left"><strong><strong><span style="font-family: Arial; font-weight: 600;"><span style="font-size: 30px;"><span style="font-weight: bolder;">SKATE ALLIGENT</span></span></span></strong></strong></p>
            <p><span style="font-family: Arial;"><span style="font-size: 16px;">Hi ' . $name . ',<br> <br> Welcome and thanks for signing up.<br> To continue the setup of your account, <a href="http://skatealligent.tk/php/verify.php?email=' . $email . '&token=' . $token . '" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/php/verify.php?email=' . $email . '&token=' . $token . '">please click here.<br><br></a> Please also take some time to review our <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/privacy-policy.html">privacy policy</a> and <a href="http://skatealligent.tk/terms-of-use.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/terms-of-use.html">terms of service</a>. </span></span></p>
            <a style="color: white; text-decoration: none; background-color: black; padding: 15px; background-color: black; border-radius: 50px; text-align: center; vertical-align: middle;" href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"><span style="font-family: Arial; font-size: 16px; font-weight: bolder;">skatealligent.tk</span></a>
            <p><span style="font-family: Arial;"><span style=" font-size: 12px;">To change your account preferences, please visit your <a href="http://skatealligent.tk/account.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/account.html">settings page</a>.</span></span></p>
            <table style="width: 210px; height: 20px; background-color: transparent; color: black;" cellspacing="0" cellpadding="0" align="left">
            <tbody>
            <tr>
            <td width="23"><a href="http://skatealligent.tk" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk"> <img src="https://i.ibb.co/w4ZQ0bD/black-Icon.png" width="23" height="23"></a></td>
            <td></a>&nbsp;<a style="font-family: Arial; font-weight: 500; color: black;">© 2021 SKATE ALLIGENT</a></td>
            </tr>
            </tbody>
            </table>';
        $message = wordwrap($message, 70);
        $header = "MIME-Version: 1.0" . "\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $header .= 'From: noreply@skatealligent.tk' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";

        $sent = mail($email, $subject, $message, $header);
        if ($sent) {
            if ($result && $result3) $allok = true;
            else $allok = false;
        } else {
            $allok = false;
        }
    }

    $sqlxxyyzz = "SELECT * FROM users WHERE email = '$email';";

    $resultxx = mysqli_query($conn, $sqlxxyyzz);

    $rowsxx = mysqli_num_rows($resultxx);

    if ($rowsxx > 0) {
        while ($row = mysqli_fetch_assoc($resultxx)) {
            $id = $row['id'];
        }
    }

    $apartment = $_POST['apartment'];
    $building = $_POST['building'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $region = $_POST['region'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $sql = "UPDATE addresses SET apartment = '$apartment', building = '$building', street = '$street', city = '$city', region = '$region' WHERE id = '$id';";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $nom = $_POST['number'];
            $fnom = $_POST['FName'];
            $lnom = $_POST['LName'];

            $sql2 = "UPDATE users SET pnumber = '$nom', FName = '$fnom', LName = '$lnom' WHERE email = '$email';";

            $result2 = mysqli_query($conn, $sql2);
            if ($result2) {
                echo $allok = true;
            } else {
                echo $allok = false;
            }
        } else {
            echo $allok = false;
        }
    }

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $sql1 = "SELECT * FROM users WHERE email = '$email';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);

        $price;
        $products;
        $productsNames = [];
        $productsPrices = [];
        $productsSales = [];
        $productsStocks = [];
        $quantity;

        if ($rows1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $products = $_POST['cart'];
                $quantity = $_POST['quan'];
                $id = $row['id'];
            }

            $products = explode(",", $products);
            $quantity = explode(",", $quantity);

            for ($i = 0; $i < count($products); $i++) {
                $sql2 = "SELECT * FROM Products WHERE id = '$products[$i]';";

                $result2 = mysqli_query($conn, $sql2);

                $rows2 = mysqli_num_rows($result2);

                if ($rows2 > 0) {
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                        array_push($productsNames, $row2['Name']);
                        array_push($productsPrices, $row2['Price']);
                        array_push($productsSales, $row2['Sale']);
                        array_push($productsStocks, $row2['Stock']);
                    }
                }
            }

            $names = implode(", ", $productsNames);

            $subtotalPrice = intval($shipping);
            for ($i = 0; $i < count($productsPrices); $i++) {
                $subtotalPrice += $productsPrices[$i] * $quantity[$i];
            }

            $sale;
            for ($i = 0; $i < count($productsSales); $i++) {
                $subtotalPrice -= $productsSales[$i] * $quantity[$i];
            }

            $totalPrice = $subtotalPrice - $sale;

            $names = urlencode($names);

            $comments = urlencode($comments);

            $quan = implode(", ", $quantity);

            $sql = "INSERT INTO orders (Products, price, quantity, status, userid, comments) VALUES ('$names', '$totalPrice', '$quan', 'Placed', '$id', '$comments');";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                $result3s = [];
                for ($i = 0; $i < count($products); $i++) {
                    $stock = $productsStocks[$i] - $quantity[$i];
                    if ($stock < 0) {
                        $understock = $quantity[$i] + $stock;
                        $stock = $productsStocks[$i] - $understock;
                    }

                    $sql3 = "UPDATE Products SET Stock = '$stock' WHERE ID = " . $products[$i] . ";";

                    $result3 = mysqli_query($conn, $sql3);
                    array_push($result3s, strval($result3));
                }

                $result3s = implode(", ", $result3s);

                if (strpos($result3s, "false") == null) {
                    $sql4 = "UPDATE users SET cart = '', cartCount = '' WHERE id = " . $id . ";";

                    $result4 = mysqli_query($conn, $sql4);
                    if ($result4) {

                        $sqluser = "SELECT * FROM users WHERE id = " . $id . ";";
                        $sqluseresult = mysqli_query($conn, $sqluser);
                        if ($sqluseresult) {
                            $uname;
                            $uemail;
                            $uphone;
                            $uaddress;
                            $date;
                            $oid;
                            while ($rowuser = mysqli_fetch_assoc($sqluseresult)) {
                                $uname = $rowuser['FName'] . " " . $rowuser['LName'];
                                $uemail = $rowuser['email'];
                                $uphone = $rowuser['pnumber'];
                            }
                            $sqlad = "SELECT * FROM addresses WHERE id = " . $id . ";";
                            $sqladresult = mysqli_query($conn, $sqlad);
                            if ($sqladresult) {
                                while ($rowad = mysqli_fetch_assoc($sqladresult)) {
                                    $uaddress = $rowad['apartment'] . ', ' . $rowad['building'] . ', ' . $rowad['street'] . ', ' . $rowad['city'] . ', ' . $rowad['region'];
                                }
                                $sqldate = "SELECT * FROM orders WHERE userid = " . $id . " ORDER BY id Desc;";
                                $sqldateresult = mysqli_query($conn, $sqldate);
                                $iti = 0;
                                if ($sqldateresult) {
                                    while ($rowdate = mysqli_fetch_assoc($sqldateresult)) {
                                        if ($iti == 0) {
                                            $date = substr($rowdate['date'], 0, strpos($rowdate['date'], " "));
                                            $oid = $rowdate['id'];
                                        }
                                        $iti++;
                                    }

                                    $sqlord = "SELECT * FROM orders WHERE userid = " . $id . " ORDER BY id Desc;";
                                    $queryord = mysqli_query($conn, $sqlord);
                                    $rowsord = mysqli_num_rows($queryord);

                                    $it;

                                    $productList = [];
                                    while ($roword = mysqli_fetch_assoc($queryord)) {
                                        if ($it == 0) {
                                            $product;
                                            $quant;

                                            $prods = urldecode($roword['Products']);
                                            $quant = $roword['quantity'];

                                            $quant = explode(", ", $quant);
                                            $product = explode(", ", $prods);;
                                            for ($i = 0; $i < count($product); $i++) {
                                                $sqlprod = "SELECT * FROM Products WHERE Name = '" . $product[$i] . "';";
                                                $queryprod = mysqli_query($conn, $sqlprod);
                                                $rowsprod = mysqli_num_rows($queryprod);
                                                if ($rowsprod > 0) {
                                                    while ($rowprods = mysqli_fetch_assoc($queryprod)) {
                                                        $productItem;
                                                        if ($rowprods['Sale'] > 0) {
                                                            $productItem = '<p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">';
                                                            $productItem .= '<span style="display:block;font-size:13px;font-weight:normal;">' . $quant[$i] . "x " . $product[$i] . '</span><br> EGP ' . (($rowprods['Price'] * $quant[$i]) - ($rowprods['Sale'] * $quant[$i])) . ' <s style="color:grey">EGP ' . ($rowprods['Price'] * $quant[$i]) . '</s>';
                                                            $productItem .= '</p>';
                                                        } else {
                                                            $productItem = '<p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">';
                                                            $productItem .= '<span style="display:block;font-size:13px;font-weight:normal;">' . $quant[$i] . "x " . $product[$i] . '</span><br> EGP ' . ($rowprods['Price'] * $quant[$i]) . '';
                                                            $productItem .= '</p>';
                                                        }
                                                        array_push($productList, $productItem);
                                                    }
                                                }
                                            }
                                        }
                                        $it++;
                                    }
                                    $msg =                         '<body style="background-color:#e2e1e0;font-family: Open Sans, sans-serif;font-size:100%;font-weight:400;line-height:1.4;color:#000;padding-top:50px">
                                    <table style="max-width:670px;margin:50px auto 10px;background-color:#fff;padding:50px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);-moz-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24); border-top: solid 10px #160a22;">
                                      <thead>
                                        <tr>
                                          <th style="text-align:left;"><a href="http://skatealligent.tk" target="_blank"><img style="max-width: 50px;" src="https://i.ibb.co/821hSZW/black-Logo.png" alt="Skate Alligent"></a></th>
                                          <th style="text-align:right;font-weight:400;">' . $date . '</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td style="height:35px;"></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="border: solid 1px #ddd; padding:10px 20px;">
                                            <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Order status</span><b style="color:green;font-weight:normal;margin:0">Placed</b></p>
                                            <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Order ID</span> ' . $oid . '</p>
                                            <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Order amount</span> EGP ' . $totalPrice . '</p>
                                            <p style="font-size:14px;margin:0 0 0 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Track Status</span> <a href="http://skatealligent.tk/account.html?pg=orders" target="_blank">Click Here</a></p>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td style="height:35px;"></td>
                                        </tr>
                                        <tr>
                                          <td style="width:50%;padding:20px;vertical-align:top">
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px">Name</span> ' . $uname . '</p>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Email</span> ' . $uemail . '</p>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Phone Number</span> +20-' . substr($uphone, 1) . '</p>
                                          </td>
                                          <td style="width:50%;padding:20px;vertical-align:top">
                                            <br>
                                            <br>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Address</span> ' . $uaddress . '</p>
                                            <p style="margin:0 0 10px 0;padding:0;font-size:14px;">
                                          </td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="font-size:20px;padding:30px 15px 0 15px;">Items</td>
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="padding:15px;">';

                                    for ($i = 0; $i < count($productList); $i++) {
                                        $msg .= $productList[$i];
                                    }

                                    $msg .= '<p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">
                                                <span style="display:block;font-size:13px;font-weight:normal;">Shipping</span> <br>EGP ' . intval($shipping) . '
                                                </p>
                                                <p style="font-size:14px;margin:0;padding:10px;">
                                            <br>Comments: ' . urldecode($comments) . '
                                            </p>
                                            </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding:15px;">
                                            <p style="font-size:14px;margin:0;padding:10px;text-align:center;font-weight:bold;">
                                                We will be contacting you on +20-' . substr($uphone, 1) . ' soon to confirm your order.
                                                </p>
                                            </td>
                                                </tr>
                                        </tbody>
                                        <tfooter>
                                            <tr>
                                            <td colspan="2" style="font-size:14px;padding:50px 15px 0 15px;">
                                                <strong style="display:block;margin:0 0 10px 0;">Regards<br><br><a href="http://skatealligent.tk" target="_blank" style="text-decoration: none; color: black">Skate Alligent</a></strong>
                                                <b>Phone:</b> <a href="tel:+201008835438">+20-1008835438</a><br>
                                                <b>Email:</b> <a href="mailto:contact@skatealligent.tk">contact@skatealligent.tk</a>
                                            </td>
                                            </tr>
                                        </tfooter>
                                        </table>
                                        <div class="footer" style="width: auto; text-align: center; margin-top: 20px; display: block">
                                        <p style="font-size:13px; margin-top: 20px; display: block;">Need help? <a href="mailto:contact@skatealligent.tk" target="_blank" style="color: #037aee;">contact@skatealligent.tk</a></p>
                                        <a style="font-size:13px; margin-top: 20px; display: block;">© 2021, Skate Alligent, All rights reserved.</a>
                                        <p style="font-size:13px; margin-top: 10px; display: block;"><a href="http://skatealligent.tk/terms-of-use.html" target="_blank" style="color: #037aee;">Terms of Service</a> | <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" style="color: #037aee;">Privacy Policy</a></p><br>
                                        </div>
                                    </body>';
                                    $subject = "Skate Alligent Order $oid Receipt";
                                    $msg = wordwrap($msg, 70);
                                    $header = "MIME-Version: 1.0" . "\r\n";
                                    $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                    $header .= 'From: noreply@skatealligent.tk' . "\r\n";
                                    //$headers .= 'Cc: myboss@example.com' . "\r\n";

                                    $sent = mail($uemail, $subject, $msg, $header);
                                    if ($sent) {
                                        $sent2 = true;
                                        if ($sent2) {
                                            echo "Placed Order";
                                        } else {
                                            echo "couldn't Place Order";
                                            echo " no 8";
                                        }
                                    } else {
                                        echo "couldn't Place Order";
                                        echo " no 7";
                                    }
                                } else {
                                    echo "couldn't Place Order";
                                    echo " no 6";
                                }
                            } else {
                                echo "couldn't Place Order";
                                echo " no 5";
                            }
                        } else {
                            echo "couldn't Place Order";
                            echo " no 4";
                        }
                    } else {
                        echo "couldn't Place Order";
                        echo " no 3";
                    }
                } else {
                    echo "couldn't Place Order";
                    echo " no 2";
                }
            } else {
                echo "couldn't Place Order";
                echo " no 1";
            }
        }
    }
}

if (isset($_POST['newAccUsername'])) {
    $allok = true;

    $name = $_POST['newAccUsername'];
    $password = $_POST['newPassword'];
    $email = $_POST['email'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $salt;

        $sql1 = "SELECT * FROM users WHERE email = '$email';";

        $result1 = mysqli_query($conn, $sql1);

        $rows1 = mysqli_num_rows($result1);
        while ($roww1 = mysqli_fetch_assoc($result1)) {
            $salt = $roww1['salt'];
        }

        $pass = md5(md5($password) . $salt);

        $sql2 = "SELECT * FROM users WHERE username = '$name';";

        $result2 = mysqli_query($conn, $sql2);

        $rows2 = mysqli_num_rows($result2);

        if ($rows2 > 0) {
            echo "username is in use";
        } else if ($rows2 == 0) {
            $sql = "UPDATE users SET username = '$name', password = '$pass' WHERE email = '$email';";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo 'change successful';
            } else {
                echo 'change unsuccessful';
            }
        }
    }
}

if (isset($_POST['adIds'])) {
    $allok = true;

    $adId = $_POST['adIds'];
    $apartment = $_POST['apartment'];
    $building = $_POST['building'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $region = $_POST['region'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $sql = "UPDATE addresses SET apartment = '$apartment', building = '$building', street = '$street', city = '$city', region = '$region' WHERE id = '$adId';";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            if (isset($_POST['uid'])) {
                $uid = $_POST['uid'];
                $nom = $_POST['number'];

                $sql2 = "UPDATE users SET pnumber = '$nom' WHERE id = '$uid';";

                $result2 = mysqli_query($conn, $sql2);
                if ($result2) {
                    echo 'change successful';
                } else {
                    echo 'change unsuccessful';
                }
            } else echo 'change successful';
        } else {
            echo 'change unsuccessful';
        }
    }
}
