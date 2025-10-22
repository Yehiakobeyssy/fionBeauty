<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';

    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();  
    }else{
        $admin_id = $_SESSION['admin_id'];
        $sql=$con->prepare('SELECT fName, lName FROM  tbladmin WHERE adminID  = ?');
        $sql->execute([$admin_id]);
        $result =  $sql->fetch();
        $admin_name = $result['fName'].' ' . $result['lName'];
    }
    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/changePassword.css">
</head>
<body> 
    <?php include 'include/adminheader.php' ?>
    <main>
        <?php include 'include/adminaside.php'?>
        <div class="container_info">
            <?php
                if(isset($_POST['btnchange'])){
                    $oldPass= $_POST['txtoldPassword'];
                    $newpass= $_POST['txtNewPassword'];
                    $conform = $_POST['txtConformPassword'];

                    if($newpass == $conform ){
                        $sql=$con->prepare('SELECT adminPassword FROM  tbladmin WHERE adminID  = ?');
                        $sql->execute([$admin_id]);
                        $result= $sql->fetch();
                        $checkpassword= $result['adminPassword'];

                        if($checkpassword == sha1($oldPass)){
                            $stat = $con->prepare('UPDATE tbladmin SET adminPassword = ? WHERE adminID  = ?');
                            $stat->execute([sha1($newpass),$admin_id]);
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