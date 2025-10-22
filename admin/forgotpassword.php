<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';


    include '../common/head.php';

    $do = isset($_GET['do'])?$_GET['do']:'forgot'
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>
<body>
    <header>
        <img src="../images/logo.png" alt="">
        <h3>Fion Beauty Supplies</h3>
    </header>
    <main>
    <?php
        if($do == 'forgot'){?>
            <div class="container_frm">
                <div class="title">
                    <h3>Forget Your Password ?</h3>
                    <span>Enter your email and we'll send you a link to reset your password.</span>
                </div>
                <form action="" method="post">
                    <label for="">Email</label>
                    <input type="email" name="txtemail" id="txtemail">
                    <div id="emailError" style="color:red; font-size:14px; margin-bottom:5px;"></div>
                    <div class="btncontrol">
                        <button type="submit" name="btnsendlink">Send</button>
                    </div>
                </form>
                <?php 
                    if(isset($_POST['btnsendlink'])){
                        $emailsend = $_POST['txtemail'];
                        $checkemail = checkItem('adminEmail','tbladmin',$emailsend);

                        if($checkemail == 1 ){
                            $token = bin2hex(random_bytes(16));

                            $sql = $con->prepare('UPDATE tbladmin SET token = ? WHERE adminEmail = ?');
                            $sql->execute([$token, $emailsend]);

                            include '../mail.php';

                            $mail->setFrom($applicationemail, 'Fion Beauty Supplies'); // Sender
                            $mail->addAddress($emailsend);                    // Receiver
                            $mail->Subject = '🔒 Password Reset Request';

                            // Content
                            $mail->isHTML(true);
                            $mail->CharSet = 'UTF-8';   
                            $resetLink = $websiteaddresse.'admin/forgotpassword.php?do=newPassword&token='.$token;
                            $mail->Body = "
                                <p>Hello,</p>
                                <p>We received a request to reset your Fion Beauty Supplies admin account password.</p>
                                <p>If you made this request, please click the link below to create a new password:</p>
                                <p><a href='$resetLink' target='_blank' style='color:#007bff;'>Reset Your Password</a></p>
                                <p>This link will expire in 30 minutes for your security.</p>
                                <p>If you didn’t request a password reset, please ignore this message — your account will remain secure.</p>
                                <br>
                                <p>Best regards,<br>Fion Beauty Supplies Security Team</p>
                            ";

                            if($mail->send()){
                                echo '<div class="alert alert-success">✅ A secure password reset link has been sent to your email. Please check your inbox (and spam folder) to continue.</div>';
                            } else {
                                echo '<div class="alert alert-warning">⚠️ We could not send the reset link at the moment. Please try again later.</div>';
                            }

                        } else {
                            echo '<div class="alert alert-danger">❌ The email address you entered is not registered in our system. Please verify and try again.</div>';
                        }
                    }

                ?>
            </div>
        <?php
        }elseif($do =='newPassword'){
            $token = isset($_GET['token'])?$_GET['token']:'';
            $checkToken = $checkemail = checkItem('token','tbladmin',$token);

            if($checkToken == 0){
                echo '<div class="alert alert-danger" >The Link is wrong  </div>';
            }else{
                $sql= $con->prepare('SELECT adminID  FROM  tbladmin WHERE token = ?');
                $sql->execute([$token]);
                $result = $sql->fetch();
                $adminID = $result['adminID']?>
                <div class="container_frm">
                    <div class="title">
                        <h3>Create New Password</h3>
                        <span>Your new password must be different from previous used passwords.</span>
                    </div>
                    <form action="" method="post">
                        <label for="">Password</label>
                        <input type="password" name="txtnewpass" id="txtnewpass" required>
                        <label for="">Confirm Password</label>
                        <input type="password" name="txtconformpass" id="txtconformpass" required>
                        <div id="passworderror" style="color:red; font-size:14px; margin-bottom:5px;"></div>
                        <div class="btncontrol">
                            <button type="submit" name="btnupdatepass">Update Password</button>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnupdatepass'])){
                            $newpass = $_POST['txtnewpass'];
                            $conform = $_POST['txtconformpass'];

                            if($newpass == $conform){
                                $newpass = sha1($newpass);
                                $newtoken = bin2hex(random_bytes(16));

                                $sql=$con->prepare('UPDATE tbladmin SET adminPassword = ? , token = ? WHERE adminID  = ?');
                                $sql->execute([$newpass, $newtoken ,$adminID ]);

                                header('location: index.php');
                            }else{
                                echo '<div class="alert alert-danger">The passwords you entered do not match.</div>';
                            }
                        }
                    ?>
                </div>
            <?php
            }
        }else{
            header("Location: index.php");
            exit();  
        }
    ?>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/forgotpassword.js"></script>
</body>
