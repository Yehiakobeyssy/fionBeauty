<?php
session_start();
// no processing here: HTML + minimal PHP for session detection/rendering only
include 'settings/connect.php'; // $con available if needed for rendering (e.g. professions)
include 'common/function.php';
    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        $user_id = 0; // if neither session nor cookie exist
    };
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="common/jslinks/jquery.min.js"></script> <!-- ensure jQuery loaded -->
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php';
    ?>

    <div class="container_login">
        <div class="img_style">
            <img src="images/img_app/login.png" alt="app image">
        </div>

        <div class="container_form" id="auth-area">
            <!-- error box (hidden by default) -->
            <div id="msg-box" class="alert-box hidden">
                <span id="msg-text"></span>
                <button type="button" class="close-btn" id="msg-close">&times;</button>
            </div>

            <!-- TAB HEADERS -->
            <div class="tab-controls">
                <button class="tab-btn active" data-target="#login-form">Sign In</button>
                <button class="tab-btn" data-target="#signup-form">Create Account</button>
            </div>

            <!-- LOGIN FORM -->
            <form id="login-form" class="auth-form" autocomplete="off">
                <div class="title_form"><h3>Sign In</h3></div>

                <label for="loginemail">Email</label>
                <input type="email" name="loginemail" id="loginemail" required>

                <label for="loginpass">Password</label>
                <input type="password" name="loginpass" id="loginpass" required>

                <div class="forgot">
                    <a href="forgotpassword.php">Forgot Password?</a>
                </div>

                <div class="btncontrol">
                    <button type="submit" id="login-submit">Sign in</button>
                </div>

                <div class="makeanaccount">
                    <label>Don't have an account? <a href="#" class="switch-tab" data-target="#signup-form">sign up</a></label>
                </div>
            </form>

            <!-- SIGNUP FORM -->
            <form id="signup-form" class="auth-form hidden" enctype="multipart/form-data" autocomplete="off">
                <div class="title_form"><h3>Create Account</h3></div>

                <label for="clientFname">First Name</label>
                <input type="text" name="clientFname" id="clientFname" required>

                <label for="clientLname">Last Name</label>
                <input type="text" name="clientLname" id="clientLname" required>

                <label for="clientPhoneNumber">Phone Number</label>
                <input type="text" name="clientPhoneNumber" id="clientPhoneNumber" required>

                <label for="clientEmail">E-mail</label>
                <input type="email" name="clientEmail" id="clientEmail" required>
                <div id="email-check" class="small-note"></div>

                <label for="profession">Profession</label>
                <select name="profession" id="profession" required>
                    <option value="0">[Select One]</option>
                    <?php
                    // Render professions from DB
                    $stmt = $con->prepare('SELECT professionID,profession FROM tblprofession WHERE professionAcctive = 1');
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($rows as $r){
                        echo '<option value="'.htmlspecialchars($r['professionID']).'">'.htmlspecialchars($r['profession']).'</option>';
                    }
                    ?>
                </select>

                <label for="certificate">Upload Certificate</label>
                <input type="file" name="certificate" id="certificate" required>

                <label for="clientPassword">Password</label>
                <input type="password" name="clientPassword" id="clientPassword" required>

                <label for="conformPassword">Confirm Password</label>
                <input type="password" name="conformPassword" id="conformPassword" required>
                <div id="pw-note" class="small-note"></div>

                <div class="btncontrol">
                    <button type="submit" id="signup-submit">Create Account</button>
                </div>

                <div class="makeanaccount">
                    <label>Have an account? <a href="#" class="switch-tab" data-target="#login-form">sign in</a></label>
                </div>
            </form>
        </div>
    </div>

    <?php include 'include/footer.php' ?>
    <script src="js/login.js"></script>
</body>
</html>
