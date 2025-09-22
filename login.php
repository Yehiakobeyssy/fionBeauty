<?php
    session_start();
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        $user_id = 0; // if neither session nor cookie exist
    };

    $do= (isset($_GET['do']))?$_GET['do']:'login'
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
    ?>
    <?php
        if($do=='login'){?>
            <div class="container_login">
                <div class="img_style">
                    <img src="images/img_app/login.png" alt="" srcset="">
                </div>
                <div class="container_form">
                    <div class="title_form">
                        <h3>Sign In</h3>
                    </div>
                    
                    <form action="" method="post">
                        <label for="">Email</label>
                        <input type="email" name="loginemail" id="">
                        <label for="">Password</label>
                        <input type="password" name="loginpass" id="">
                        <div class="forgot">
                            <a href="">Forgot Password?</a>
                        </div>
                        <div class="btncontrol">
                            <button type="submit" name="login">Sign in </button>
                        </div>
                        <div class="makeanaccount">
                            <label for="">Dont't have an account? <a href="login.php?do=signup">sign up</a></label>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['login'])){
                            $email = $_POST['loginemail'];
                            $pass = sha1($_POST['loginpass']);

                            $sql = $con->prepare('SELECT clientID FROM tblclient WHERE clientEmail = ? AND clientPassword = ?');
                            $sql->execute([$email, $pass]);
                            $client = $sql->fetch(PDO::FETCH_ASSOC);

                            if ($client) {
                                $_SESSION['user_id'] = $client['clientID'];
                                header("Location: index.php");
                                exit;
                            } else {
                                echo '<div class="alert alert-danger" role="alert">
                                        UserName or Password inCorrect !
                                    </div>';
                            }
                        }
                    ?>
                </div>
            </div>
        <?php
        }elseif($do=='signup'){?>
            <div class="container_login">
                <div class="img_style">
                    <img src="images/img_app/login.png" alt="" srcset="">
                </div>
                <div class="container_form">
                    <div class="title_form">
                        <h3>Create Account</h3>
                    </div>
                    
                    <form action="" method="post" enctype="multipart/form-data">
                        <label for="">First Name</label>
                        <input type="text" name="clientFname" id="" required>
                        <label for="">Last Name</label>
                        <input type="text" name="clientLname" id="" required>
                        <label for="">Phone Number</label>
                        <input type="text" name="clientPhoneNumber" id="" required>
                        <label for="">E-mail</label>
                        <input type="email" name="clientEmail" id="" required>
                        <label for="">Profession</label>
                        <select name="profession" id="" required>
                            <option value="0">[Select One]</option>
                            <?php 
                                $sql=$con->prepare('SELECT professionID,profession FROM tblprofession WHERE professionAcctive = 1');
                                $sql->execute();
                                $result = $sql->fetchAll();
                                foreach($result as $profession){
                                    echo '<option value="'.$profession['professionID'].'">'.$profession['profession'].'</option>';
                                }
                            ?>
                        </select>
                        <label for="">Upload Certificate</label>
                        <input type="file" name="certificate" id="" required>
                        <label for="">Password</label>
                        <input type="password" name="clientPassword" id="" required>
                        <label for="">Conform Password</label>
                        <input type="password" name="conformPassword" id="" required>
                        <div class="btncontrol">
                            <button type="submit" name="btnnewAccount">Create Account</button>
                        </div>
                        <div class="makeanaccount">
                            <label for="">Have an account? <a href="login.php?do=login">sign in</a></label>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnnewAccount'])){
                            $checkemail = checkItem('clientEmail','tblclient',$_POST['clientEmail']);
                            if($checkemail == 0){
                                if($_POST['clientPassword']== $_POST['conformPassword']){
                                    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
                                        $temp = explode(".", $_FILES['certificate']['name']);
                                        $newfilename = round(microtime(true)) . '.' . end($temp);
                                        move_uploaded_file($_FILES['certificate']['tmp_name'], 'documents/' . $newfilename);
                                    }else{
                                        $newfilename='';
                                    }

                                    $clientFname = $_POST['clientFname'];
                                    $clientLname = $_POST['clientLname'];
                                    $clientPhoneNumber = $_POST['clientPhoneNumber'];
                                    $clientEmail = $_POST['clientEmail'];
                                    $certificate = $newfilename;
                                    $profession = $_POST['profession'];
                                    $clientPassword = sha1($_POST['clientPassword']);
                                    $clientActive = 0;
                                    $clientActivation = sha1(date('Y.m.d'));
                                    $clientBlock = 0;
                                    $pushId = '';
                                    $clientAbout = '';

                                    $sql=$con->prepare('INSERT INTO  tblclient (clientFname,clientLname,clientPhoneNumber,clientEmail,certificate,profession,clientPassword,clientActive,clientActivation,clientBlock,pushId,clientAbout)
                                                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ');
                                    $sql->execute(array($clientFname,$clientLname,$clientPhoneNumber,$clientEmail,$certificate,$profession,$clientPassword,$clientActive,$clientActivation,$clientBlock,$pushId,$clientAbout));
                                    
                                    echo '<script> location.href="login.php"</script>';
                                }
                            }
                        }
                    ?>
                </div>
            </div>
        <?php
        }else{
            echo '<script> location.href="index.php"</script>';
        }
    ?>
    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/login.js"></script>
</body>