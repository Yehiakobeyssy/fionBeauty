<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';

    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        header("Location: ../login.php");
        exit(); 
    }

    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/changePassword.css">
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
        include 'include/catecorysname.php';
    ?>
    <main>
        <?php
            $sql = $con->prepare('SELECT clientBlock FROM  tblclient WHERE clientID  = ?');
            $sql->execute([$user_id]);
            $result_block = $sql->fetch();
            $isBlock = $result_block['clientBlock'];

            if ($isBlock == 1) {
                echo '
                    <div class="alert alert-danger">
                        <h2>OPPS! You are Blocked from Admin</h2>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = "../index.php";
                        }, 2000);
                    </script>
                ';
                exit(); // stop the rest of the page from executing
            }
        ?>
        <?php include 'include/aside.php' ?>
        <div class="sections_side">
            
            <?php
                if(isset($_POST['btnchange'])){
                    $oldPass= $_POST['txtoldPassword'];
                    $newpass= $_POST['txtNewPassword'];
                    $conform = $_POST['txtConformPassword'];

                    if($newpass == $conform ){
                        $sql=$con->prepare('SELECT clientPassword FROM   tblclient WHERE clientID   = ?');
                        $sql->execute([$user_id]);
                        $result= $sql->fetch();
                        $checkpassword= $result['clientPassword'];

                        if($checkpassword == sha1($oldPass)){
                            $stat = $con->prepare('UPDATE tblclient SET clientPassword = ? WHERE clientID  = ?');
                            $stat->execute([sha1($newpass),$user_id]);
                            echo '
                                <div class="display_msg susscess">
                                    <h5>Your Password changed sussesfully</h5>
                                </div>
                                <script>
                                    // Redirect after 1 second (1000 ms)
                                    setTimeout(function() {
                                        window.location.href = "dashboard.php";
                                    }, 1000);
                                </script>
                            ';
                        }else{
                            echo '
                            <div class="display_msg error">
                                <h5>Error ! The Currect Password is false</h5>
                            </div>
                        ';
                        }
                    }else{
                        echo '
                            <div class="display_msg error">
                                <h5>Error ! The New Password dont match</h5>
                            </div>
                        ';
                    }
                }
            ?>
            
            
            <form id="changePasswordForm" method="post">
                <div class="title">
                    <h5>Change Password</h5>
                </div>

                <input type="password" name="txtoldPassword" id="oldPassword" placeholder="Current Password">
                <input type="password" name="txtNewPassword" id="newPassword" placeholder="New Password">
                <input type="password" name="txtConformPassword" id="confirmPassword" placeholder="Confirm New Password">

                <!-- This will display the error -->
                <div id="passwordError" class="display_msg error" style="display:none; margin-top:10px; width:100%">
                    <h5>Error! The new passwords don’t match.</h5>
                </div>

                <div class="btncontrol">
                    <button type="reset" class="btn-outboder">Cancel</button>
                    <button type="submit" name="btnchange" class="btn-inboder">Change Password</button>
                </div>
            </form>
        </div>
    </main>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script>
        $(document).ready(function() {
            // When typing in either password field
            $('#newPassword, #confirmPassword').on('keyup', function() {
                let newPass = $('#newPassword').val();
                let confirmPass = $('#confirmPassword').val();

                if (confirmPass.length > 0) {
                    if (newPass !== confirmPass) {
                        $('#passwordError').show();
                    } else {
                        $('#passwordError').hide();
                    }
                } else {
                    $('#passwordError').hide();
                }
            });

            // Prevent form submission if passwords don't match
            $('#changePasswordForm').on('submit', function(e) {
                let newPass = $('#newPassword').val();
                let confirmPass = $('#confirmPassword').val();

                if (newPass !== confirmPass) {
                    e.preventDefault(); // stop form from submitting
                    $('#passwordError').show();
                }
            });
        });
    </script>
</body>