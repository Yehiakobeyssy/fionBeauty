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

    $do= isset($_GET['do'])?$_GET['do']:'manage';

    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageAdmins.css">
</head>
<body>  
    <?php include 'include/adminheader.php' ?>
    <main>
        <?php include 'include/adminaside.php'?>
        <div class="container_info">
            <div class="btnaddnewaddmin">
                <a href="manageAdmins.php?do=add" class="btn btn-primary"> + Add Admin</a>
            </div>
            <?php
                if($do == 'manage'){?>
                    <div class="statistic">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24" fill="none">
                            <rect x="0.5" y="0.5" width="24.0435" height="23" rx="7.5" fill="#FAFAFA"/>
                            <rect x="0.5" y="0.5" width="24.0435" height="23" rx="7.5" stroke="#EFEFFD"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5217 2C11.9454 2 11.4782 2.44772 11.4782 3V12C11.4782 12.5523 11.9454 13 12.5217 13H21.913C22.4893 13 22.9565 12.5523 22.9565 12C22.9565 6.47715 18.2847 2 12.5217 2ZM13.5652 11V4.06189C17.3412 4.51314 20.3341 7.38128 20.8049 11H13.5652Z" fill="#009245"/>
                            <path d="M9.77097 4.44419C10.3151 4.26228 10.6023 3.69208 10.4125 3.17062C10.2227 2.64915 9.6277 2.37389 9.08357 2.5558C5.01119 3.91725 2.08691 7.63006 2.08691 12C2.08691 17.5228 6.75873 22 12.5217 22C17.0667 22 20.9305 19.2159 22.3625 15.3332C22.5545 14.8125 22.2697 14.2412 21.7264 14.0572C21.183 13.8731 20.5868 14.146 20.3948 14.6668C19.2483 17.7753 16.1548 20 12.5217 20C7.91132 20 4.17387 16.4183 4.17387 12C4.17387 8.50685 6.51063 5.53417 9.77097 4.44419Z" fill="#009245"/>
                        </svg>
                        <div class="statiscticNumbers">
                            <?php
                                // Count all admins
                                $sql_all = $con->prepare("SELECT COUNT(*) AS total_admins FROM tbladmin");
                                $sql_all->execute();
                                $all = $sql_all->fetch(PDO::FETCH_ASSOC)['total_admins'];

                                // Count active admins
                                $sql_active = $con->prepare("SELECT COUNT(*) AS active_admins FROM tbladmin WHERE adminActive = 1");
                                $sql_active->execute();
                                $active = $sql_active->fetch(PDO::FETCH_ASSOC)['active_admins'];

                                // Count blocked admins
                                $sql_block = $con->prepare("SELECT COUNT(*) AS blocked_admins FROM tbladmin WHERE admin_block = 1");
                                $sql_block->execute();
                                $block = $sql_block->fetch(PDO::FETCH_ASSOC)['blocked_admins'];
                            ?>

                            <div class="number_card">
                                <label for="">All Admin</label>
                                <h6><?= $all ?></h6>
                            </div>
                            <div class="number_card">
                                <label for="">Active</label>
                                <h6><?= $active ?></h6>
                            </div>
                            <div class="number_card">
                                <label for="">Block</label>
                                <h6><?= $block ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="tbladmin">
                        <div class="filter_admin">
                            <div class="filter-row">
                                <div class="filter-item duration-buttons">
                                    <div class="duration-group">
                                        <button type="button" class="duration-btn active" data-value="9999">All Time</button>
                                        <button type="button" class="duration-btn" data-value="365">Last Year</button>
                                        <button type="button" class="duration-btn" data-value="30">Last Month</button>
                                        <button type="button" class="duration-btn" data-value="7">Last Week</button>
                                        <button type="button" class="duration-btn" data-value="1">Today</button>
                                    </div>
                                    <input type="hidden" name="duration" id="duration" value="9999">
                                </div>
                                <div class="filter-item">
                                    <input type="text" id="search" name="search" placeholder="Search Admin...">
                                </div>
                                <div class="filter-item">
                                    <input type="date" id="date" name="date" placeholder="Select Date">
                                </div>
                            </div>
                        </div>
                        <table>
                            <thead>
                                <th>Admin Name </th>
                                <th>Admin since</th>
                                <th>Role</th>
                                <th>status</th>
                                <th>action</th>
                            </thead>
                            <tbody id="tblmanageAdmin">

                            </tbody>
                        </table>
                    </div>
                <?php
                }elseif($do== 'add'){?>
                        <?php
                            if(isset($_POST['btnaddadmin'])){
                                $adminPassword = generatePassword();
                                $plainPassword = $adminPassword; // keep the generated password before hashing
                                $hashadminPassword = sha1($adminPassword);
                                
                                $checkEmail = checkItem('adminEmail', 'tbladmin',$_POST['adminEmail']);

                                if($checkEmail == 0){
                                    $fName          = $_POST['fName'];
                                    $lName          = $_POST['lName'];
                                    $phoneNumber    = $_POST['phoneNumber'];
                                    $adminEmail     = $_POST['adminEmail'];
                                    $adminPassword  = $hashadminPassword;
                                    $adminRole      = $_POST['adminRole'];
                                    $province       = $_POST['province'];
                                    $adminActive    = $_POST['adminActive'];
                                    $token          = bin2hex(random_bytes(16));
                                    $admin_block    = 0;

                                    $sql=$con->prepare('INSERT INTO tbladmin (fName,lName,phoneNumber,adminEmail,adminPassword,adminRole,province,adminActive,token,admin_block)
                                                        VALUES (?,?,?,?,?,?,?,?,?,?)');
                                    $sql->execute([$fName,$lName,$phoneNumber,$adminEmail,$adminPassword,$adminRole,$province,$adminActive,$token,$admin_block]);


                                    include '../mail.php';

                                    $mail->setFrom($applicationemail, 'Fion Beauty'); // Sender
                                    $mail->addAddress($adminEmail);                    // Receiver
                                    $mail->Subject = 'You’ve Been Added as an Admin at Fion Beauty Supplies 💚';

                                    // Content
                                    $mail->isHTML(true);
                                    $mail->CharSet = 'UTF-8';   
                                    $mail->Body = "
                                        <div style='font-family: Arial, sans-serif; background-color: #F8F8F8; padding: 25px;'>
                                            <div style='max-width:650px; margin:auto; background:#FFFFFF; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.08); padding:30px;'>
                                                
                                                <h2 style='color:#009245; text-align:center; font-weight:700;'>Welcome to Fion Beauty Supplies</h2>
                                                <p style='text-align:center; color:#6B6B6B; font-size:15px;'>You have been added as a new <strong>Admin</strong> in our management system.</p>

                                                <p style='color:#2E2E2E;'>Dear <strong>{$fName} {$lName}</strong>,</p>
                                                <p style='color:#2E2E2E;'>We are pleased to inform you that your administrator account has been successfully created.  
                                                Please find your login credentials below:</p>

                                                <table style='border-collapse:collapse; width:100%; margin:20px 0; font-size:14px;'>
                                                    <tr>
                                                        <td style='padding:10px; border:1px solid #ddd; background:#F6F6F6; width:35%; font-weight:bold; color:#2E2E2E;'>Email</td>
                                                        <td style='padding:10px; border:1px solid #ddd; color:#2E2E2E;'>{$adminEmail}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style='padding:10px; border:1px solid #ddd; background:#F6F6F6; font-weight:bold; color:#2E2E2E;'>Password</td>
                                                        <td style='padding:10px; border:1px solid #ddd; color:#2E2E2E;'>{$plainPassword}</td>
                                                    </tr>
                                                </table>

                                                <p style='color:#2E2E2E;'>You can log in to the admin panel using the following link:</p>
                                                <p style='text-align:center; margin:25px 0;'>
                                                    <a href='{$websiteaddresse}admin/index.php' 
                                                    style='background:#009245; color:#FFFFFF; padding:12px 25px; border-radius:5px; text-decoration:none; font-weight:bold; display:inline-block;'>
                                                    Go to Admin Panel
                                                    </a>
                                                </p>

                                                <p style='color:#6B6B6B; font-size:14px;'>If you didn’t expect this message, please contact our support team immediately.</p>

                                                <hr style='margin:30px 0; border:none; border-top:1px solid #eee;'>

                                                <p style='font-size:13px; color:#999; text-align:center;'>
                                                    © " . date('Y') . " Fion Beauty Supplies. All rights reserved.<br>
                                                    This is an automated message — please do not reply directly.
                                                </p>

                                            </div>
                                        </div>
                                    ";
                                    $mail->send();

                                    echo '<div class="conformnewadmin ">
                                            You Added new admin Successfully <br>
                                            Email and password will be sent
                                            </div> ';
                                }
                            }
                        ?>
                        
                    
                    <div class="newAdmin">
                        <div class="title">
                            <h4>Adding New Admin</h4>
                        </div>
                        <form action="" method="post">
                            <div class="double">
                                <input type="text" name="fName" id="" placeholder="First Name" required>
                                <input type="text" name="lName" id="" placeholder="Last Name" required>
                            </div>
                            <div class="double">
                                <input type="text" name="phoneNumber" id="" placeholder="Phone Number">
                                <input type="email" name="adminEmail" id="adminnewemail" placeholder="Email" required>
                                
                            </div>
                            <small id="emailError" style="color:red; display:none;"></small>
                            <div class="double">
                                <select name="adminRole" id="">
                                    <option value="" disabled selected hidden>Role</option>
                                    <?php
                                        $sql=$con->prepare('SELECT adminRollId ,adminRoll FROM tbladminroll');
                                        $sql->execute();
                                        $roles = $sql->fetchAll();
                                        foreach($roles as $role){
                                            echo '<option value="'.$role['adminRollId'].'">'.$role['adminRoll'].'</option>';
                                        }
                                    ?>
                                </select>
                                <select name="province" id="">
                                    <option value="" disabled selected hidden> Province</option>
                                    <?php
                                        $sql= $con->prepare('SELECT provinceID ,provinceName FROM tblprovince ORDER BY provinceName');
                                        $sql->execute();
                                        $provinces = $sql->fetchAll();
                                        foreach($provinces as $province){
                                            echo '<option value="'.$province['provinceID'].'"> '.$province['provinceName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="full">
                                <select name="adminActive" id="" required>
                                    <option value="" disabled selected hidden>Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">InActive</option>
                                </select>
                            </div>
                            <div class="btncontrol">
                                <button type="reset" class="btn btn-outboder">Cancel</button>
                                <button type="submit" class="btn btn-inboder"  name="btnaddadmin">Add Admnin</button>
                            </div>
                        </form>
                    </div>
                <?php
                }elseif($do == 'edid'){?>
                    <?php
                        $adminId = isset($_GET['adminId'])?$_GET['adminId']:0;
                        $checkadmin =  checkItem('adminID','tbladmin', $adminId);
                        if($checkadmin == 0){
                            echo '<script>location.href="manageAdmins.php"</script>';
                        }

                        if (isset($_POST['btneditadmin'])) {
                            $fName         = $_POST['fName'];
                            $lName         = $_POST['lName'];
                            $phoneNumber   = $_POST['phoneNumber'];
                            $adminRole     = $_POST['adminRole'];
                            $province      = $_POST['province'];
                            $adminActive   = $_POST['adminActive'];

                            $sql = $con->prepare("UPDATE tbladmin 
                                                SET fName = ?, 
                                                    lName = ?, 
                                                    phoneNumber = ?, 
                                                    adminRole = ?, 
                                                    province = ?, 
                                                    adminActive = ?
                                                WHERE adminID = ?");
                            $sql->execute([$fName, $lName, $phoneNumber, $adminRole, $province, $adminActive, $adminId]);

                            
                        }

                        $sql = $con->prepare("SELECT * FROM tbladmin WHERE adminID = ?");
                        $sql->execute([$adminId]);
                        $admin = $sql->fetch();
                    ?>

                    <div class="newAdmin">
                        <div class="title">
                            <h4>Edit Admin</h4>
                        </div>
                        <form action="" method="post">
                            <input type="hidden" name="adminId" value="<?php echo $admin['adminID']; ?>">

                            <div class="double">
                                <input type="text" name="fName" placeholder="First Name" value="<?php echo htmlspecialchars($admin['fName']); ?>" required>
                                <input type="text" name="lName" placeholder="Last Name" value="<?php echo htmlspecialchars($admin['lName']); ?>" required>
                            </div>

                            <div class="double">
                                <input type="text" name="phoneNumber" placeholder="Phone Number" value="<?php echo htmlspecialchars($admin['phoneNumber']); ?>">
                                <input type="email" name="adminEmail" placeholder="Email" value="<?php echo htmlspecialchars($admin['adminEmail']); ?>" required disabled>
                            </div>

                            <div class="double">
                                <select name="adminRole" required>
                                    <option value="" disabled hidden>Role</option>
                                    <?php
                                        $sql = $con->prepare('SELECT adminRollId, adminRoll FROM tbladminroll');
                                        $sql->execute();
                                        $roles = $sql->fetchAll();
                                        foreach ($roles as $role) {
                                            $selected = ($role['adminRollId'] == $admin['adminRole']) ? 'selected' : '';
                                            echo '<option value="'.$role['adminRollId'].'" '.$selected.'>'.$role['adminRoll'].'</option>';
                                        }
                                    ?>
                                </select>

                                <select name="province" required>
                                    <option value="" disabled hidden>Province</option>
                                    <?php
                                        $sql = $con->prepare('SELECT provinceID, provinceName FROM tblprovince ORDER BY provinceName');
                                        $sql->execute();
                                        $provinces = $sql->fetchAll();
                                        foreach ($provinces as $province) {
                                            $selected = ($province['provinceID'] == $admin['province']) ? 'selected' : '';
                                            echo '<option value="'.$province['provinceID'].'" '.$selected.'>'.$province['provinceName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="full">
                                <select name="adminActive" required>
                                    <option value="" disabled hidden>Select Status</option>
                                    <option value="1" <?php echo ($admin['adminActive'] == 1 ? 'selected' : ''); ?>>Active</option>
                                    <option value="0" <?php echo ($admin['adminActive'] == 0 ? 'selected' : ''); ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="btncontrol">
                                <button type="reset" class="btn btn-outboder">Cancel</button>
                                <button type="submit" class="btn btn-inboder" name="btneditadmin">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php
                }elseif($do == 'block'){?>
                    <?php
                        $adminId = isset($_GET['adminId']) ? $_GET['adminId'] : 0;
                        $checkadmin = checkItem('adminID', 'tbladmin', $adminId);

                        if ($checkadmin == 0) {
                            echo '<script>location.href="manageAdmins.php"</script>';
                            exit;
                        }

                        // Fetch admin current status
                        $sql = $con->prepare('SELECT adminActive, admin_block, fName, lName FROM tbladmin WHERE adminID = ?');
                        $sql->execute([$adminId]);
                        $result = $sql->fetch();

                        // Handle form submission
                        if (isset($_POST['action'])) {
                            $action = $_POST['action'];

                            switch ($action) {
                                case 'activate':
                                    $sql = $con->prepare("UPDATE tbladmin SET adminActive = 1 WHERE adminID = ?");
                                    $sql->execute([$adminId]);
                                    $msg = "✅ Admin has been activated successfully.";
                                    break;

                                case 'deactivate':
                                    $sql = $con->prepare("UPDATE tbladmin SET adminActive = 0 WHERE adminID = ?");
                                    $sql->execute([$adminId]);
                                    $msg = "⚠️ Admin has been deactivated.";
                                    break;

                                case 'block':
                                    $sql = $con->prepare("UPDATE tbladmin SET admin_block = 1 WHERE adminID = ?");
                                    $sql->execute([$adminId]);
                                    $msg = "🚫 Admin has been blocked.";
                                    break;

                                case 'unblock':
                                    $sql = $con->prepare("UPDATE tbladmin SET admin_block = 0 WHERE adminID = ?");
                                    $sql->execute([$adminId]);
                                    $msg = "✅ Admin has been unblocked.";
                                    break;

                                case 'cancel':
                                    echo '<script>location.href="manageAdmins.php"</script>';
                                    exit;
                            }

                            // Refresh to reflect changes
                            echo '<div class="adminmsg">'.$msg.'</div>';
                            echo '<script>
                                    setTimeout(function(){
                                        location.href = "manageAdmins.php";
                                    }, 1000);
                                </script>';
                            $sql->execute([$adminId]);
                            $sql = $con->prepare('SELECT adminActive, admin_block, fName, lName FROM tbladmin WHERE adminID = ?');
                            $sql->execute([$adminId]);
                            $result = $sql->fetch();
                        }
                    ?>

                    <style>
                        .admin-control-box {
                            max-width: 500px;
                            margin: 40px auto;
                            background: #fff;
                            padding: 30px;
                            border-radius: 15px;
                            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                            font-family: 'Arial', sans-serif;
                            text-align: center;
                        }
                        .admin-control-box h3 {
                            color: #333;
                            margin-bottom: 20px;
                        }
                        .adminmsg {
                            max-width: 500px;
                            margin: 20px auto;
                            background: #eafaf1;
                            border-left: 5px solid #28a745;
                            color: #155724;
                            padding: 15px;
                            border-radius: 8px;
                            font-size: 15px;
                            text-align: center;
                        }
                        .admin-control-box form button {
                            border: none;
                            padding: 12px 25px;
                            border-radius: 8px;
                            font-weight: bold;
                            font-size: 14px;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            margin: 10px;
                        }
                        .btn-green {
                            background-color: #28a745;
                            color: #fff;
                        }
                        .btn-red {
                            background-color: #dc3545;
                            color: #fff;
                        }
                        .btn-gray {
                            background-color: #6c757d;
                            color: #fff;
                        }
                        .btn-green:hover { background-color: #218838; }
                        .btn-red:hover { background-color: #c82333; }
                        .btn-gray:hover { background-color: #5a6268; }
                    </style>

                    <div class="admin-control-box">
                        <h3>Manage Admin: <?php echo htmlspecialchars($result['fName'].' '.$result['lName']); ?></h3>

                        <form method="post">
                            <?php if ($result['adminActive'] == 0): ?>
                                <button type="submit" name="action" value="activate" class="btn-green">Activate Admin</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="deactivate" class="btn-red">Make Admin Inactive</button>
                            <?php endif; ?>

                            <?php if ($result['admin_block'] == 0): ?>
                                <button type="submit" name="action" value="block" class="btn-red">Block Admin</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="unblock" class="btn-green">Unblock Admin</button>
                            <?php endif; ?>

                            <button type="submit" name="action" value="cancel" class="btn-gray">Cancel</button>
                        </form>
                    </div>
                <?php
                }else{
                    echo '<script> location.href = "manageAdmins.php" </script>';
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageAdmins.js"></script>
</body>