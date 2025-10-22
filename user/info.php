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

    $do= (isset($_GET['do']))?$_GET['do']:'manage';


    if (isset($_POST['btnupdateinfo'])) {
        $fname       = $_POST['clientFname'];
        $lname       = $_POST['clientLname'];
        $phone       = $_POST['clientPhoneNumber'];
        $profession  = $_POST['profession'];

                                    // Fetch old certificate from DB first
        $stmt = $con->prepare("SELECT certificate FROM tblclient WHERE clientID = ?");
        $stmt->execute([$user_id]);
        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldFile = $oldData['certificate'];

                                    // Handle file upload
        $newFileName = $oldFile; // Default keep old file
        if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === 0) {
            $ext = pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION);
            $newFileName = round(microtime(true)) . '.' . $ext;
            $uploadPath = '../documents/' . $newFileName;

                                        // Delete old file if exists
            if (!empty($oldFile) && file_exists("../documents/" . $oldFile)) {
                unlink("../documents/" . $oldFile);
            }

            // Move new file
            if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $uploadPath)) {
                die("Error uploading file.");
            }
        }

                                    // Update DB
        $sql = "UPDATE tblclient 
                SET clientFname = ?, clientLname = ?, clientPhoneNumber = ?, profession = ?, certificate = ?
                WHERE clientID = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$fname, $lname, $phone, $profession, $newFileName, $user_id]);
    }
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/info.css">
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
        include 'include/catecorysname.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ user's Account/  <strong>Oders</strong></h5>
        </div> 
        <div class="catecoryname">
            <h2>order History</h2>
        </div>      
        <div class="desgin">

        </div>
    </div>
        <?php
    $sql = $con->prepare('SELECT clientActive FROM tblclient WHERE clientID = ?');
    $sql->execute([$user_id]);
    $check = $sql->fetch(PDO::FETCH_ASSOC);

    if ($check['clientActive'] == 0) {
        echo '
            <div style="
                width: 80%;
                margin: 20px auto;
                padding: 15px;
                background-color: #fff3cd; /* Bootstrap warning yellow */
                border: 1px solid #ffeeba; /* Slight border like bootstrap */
                color: #856404; /* Text color for warning */
                font-family: Arial, sans-serif;
                text-align: center;
                border-radius: 5px;
            ">
                Your account is not yet active. Please contact the admin to activate your account.
            </div>
        ';
    }
    ?>
    <div class="welcome_note">
        <?php
            $sql= $con->prepare('SELECT clientFname , clientLname FROM tblclient WHERE clientID  = ?');
            $sql->execute([$user_id]);
            $result = $sql->fetch();
            $fullname = $result['clientFname'].' '. $result['clientLname'];
            $initials = strtoupper(substr($result['clientFname'], 0, 1) . substr($result['clientLname'], 0, 1));

            $firstLetter = strtoupper(substr($result['clientFname'], 0, 1));

            // letter → color map
            $colors = [
                'A' => '#4285F4', // Blue
                'B' => '#34A853', // Green
                'C' => '#FBBC05', // Yellow
                'D' => '#EA4335', // Red
                'E' => '#9C27B0',
                'F' => '#FF5722',
                'G' => '#009688',
                'H' => '#795548',
                'I' => '#3F51B5',
                'J' => '#CDDC39',
                'K' => '#607D8B',
                'L' => '#E91E63',
                'M' => '#00BCD4',
                'N' => '#8BC34A',
                'O' => '#FFC107',
                'P' => '#673AB7',
                'Q' => '#FF9800',
                'R' => '#F44336',
                'S' => '#4CAF50',
                'T' => '#03A9F4',
                'U' => '#9E9E9E',
                'V' => '#FFEB3B',
                'W' => '#8E24AA',
                'X' => '#1E88E5',
                'Y' => '#D32F2F',
                'Z' => '#2E7D32',
            ];
            $bgColor = $colors[$firstLetter] ?? '#333';
            echo "<h2> WELCOME , <span>". $fullname ." </span></h2>"
        ?>
    </div>
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
                if($do == 'manage'){?>
                    <div class="personal_info">
                        <div class="title">
                            <h4>Account Settings</h4>
                            <a href="changePassword.php">Change Password</a>
                        </div>
                        <?php
                            $sql= $con->prepare('SELECT clientFname,clientLname,clientPhoneNumber,clientEmail,profession,certificate 
                                                FROM tblclient 
                                                WHERE clientID = ?');
                            $sql->execute([$user_id]);
                            $result_info= $sql->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="">Name</label>
                            <input type="text" name="clientFname" id="" value="<?=$result_info['clientFname']?>">
                            <label for="">Last Name</label>
                            <input type="text" name="clientLname" id="" value="<?= $result_info['clientLname']?>">
                            <label for="">Phone Number</label>
                            <input type="text" name="clientPhoneNumber" id="" value="<?= $result_info['clientPhoneNumber']?>">
                            <label for="">E-mail</label>
                            <input type="email" name="" id="" disabled value="<?= $result_info['clientEmail']?>">
                            <label for="">Proffession</label>
                            <select name="profession" id="">
                                <option value="0">SELECT ONE</option>
                                <?php 
                                    $sql = $con->prepare('SELECT professionID ,profession FROM tblprofession WHERE professionAcctive = 1');
                                    $sql->execute();
                                    $professions = $sql->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($professions as $pro){
                                        if($result_info['profession'] == $pro['professionID']){
                                            echo '<option value="'.$pro['professionID'].'" selected>'.$pro['profession'].'</option>';
                                        }else{
                                            echo '<option value="'.$pro['professionID'].'">'.$pro['profession'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                            <label for="">Upload Certificate</label>
                            <input type="file" name="certificate" id="">
                            <div class="btncontrol">
                                <button type="submit" name="btnupdateinfo" class="btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <div class="addresses">
                        <div class="title">
                            <h4>Addresses</h4>
                            <a href="info.php?do=addAdd">Add New</a>
                        </div>
                        <div class="container_add">
                            <?php
                                $sql = $con->prepare('SELECT addresseID ,NameAdd,emailAdd,phoneNumber,street, bultingNo, doorNo, poatalCode, cityName, provinceName,mainAdd 
                                                FROM tbladdresse 
                                                INNER JOIN tblcity ON tblcity.cityID = tbladdresse.cityID
                                                INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
                                                WHERE  userID = ? AND addActive = 1
                                                ORDER BY mainAdd DESC');
                                $sql->execute([$user_id]);
                                $row = $sql->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($row as $add){
                                    echo '
                                        <div class="addresse">
                                            <h5>'.$add['NameAdd'].'</h5>
                                            <label>'.$add['street'].' '.$add['bultingNo'].' '.$add['doorNo'].'</label><br>
                                            <label>'.$add['cityName'].' - '.$add['provinceName'].'</label><br>
                                            <label>'.$add['poatalCode'].'</label><br><br>
                                            <span>'.$add['phoneNumber'].'</span><br>
                                            <span>'.$add['emailAdd'].'</span>
                                            <div class="controladd">
                                                <a href="info.php?do=deleteAdd&idadd='.$add['addresseID'].'">Remove</a>
                                                <a href="info.php?do=edidAdd&idadd='.$add['addresseID'].'">Edit</a>';
                                                if($add['mainAdd'] == 0){
                                                    echo'<a href="info.php?do=makemain&idadd='.$add['addresseID'].'">Select as Default</a>';
                                                };
                                            echo '
                                            </div>
                                        </div>
                                    ';
                                }
                            ?>
                        </div>
                    </div>
                <?php
                }elseif($do=='addAdd'){?>
                    <div class="title">
                        <h4>Add Address</h4>
                    </div>
                    <form action="" method="post" class="frmadd">
                        <div class="long">
                            <label for="">Name :</label>
                            <input type="text" name="NameAdd" id="" required>
                        </div>
                        <div class="double">
                            <div class="insite">
                                <label>Province</label>
                                <select name="provinceID" id="provinceSelect">
                                    <option value="0">SELECT ONE</option>
                                    <?php
                                        $sql = $con->prepare('SELECT provinceID , provinceName FROM tblprovince WHERE provinceActive = 1');
                                        $sql->execute();
                                        $provinces = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($provinces as $pro) {
                                            echo '<option value="'.$pro['provinceID'].'">'.$pro['provinceName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="insite">
                                <label>City</label>
                                <select name="cityID" id="citySelect" required>
                                    <option value="">SELECT ONE</option>
                                </select>
                            </div>
                            <div class="insite">
                                <label for="">Postal Code</label>
                                <input type="text" name="poatalCode" id="" required>
                            </div>
                        </div>
                        <div class="long">
                            <label for="">Street</label>
                            <input type="text" name="street" id="" required>
                        </div>
                        <div class="double">
                            <div class="insite">
                                <label for="">Bulting No </label>
                                <input type="text" name="bultingNo" id="" required>
                            </div>
                            <div class="insite">
                                <label for="">Door No </label>
                                <input type="text" name="doorNo" id="" required>
                            </div>
                        </div>
                        <div class="long">
                            <label for="">Phone Number</label>
                            <input type="text" name="phoneNumber" id="" >
                        </div>
                        <div class="long">
                            <label for="">E-mail</label>
                            <input type="email" name="emailAdd" id="">
                        </div>
                        <div class="long">
                            <label for="">Delivery Instruction</label>
                            <textarea name="noteAdd" id=""></textarea>
                        </div>
                        <div class="set">
                            <input type="checkbox" name="mainAdd" id="">
                            <label for="">Set as default shipping address.</label>
                        </div>
                        <div class="btncontrol">
                            <button type="submit" class="btn-primary" name="btnaddAdd">Save</button>
                            <button type="reset" class="btn-cancel">Cancel</button>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnaddAdd'])){
                            $userID         = $user_id;
                            $NameAdd        = $_POST['NameAdd'];
                            $phoneNumber    = $_POST['phoneNumber'];
                            $emailAdd       = $_POST['emailAdd'];
                            $provinceID     = $_POST['provinceID'];
                            $cityID         = $_POST['cityID'];
                            $street         = $_POST['street'];
                            $poatalCode     = $_POST['poatalCode'];
                            $bultingNo      = $_POST['bultingNo'];
                            $doorNo         = $_POST['doorNo'];
                            $noteAdd        = $_POST['noteAdd'];
                            $mainAdd        = isset($_POST['mainAdd'])?1:0;
                            $addActive      = 1;

                            if($mainAdd == 1){
                                $sql=$con->prepare('UPDATE tbladdresse SET mainAdd = 0 WHERE userID = ?');
                                $sql->execute([$userID]);
                            }

                            $stat = $con->prepare('INSERT INTO tbladdresse (userID,NameAdd,phoneNumber,emailAdd,provinceID,cityID,street,poatalCode,bultingNo,doorNo,noteAdd,mainAdd,addActive) 
                                                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
                            $stat->execute([$userID,$NameAdd,$phoneNumber,$emailAdd,$provinceID,$cityID,$street,$poatalCode,$bultingNo,$doorNo,$noteAdd,$mainAdd,$addActive]);

                            echo '<div class="alert alert-success"> The address save successfuly</div>';
                        }
                    ?>
                    <script src="js/info.js"></script>
                <?php
                }elseif($do=='edidAdd'){?>
                    <?php
                        // إذا ما في idadd → رجوع لـ info.php
                        if (!isset($_GET['idadd'])) {
                            echo '<script>location.href="info.php"</script>';
                            exit;
                        }

                        // جلب البيانات
                        $idadd = $_GET['idadd'];
                        $sql = $con->prepare('SELECT * FROM tbladdresse WHERE addresseID = ? AND userID = ?');
                        $sql->execute([$idadd, $user_id]);
                        $result_add = $sql->fetch(PDO::FETCH_ASSOC);

                        // إذا idadd مش موجود بقاعدة البيانات → رجوع لـ info.php
                        if (!$result_add) {
                            echo '<script>location.href="info.php"</script>';
                            exit;
                        }
                    ?>

                    <div class="title">
                        <h4><?=(isset($result_add))?'Edit Address':'Add Address'?></h4>
                    </div>

                    <form action="" method="post" class="frmadd">
                        <div class="long">
                            <label for="">Name :</label>
                            <input type="text" name="NameAdd" value="<?=isset($result_add)?$result_add['NameAdd']:''?>" required>
                        </div>
                        <div class="double">
                            <div class="insite">
                                <label for="">Province</label>
                                <select name="provinceID" id="provinceSelect" required>
                                    <option value="0">SELECT ONE</option>
                                    <?php
                                        $sql = $con->prepare('SELECT provinceID , provinceName FROM tblprovince WHERE provinceActive = 1');
                                        $sql->execute();
                                        $provinces = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($provinces as $pro) {
                                            $sel = (isset($result_add) && $result_add['provinceID'] == $pro['provinceID']) ? "selected" : "";
                                            echo '<option value="'.$pro['provinceID'].'" '.$sel.'>'.$pro['provinceName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="insite">
                                <label for="">City</label>
                                <select name="cityID" id="citySelect" required>
                                    <option value="">SELECT ONE</option>
                                    <?php
                                        // If it's edit mode, load cities from the selected province only
                                        if (isset($result_add)) {
                                            $sql = $con->prepare('SELECT cityID, cityName FROM tblcity WHERE cityactive = 1 AND provinceID = ?');
                                            $sql->execute([$result_add['provinceID']]);
                                        } else {
                                            $sql = $con->prepare('SELECT cityID, cityName FROM tblcity WHERE cityactive = 1 LIMIT 0');
                                            $sql->execute();
                                        }
                                        $cities = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($cities as $city) {
                                            $sel = (isset($result_add) && $result_add['cityID'] == $city['cityID']) ? "selected" : "";
                                            echo '<option value="'.$city['cityID'].'" '.$sel.'>'.$city['cityName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="insite">
                                <label for="">Postal Code</label>
                                <input type="text" name="poatalCode" value="<?=isset($result_add)?$result_add['poatalCode']:''?>" required>
                            </div>
                        </div>
                        <div class="long">
                            <label for="">Street</label>
                            <input type="text" name="street" value="<?=isset($result_add)?$result_add['street']:''?>" required>
                        </div>
                        <div class="double">
                            <div class="insite">
                                <label for="">Bulting No </label>
                                <input type="text" name="bultingNo" value="<?=isset($result_add)?$result_add['bultingNo']:''?>" required>
                            </div>
                            <div class="insite">
                                <label for="">Door No </label>
                                <input type="text" name="doorNo" value="<?=isset($result_add)?$result_add['doorNo']:''?>" required>
                            </div>
                        </div>
                        <div class="long">
                            <label for="">Phone Number</label>
                            <input type="text" name="phoneNumber" value="<?=isset($result_add)?$result_add['phoneNumber']:''?>">
                        </div>
                        <div class="long">
                            <label for="">E-mail</label>
                            <input type="email" name="emailAdd" value="<?=isset($result_add)?$result_add['emailAdd']:''?>">
                        </div>
                        <div class="long">
                            <label for="">Delivery Instruction</label>
                            <textarea name="noteAdd"><?=isset($result_add)?$result_add['noteAdd']:''?></textarea>
                        </div>
                        <div class="set">
                            <input type="checkbox" name="mainAdd" <?=isset($result_add)&&$result_add['mainAdd']==1?'checked':''?>>
                            <label for="">Set as default shipping address.</label>
                        </div>
                        <div class="btncontrol">
                            <button type="submit" class="btn-primary" name="btnupdateAdd">Update</button>
                            <button type="reset" class="btn-cancel">Cancel</button>
                        </div>
                    </form>

                    <?php
                        if(isset($_POST['btnupdateAdd'])){
                            $userID         = $user_id;
                            $NameAdd        = $_POST['NameAdd'];
                            $phoneNumber    = $_POST['phoneNumber'];
                            $emailAdd       = $_POST['emailAdd'];
                            $provinceID     = $_POST['provinceID'];
                            $cityID         = $_POST['cityID'];
                            $street         = $_POST['street'];
                            $poatalCode     = $_POST['poatalCode'];
                            $bultingNo      = $_POST['bultingNo'];
                            $doorNo         = $_POST['doorNo'];
                            $noteAdd        = $_POST['noteAdd'];
                            $mainAdd        = isset($_POST['mainAdd'])?1:0;
                            $addActive      = 1;

                            if($mainAdd == 1){
                                $sql=$con->prepare('UPDATE tbladdresse SET mainAdd = 0 WHERE userID = ?');
                                $sql->execute([$userID]);
                            }

                            if(isset($_GET['idadd'])){ 
                                // Update
                                $idadd = $_GET['idadd'];
                                $stat = $con->prepare('UPDATE tbladdresse SET NameAdd=?,phoneNumber=?,emailAdd=?,provinceID=?,cityID=?,street=?,poatalCode=?,bultingNo=?,doorNo=?,noteAdd=?,mainAdd=?,addActive=? WHERE addresseID=? AND userID=?');
                                $stat->execute([$NameAdd,$phoneNumber,$emailAdd,$provinceID,$cityID,$street,$poatalCode,$bultingNo,$doorNo,$noteAdd,$mainAdd,$addActive,$idadd,$userID]);
                                echo '<script>location.href="info.php"</script>';
                            }
                        }
                    ?>
                    <script>
                    $(document).ready(function() {
                        // 🔹 Province change → load related cities
                        $('#provinceSelect').on('change', function() {
                            const provinceID = $(this).val();

                            if (provinceID == 0) {
                                $('#citySelect').html('<option value="">SELECT ONE</option>');
                                return;
                            }

                            $.ajax({
                                url: 'ajaxuser/getCities.php',
                                type: 'POST',
                                data: { provinceID },
                                dataType: 'json',
                                success: function(response) {
                                    let options = '<option value="">SELECT ONE</option>';
                                    if (response.length > 0) {
                                        $.each(response, function(index, city) {
                                            options += `<option value="${city.cityID}">${city.cityName}</option>`;
                                        });
                                    } else {
                                        options += '<option value="">No cities found</option>';
                                    }
                                    $('#citySelect').html(options);
                                }
                            });
                        });

                        // 🔹 Auto-load correct cities on page load (Edit Mode)
                        const selectedProvince = $('#provinceSelect').val();
                        const selectedCity = "<?= isset($result_add) ? $result_add['cityID'] : '' ?>";

                        if (selectedProvince && selectedProvince != "0") {
                            $.ajax({
                                url: 'ajaxuser/getCities.php',
                                type: 'POST',
                                data: { provinceID: selectedProvince },
                                dataType: 'json',
                                success: function(response) {
                                    let options = '<option value="">SELECT ONE</option>';
                                    if (response.length > 0) {
                                        $.each(response, function(index, city) {
                                            const sel = (city.cityID == selectedCity) ? "selected" : "";
                                            options += `<option value="${city.cityID}" ${sel}>${city.cityName}</option>`;
                                        });
                                    }
                                    $('#citySelect').html(options);
                                }
                            });
                        }
                    });
                    </script>

                <?php   
                }elseif($do=='deleteAdd'){
                    if (!isset($_GET['idadd'])) {
                        echo '<script>location.href="info.php"</script>';
                        exit;
                    }


                    $idadd = $_GET['idadd'];
                    $sql = $con->prepare('SELECT * FROM tbladdresse WHERE addresseID = ? AND userID = ?');
                    $sql->execute([$idadd, $user_id]);
                    $result_add = $sql->fetch(PDO::FETCH_ASSOC);

                    if (!$result_add) {
                        echo '<script>location.href="info.php"</script>';
                        exit;
                    }

                    $sql=$con->prepare('UPDATE tbladdresse SET addActive = 0 WHERE userID = ? AND addresseID = ? ');
                    $sql->execute([$user_id,$idadd ]);

                    echo '<script>location.href="info.php"</script>';

                    
                }elseif($do=='makemain'){
                    if (!isset($_GET['idadd'])) {
                        echo '<script>location.href="info.php"</script>';
                        exit;
                    }


                    $idadd = $_GET['idadd'];
                    $sql = $con->prepare('SELECT * FROM tbladdresse WHERE addresseID = ? AND userID = ?');
                    $sql->execute([$idadd, $user_id]);
                    $result_add = $sql->fetch(PDO::FETCH_ASSOC);

                    if (!$result_add) {
                        echo '<script>location.href="info.php"</script>';
                        exit;
                    }

                    $sql=$con->prepare('UPDATE tbladdresse SET mainAdd = 0 WHERE userID = ?');
                    $sql->execute([$user_id]);

                    $sql=$con->prepare('UPDATE tbladdresse SET mainAdd = 1 WHERE userID = ? AND addresseID = ? ');
                    $sql->execute([$user_id,$idadd ]);


                    echo '<script>location.href="info.php"</script>';


                }else{
                    header("Location: ../login.php");
                    exit(); 
                }
            ?>
        </div>
    </main>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/info.js"></script>
</body>