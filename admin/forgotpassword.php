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
                                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; background: #f7f7f7; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>

                                        <!-- Header -->
                                        <div style='background-color: #009245; color: #fff; padding: 20px; text-align: center;'>
                                            <img src='" . $websiteaddresse . "images/logo_white.png' alt='Fion Beauty' style='max-height: 50px; margin-bottom: 10px;'>
                                            <h1 style='margin: 0; font-size: 24px;'>Fion Beauty Supplies</h1>
                                        </div>

                                        <!-- Body -->
                                        <div style='padding: 25px; color: #333;'>
                                            <h2 style='color: #009245; margin-top: 0;'>Password Reset Request</h2>
                                            <p>Hello,</p>
                                            <p>We received a request to reset your <strong>Fion Beauty Supplies admin account</strong> password.</p>
                                            <p>If you made this request, please click the button below to create a new password:</p>

                                            <p style='text-align: center; margin: 30px 0;'>
                                                <a href='$resetLink' target='_blank' style='background-color: #009245; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Reset Your Password</a>
                                            </p>

                                            <p style='color: #555;'>This link will expire in <strong>30 minutes</strong> for your security.</p>
                                            <p>If you didn’t request a password reset, please ignore this message — your account will remain secure.</p>

                                            <br>
                                            <p style='margin: 0;'>Best regards,<br><strong>Fion Beauty Supplies Security Team</strong></p>
                                        </div>

                                        <!-- Footer -->
                                        <div style='margin-top: 20px; padding: 20px; font-family: Arial, sans-serif; color: #6B6B6B;'>
                                            <hr style='margin: 20px 0; border: none; border-top: 1px solid #eee;'>
                                            <p style='font-size: 13px; color: #999; text-align: center; margin: 0;'>
                                                © " . date('Y') . " Fion Beauty Supplies. All rights reserved.<br>
                                                This is an automated message — please do not reply directly.
                                            </p>
                                        </div>

                                    </div>
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
