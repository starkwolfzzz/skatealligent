<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skate Alligent - Verify</title>
    <link rel="icon" type="image/png" href="../images/black_Icon.png">
    <link rel="stylesheet" href="../css/style.css">
    <link href="../fontawesome-free-5.15.3-web/css/all.css" rel="stylesheet">
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js%27);
fbq('init', '950881942420079');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=950881942420079&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
</head>

<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mobile-detect/1.4.4/mobile-detect.min.js"></script>
    <div id="indexpage">
        <div class="header">
        <div class="container">
            <div class="navbar4" id="headerr">
                <div class="logo-noline" id="logo">
                </div>
                <nav>
                    <ul id="menuItems">
                    </ul>
                </nav>
            </div>
            <section id="fade">
                <img src="../images/Background.jpg" id="background">
                <div class="container" id="container">
                    <div class="row">
                        <div class="form-container">
                            <div class="form-btn">
                                <span id="formsVerifyTitle" style="border-bottom: 3px solid white;">VERIFY</span>
                            </div>
                            <form id="LoginForm" onsubmit="return save_data_log()" class="loginform">
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
                                $password;

                                $sql = "SELECT * FROM users where email = '$email'";
                                $result = mysqli_query($conn, $sql);
                                $countdata = mysqli_num_rows($result);

                                if ($row = mysqli_fetch_assoc($result)) {
                                    $password = $row['password'];
                                }

                                $sql2 = "SELECT * FROM users where verifytoken = '$token'";
                                $result2 = mysqli_query($conn, $sql2);
                                $countdata2 = mysqli_num_rows($result2);

                                if ($countdata == 0) {
                                    echo '<div class="ErrorL" style="color: red; border: 1px solid red; opacity: 100%; vertical-align: middle; position: relative; height:30px"><a>⚠ Invalid Email.</a></div>';
                                } else if ($countdata2 == 0) {
                                    echo '<div class="ErrorL" style="color: red; border: 1px solid red; opacity: 100%; vertical-align: middle; position: relative; height:30px"><a>⚠ Invalid token.</a></div>';
                                } else {
                                    $sql3 = "SELECT * FROM users where email = '$email' AND verifytoken = '$token' AND verified = 1";
                                    $result3 = mysqli_query($conn, $sql3);
                                    $countdata3 = mysqli_num_rows($result3);

                                    if ($password != "") {
                                        if ($countdata3 == 0) {
                                            $verifyEmailQ = mysqli_query($conn, "UPDATE users SET verified = 1 WHERE email = '$email' and verifytoken = '$token'");

                                            if ($verifyEmailQ) {
                                                echo '<div class="ErrorL" style="color: white; border: 1px solid white; opacity: 100%; vertical-align: middle; position: relative; height:30px"><a>Your account is now verified.</a></div>';
                                            }
                                        } else {
                                            echo '<div class="ErrorL" style="color: white; border: 1px solid white; opacity: 100%; vertical-align: middle; position: relative; height:30px"><a>Your account was already verified.</a></div>';
                                        }
                                    } else {
                                        echo "<a style='display: none;' id='status'>Account needs setup</a>";
                                    }
                                }
                                ?>
                                <a href="../index.html" style="color: black; padding: 10px; background-color:white; border-radius: 50px; font-size: 20px;">Go to Skate Alligent</a>
                            </form>
                            <form id="RegForm" onsubmit="return changeAccountInfo()" class="loginform">
                                <div class="ErrorP"><a>⚠ Entered username is already taken, please pick another one.</a></div>
                                <div class="ErrorP"><a>⚠ Entered Passwords are not Matching, please try again.</a></div>
                                <div class="ErrorP"><a>⚠ An unknown error has occurred, please try again later.</a></div>
                                <input class="accountInput pass" type="text" placeholder="Username" required id="username" name="newAccUsername">
                                <input class="accountInput passwordField pass" type="password" placeholder="Password" required minlength="8" id="newPassword" name="newPassword" onfocus="focused2()" onkeyup="keyUp2(this)">
                                <div class="passStrength">
                                    <span>Weak</span>
                                    <span></span>
                                </div>
                                <input class="accountInput passwordField pass" type="password" placeholder="Repeat Password" required minlength="8" id="repeatNewPassword" name="repeatNewPassword">
                                <div class="toggleEyeVerify" onclick="clicked('res')">
                                    <i class="fa fa-eye"></i>
                                    <i class="fa fa-eye-slash"></i>
                                </div>
                                <div class="toggleEyeVerify2" onclick="clicked('res2')">
                                    <i class="fa fa-eye"></i>
                                    <i class="fa fa-eye-slash"></i>
                                </div>
                                <button type="submit" class="btn regSubmit">Confirm</button>
                            </form>
                        </div>
                        <div class="form-overlay">
                            <div class="overlay-bg"></div>
                            <img src="../images/loading.png" class="loading">
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!--- Cookie Notice --->
        <div class="cookies" style="visibility: hidden">
            <div class="container">
                <div class="row">
                    <div class="col-2" style="text-align: right; color: white; font-size: 1vw;">
                        <p>We use cookies to enhance your experience with our website.</p>
                        <p>By continuing to use the site, you agree to the use of cookies.</p>
                    </div>
                    <div class="col-2" style="text-align: left; font-size: 1vw;">
                        <a class="cookie-btn" id="link" onclick="acceptCookies()" style="color: white;">Accept Cookies</a>
                        <a class="cookie-btn" id="link" href="/cookie-policy.html" style="color: white;">Learn More</a>
                    </div>
                </div>
            </div>
        </div>

    <!--- notif --->
    <div class="notif" id="hide" style="color: white; background-color: #160a22;">
        <i class="fas fa-exclamation"></i>
        <a id="notifText" class="notifText" style="font-size: 15px;">An email was sent to you.</a>
    </div>
    </div>

    <!--- JavaScript --->
    <script>

        // <-- Menu -->
        var MenuItems = document.getElementById("menuItems");

        MenuItems.style.maxHeight = "0px";

        function menuToggle() {
            if (MenuItems.style.maxHeight == "0px") {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }

        // <-- Products Href -->
        function prodPg() {
            location.href = "/product.html?id=" + "id";
        }

        // <-- Switch Page -->
        function switchPg(pg) {
            location.href = pg;
        }

        // <-- Switch between login and reg and pass-->
        var LoginForm = document.getElementById("LoginForm");
        var RegForm = document.getElementById("RegForm");

        function switchBetween(type) {
            if (type == "Register") {
                RegForm.style.transform = "translateY(-30px) translateX(0px)";
                LoginForm.style.transform = "translateY(0px) translateX(0px)";
            } else if (type == "Login") {
                RegForm.style.transform = "translateY(-30px) translateX(300px)";
                LoginForm.style.transform = "translateY(0px) translateX(300px)";
            }
        }

        if(document.getElementById("status") != null){
            document.getElementById("formsVerifyTitle").innerHTML = "SETUP";
            switchBetween("Register");
            document.getElementById("LoginForm").remove();
        } else {
            document.getElementById("formsVerifyTitle").innerHTML = "VERIFY";
            switchBetween("Login");
            document.getElementById("RegForm").remove();
        }

        // <-- Cookie Consent -->
        let cSection = document.getElementsByClassName("cookies");
        let cCont = cSection[0].children[0];
        checkCookies();

        function checkCookies() {
            if (getCookie("CookieConsent") == null) {
                cFunc("show");
            } else {
                cFunc("hide");
            }
        }

        function acceptCookies() {
            cFunc("hide");
            setCookie("CookieConsent", "true");
        }

        function cFunc(n) {
            if (n == "show") {
                document.getElementsByClassName("cookies")[0].style.visibility = "visible";
                cCont.style.top = parseInt(parseInt(window.innerHeight) - 80) + "px";
            } else if (n == "hide") {
                cCont.style.top = window.innerHeight + "px";
                document.getElementsByClassName("cookies")[0].style.visibility = "hidden";
            }
        }

        function setCookie(key, value, time = 10000 * 365 * 24 * 60 * 60) {
            var expires = new Date();
            expires.setTime(expires.getTime() + time);
            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
        }

        function getCookie(key) {
            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
            return keyValue ? keyValue[2] : null;
        }

        // <-- Get Password Strength -->            
        function checkStrength(password) {
            let s = 0;
            if (password.length > 6) {
                s++;
            }

            if (password.length > 10) {
                s++;
            }

            if (/[A-Z]/.test(password)) {
                s++;
            }

            if (/[0-9]/.test(password)) {
                s++;
            }

            if (/[^A-Za-z0-9]/.test(password)) {
                s++;
            }

            if (password.length == 0) {
                s = -1;
            }

            return s;
        }

        function focused2() {
            document.querySelector(".passStrength").style.visibility = "visible";
            document.querySelector(".passStrength").style.transform = "translateY(0px)";
            document.querySelector(".passStrength").style.opacity = "100%";
        }

        function clicked(type) {
            if (type.includes("res") && !type.includes("res2")) {
                let pnt = document.querySelector(".toggleEyeVerify");

                if (type.includes("reset")) {
                    document.querySelector("#newPassword").setAttribute("type", "password");
                    pnt.classList.remove("active");
                } else {
                    if (pnt.classList.contains("active")) {
                        document.querySelector("#newPassword").setAttribute("type", "password");
                        pnt.classList.remove("active");
                    } else {
                        document.querySelector("#newPassword").setAttribute("type", "text");
                        pnt.classList.add("active");
                    }
                }
            } else if (type.includes("res2")) {
                let pnt = document.querySelector(".toggleEyeVerify2");

                if (type.includes("reset")) {
                    document.querySelector("#repeatNewPassword").setAttribute("type", "password");
                    pnt.classList.remove("active");
                } else {
                    if (pnt.classList.contains("active")) {
                        document.querySelector("#repeatNewPassword").setAttribute("type", "password");
                        pnt.classList.remove("active");
                    } else {
                        document.querySelector("#repeatNewPassword").setAttribute("type", "text");
                        pnt.classList.add("active");
                    }
                }
            }

        }

        function keyUp2(x) {
            var password;

            if (x == "reset") {
                password = "";
            } else {
                password = x.value;
            }

            let strength = checkStrength(password);
            let passwordStrengths = document.querySelectorAll(".passStrength span");
            if (strength > -1) {
                strength = Math.max(strength, 1);
                passwordStrengths[1].style.width = strength * 20 + "%";
            } else {
                passwordStrengths[1].style.width = "0%";
            }

            if (strength == -1) {
                passwordStrengths[0].innerText = "Weak";
                passwordStrengths[1].style.color = "#111";
                passwordStrengths[1].style.background = "#fff";
            } else if (strength <= 2) {
                passwordStrengths[0].innerText = "Weak";
                passwordStrengths[1].style.color = "#111";
                passwordStrengths[1].style.background = "#d13636";
            } else if (strength > 2 && strength <= 4) {
                passwordStrengths[0].innerText = "Medium";
                passwordStrengths[1].style.color = "#111";
                passwordStrengths[1].style.background = "#e6da44";
            } else if (strength > 4) {
                passwordStrengths[0].innerText = "Strong";
                passwordStrengths[1].style.color = "#fff";
                passwordStrengths[1].style.background = "#20a820";
            }

            if (x == "reset") {
                document.querySelector(".passStrength").style.visibility = "hidden";
                document.querySelector(".passStrength").style.transform = "translateY(-10px)";
                document.querySelector(".passStrength").style.opacity = "0%";
            }

            let regPasswordd = document.querySelector("#newPassword");
            let form = document.querySelector("#PasswordForm");
            if (checkStrength(password) <= 2) {
                regPasswordd.setCustomValidity("Please choose a stronger password.");
            } else {
                regPasswordd.setCustomValidity("");
            }
        }

        function notification(type) {
            let notif = document.getElementsByClassName("notif")[0];
            let textNo = notif.children[1];

            if (type == "reg") {
                textNo.style.fontSize = "15px";
                textNo.textContent = "An email was sent to you.";
                notif.id = "show";
                setTimeout(function() {
                    notif.id = "hide";
                }, 5000);
            } else if (type == "resPass") {
                textNo.style.fontSize = "15px";
                textNo.textContent = "Your password has been reset.";
                notif.id = "show";
                setTimeout(function() {
                    notif.id = "hide";
                }, 5000);
            } else if (type == "res") {
                textNo.style.fontSize = "15px";
                textNo.textContent = "An email was sent to you.";
                notif.id = "show";
                setTimeout(function() {
                    notif.id = "hide";
                }, 5000);
            }
        }

        function loading(con) {
            if (con == true) {
                document.getElementsByClassName("form-overlay")[0].classList.add("ovlyact");
            } else {
                document.getElementsByClassName("form-overlay")[0].classList.remove("ovlyact");
            }
        }

        // <-- Account Info -->
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const email = urlParams.get('email');

        function changeAccountInfo() {
            loading(true);
            var pass1 = document.getElementById("newPassword").value;
            var pass2 = document.getElementById("repeatNewPassword").value;

            if (pass1 == pass2) {
                var form_elms = document.getElementsByClassName("pass");

                var form_data = new FormData();

                for (i = 0; i < form_elms.length; i++) {
                    form_data.append(form_elms[i].name, form_elms[i].value);
                }

                form_data.append("email", email);

                document.getElementsByClassName('regSubmit')[0].disabled = true;
                document.getElementsByClassName('regSubmit')[0].style.opacity = "75%";

                var ajax_request = new XMLHttpRequest();

                ajax_request.open('POST', 'process_data.php');

                ajax_request.send(form_data);

                ajax_request.onreadystatechange = function() {
                    if (ajax_request.readyState == 4 && ajax_request.status == 200) {

                        var response = ajax_request.responseText;

                        if (response == "username is in use") {
                            RegError(0);
                        } else if (response == "change successful") {
                            location.reload();
                        } else if (response == "change unsuccessful") {
                            RegError(2);
                        }
                        loading(false);
                    }
                }
            } else {
                RegError(1);
                loading(false);
            }

            return false;
        }

        function RegError(code) {
            let regText = document.getElementsByClassName("ErrorP");

            if (code == 0) {
                regText[0].style.opacity = "100%";
                regText[1].style.opacity = "0%";
                regText[2].style.opacity = "0%";
            } else if (code == 1) {
                regText[0].style.opacity = "0%";
                regText[1].style.opacity = "100%";
                regText[2].style.opacity = "0%";
            } else if (code == 2) {
                regText[0].style.opacity = "0%";
                regText[1].style.opacity = "0%";
                regText[2].style.opacity = "100%";
            } else if (code == "reset") {
                regText[0].style.opacity = "0%";
                regText[1].style.opacity = "0%";
                regText[2].style.opacity = "0%";
            }
        }
    </script>
</body>

</html>