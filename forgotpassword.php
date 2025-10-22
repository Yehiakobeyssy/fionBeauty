<?php
    session_start();
    // no processing here: HTML + minimal PHP for session detection/rendering only
    include 'settings/connect.php'; // $con available if needed for rendering (e.g. professions)
    include 'common/function.php';
    include 'common/head.php';   
    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        $user_id = 0; // if neither session nor cookie exist
    };

    $do=(isset($_GET['do']))?$_GET['do']:'forget';
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
    <link rel="stylesheet" href="css/forgotpassword.css">
    <script src="common/jslinks/jquery.min.js"></script> <!-- ensure jQuery loaded -->
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php';
    ?>
    <div class="container_forgetpass">
        <?php
            if($do=='forget'){?>
                <div class="newform">
                    <h3>Forget Your Password ? </h3>
                    <label for="">Enter your email and we'll send you a link to reset your password </label>
                    <form action="" method="post">
                        <input type="email" name="txtemail" id="" placeholder="E-mail">
                        <button type="submit" name="btnsendlink">Send</button>
                    </form>
                    <?php
                        if(isset($_POST['btnsendlink'])){
                            $email = $_POST['txtemail'];

                            $checkemail = checkItem('clientEmail','tblclient',$email);

                            if($checkemail == 1){
                                $newclientActivation = bin2hex(random_bytes(16));
                                $sql = $con->prepare('UPDATE tblclient SET clientActivation = ? WHERE clientEmail =?');
                                $sql->execute([ $newclientActivation,$email]);
                                
                                include 'mail.php';

                                $mail->setFrom($applicationemail, 'Fion Beauty Supplies'); // Sender
                                $mail->addAddress($email);              // Receiver
                                $mail->Subject = 'Reset Password';

                                //Content
                                $mail->isHTML(true);
                                $mail->CharSet = 'UTF-8';   
                                $resetLink = $websiteaddresse.'forgotpassword.php?do=newpass&token='.$newclientActivation;
                                $mail->Body = "Click the link to reset your password: <a href='$resetLink'>$resetLink</a>";

                                $mail->send();


                                echo '<div class="alert alert-success">We have sent a password reset link to your email address.</div>';
                            }else{
                                echo '<div class="alert alert-danger">The email address you entered is not registered.</div>';
                            }


                        }
                    ?>
                    
                    
                </div>
            <?php
            }elseif($do=='newpass'){
                $get_token = (isset($_GET['token']))?$_GET['token']:'';
                $checkToken = checkItem('clientActivation','tblclient',$get_token);

                if($checkToken == 0){
                    echo '<div class="alert alert-danger" >The Link is wrong  </div>';
                }else{
                    $sql=$con->prepare('SELECT clientID  FROM tblclient WHERE clientActivation = ?');
                    $sql->execute([$get_token]);
                    $result = $sql->fetch();
                    $clientID = $result['clientID'];

                    ?>
                        <div class="newform">
                            <h3>Create New Password </h3>
                            <label for="">Your new password must be different from previous used passwords. </label>
                            <form action="" method="post">
                                <input type="password" name="txtnewpassword" id="" placeholder="New Password">
                                <input type="password" name="txtconformpass" id="" placeholder="Conform Password">
                                <button type="submit" name="btnverfiy">Verfiy the Password</button>
                            </form>
                            <?php
                                if(isset($_POST['btnverfiy'])){
                                    if($_POST['txtnewpassword'] == $_POST['txtconformpass']){
                                        $newpassword = sha1($_POST['txtnewpassword']);
                                        
                                        $sql= $con->prepare('UPDATE tblclient SET clientPassword = ? WHERE clientID = ? ');
                                        $sql->execute([$newpassword,$clientID]);

                                        echo '<div class="alert alert-success">Your password has been changed successfully.</div>';
                                    }else{
                                        echo '<div class="alert alert-danger">The passwords you entered do not match.</div>';
                                    }

                                }
                            ?>
                        </div>
                    <?php
                }
            ?>
            <?php
            }else{
                echo '<script>location.href="index.php"</script>';
            }
        ?>
    </div>

    <?php 
        include 'include/footer.php' ;
    ?>
    <script src="js/forgotpassword.js"></script>
</body>
</html>
