<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';

    if (isset($_SESSION['admin_id'])) {
        header("Location: dashboard.php");
        exit();  
    }
    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <header>
        <img src="../images/logo.png" alt="">
        <h3>Fion Beauty</h3>
    </header>
    <main>
        <div class="container_frm">
            <div class="title">
                <h3>Sign In</h3>
            </div>
            <form action="" method="post">
                <label for="">E-mail</label>
                <input type="email" name="txtemail" id="txtemail">
                <div id="emailError" style="color:red; font-size:14px; margin-bottom:5px;"></div>
                <label for="">Password</label>
                <input type="password" name="txtpassword" id="txtpassword">
                <div id="passwordError" style="color:red; font-size:14px; margin-bottom:5px;"></div>
                <div class="forgotpass">
                    <a href="forgotpassword.php">Forgot Password?</a>
                </div>
                <div class="btncontrol">
                    <button type="submit" name="btnsignup">Sign In</button>
                </div>
                <?php
                    if(isset($_POST['btnsignup'])){
                        $adminemail = $_POST['txtemail'];
                        $adminpass = sha1($_POST['txtpassword']);

                        $sql= $con->prepare('SELECT adminID  FROM tbladmin WHERE adminEmail = ? AND adminPassword = ?');
                        $sql->execute([$adminemail,$adminpass]);
                        $check = $sql->rowCount();
                        if($check>= 1){
                            $result = $sql->fetch();
                            $_SESSION['admin_id'] = $result['adminID'];
                            header("Location: dashboard.php");
                            exit();
                        }else{
                            echo '<div class="alert alert-danger"> Wrong Email or password ! Try again </div>';
                        }
                        
                    }
                ?>
            </form>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/index.js"></script>
</body>
