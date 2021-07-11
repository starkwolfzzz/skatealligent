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
            $header .= 'From: mail@skatealligent.tk' . "\r\n";
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
                        $session = md5($arrayOfIds[$i] . md5(generateRandomString(rand(5, 500))));
                        $sql = "UPDATE users SET session = '$session' WHERE id = '$arrayOfIds[$i]';";
                        $result = mysqli_query($conn, $sql);

                        if ($result) {
                            echo $session;
                            echo ', login successful';
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
                $header .= 'From: mail@skatealligent.tk' . "\r\n";
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

if (isset($_POST['adId'])) {
    $allok = true;

    $adId = $_POST['adId'];
    $apartment = $_POST['apartment'];
    $building = $_POST['building'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $region = $_POST['region'];
    $country = $_POST['country'];
    $postal = $_POST['postal'];

    if ($allok) {
        if (!$conn) {
            echo "failed to connect to database";
        }

        $sql = "UPDATE addresses SET apartment = '$apartment', building = '$building', street = '$street', city = '$city', region = '$region', country = '$country', postal = '$postal' WHERE id = '$adId';";

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
            <p><span style="font-family: Arial;"><span style="font-size: 16px;">Hi ' . $name . ',<br> <br> Sorry to hear you are planning to delete your account, if you have any complaints you can email as at <a href="mailto:mail@skatealligent.tk">mail@skatealligent.tk</a> instead.<br> To delete your account, <a href="http://skatealligent.tk/php/delete.php?id=' . $id . '&token=' . $token . '" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/php/delete.php?email=' . $email . '&token=' . $token . '">please click here.<br><br></a> Please also take some time to review our <a href="http://skatealligent.tk/privacy-policy.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/privacy-policy.html">privacy policy</a> and <a href="http://skatealligent.tk/terms-of-use.html" target="_blank" rel="noopener noreferrer" data-saferedirecturl="https://www.google.com/url?q=http://skatealligent.tk/terms-of-use.html">terms of service</a>. </span></span></p>
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
            $header .= 'From: mail@skatealligent.tk' . "\r\n";
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

if (isset($_POST['orderUserId'])) {
    $allok = true;

    $id = $_POST['orderUserId'];
    $shipping = $_POST['orderShipping'];
    $comments = $_POST['orderComments'];

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
                    if($stock < 0){
                        $understock = $quantity[$i] + $stock;
                        $stock = $productsStocks[$i] - $understock;
                    }
    
                    $sql3 = "UPDATE Products SET Stock = '$stock' WHERE ID = " . $products[$i] . ";";
    
                    $result3 = mysqli_query($conn, $sql3);
                    array_push($result3s, strval($result3));
                }

                $result3s = implode(", ", $result3s);

                if(strpos($result3s, "false") == null){
                    $sql4 = "UPDATE users SET cart = '', cartCount = '' WHERE ID = " . $id . ";";
    
                    $result4 = mysqli_query($conn, $sql4);
                    echo "Placed Order";
                } else echo "couldn't Place Order";
            } else {
                echo "couldn't Place Order";
            }
        }
    }
}
