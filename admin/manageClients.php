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

    $do = isset($_GET['do'])?$_GET['do']:'manage';

    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageClients.css">
    
</head>
<body> 
    <?php include 'include/adminheader.php' ?>
    <?php
            $sql = $con->prepare('SELECT admin_block FROM  tbladmin WHERE adminID   = ?');
            $sql->execute([$admin_id]);
            $result_block = $sql->fetch();
            $isBlock = $result_block['admin_block'];

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
    <main>
        <?php include 'include/adminaside.php'?>
        <div class="container_info">
            <div class="addnewcustomer">
                <a href="manageClients.php?do=add">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                        <path d="M5.66667 12.4167C5.66667 12.8769 6.03976 13.25 6.5 13.25C6.96024 13.25 7.33333 12.8769 7.33333 12.4167V7.83333H11.9167C12.3769 7.83333 12.75 7.46024 12.75 7C12.75 6.53976 12.3769 6.16667 11.9167 6.16667H7.33333V1.58333C7.33333 1.1231 6.96024 0.75 6.5 0.75C6.03976 0.75 5.66667 1.1231 5.66667 1.58333V6.16667H1.08333C0.623096 6.16667 0.25 6.53976 0.25 7C0.25 7.46024 0.623096 7.83333 1.08333 7.83333H5.66667V12.4167Z" fill="#FAFAFA"/>
                    </svg>
                    Add New Customer
                </a>
            </div>
            <?php
                if($do == 'manage'){?>
                    <div class="mainstatistic card">
                        <div class="title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="19" viewBox="0 0 22 19" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.6087 4.5C13.6087 6.98528 11.5064 9 8.91304 9C6.31971 9 4.21739 6.98528 4.21739 4.5C4.21739 2.01472 6.31971 0 8.91304 0C11.5064 0 13.6087 2.01472 13.6087 4.5ZM11.5217 4.5C11.5217 5.88071 10.3538 7 8.91304 7C7.4723 7 6.30435 5.88071 6.30435 4.5C6.30435 3.11929 7.4723 2 8.91304 2C10.3538 2 11.5217 3.11929 11.5217 4.5Z" fill="#009245"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.565216 16.9231C0.565216 13.0996 3.79955 10 7.7893 10H10.0368C14.0265 10 17.2609 13.0996 17.2609 16.9231C17.2609 18.0701 16.2906 19 15.0936 19H2.73244C1.53552 19 0.565216 18.0701 0.565216 16.9231ZM2.65217 16.9231C2.65217 14.2041 4.95214 12 7.7893 12H10.0368C12.8739 12 15.1739 14.2041 15.1739 16.9231C15.1739 16.9656 15.138 17 15.0936 17H2.73244C2.68811 17 2.65217 16.9656 2.65217 16.9231Z" fill="#009245"/>
                                <path d="M18.2206 18.0973C18.1128 18.4981 18.3851 19 18.817 19H19.2676C20.4645 19 21.4348 18.0701 21.4348 16.9231C21.4348 13.0996 18.2005 10 14.2107 10C14.0623 10 14.0082 10.1975 14.1342 10.2727C15.1211 10.8614 15.9779 11.6314 16.6538 12.5343C16.6996 12.5955 16.7586 12.6466 16.8263 12.685C18.3358 13.5424 19.3478 15.1195 19.3478 16.9231C19.3478 16.9656 19.3119 17 19.2676 17H18.7733C18.5115 17 18.3044 17.2106 18.3044 17.4615C18.3044 17.6811 18.2753 17.8941 18.2206 18.0973Z" fill="#009245"/>
                                <path d="M14.0459 6.96308C14.1033 6.85341 14.1978 6.76601 14.3108 6.70836C15.1347 6.28814 15.6956 5.45685 15.6956 4.5C15.6956 3.54315 15.1347 2.71186 14.3108 2.29164C14.1978 2.23399 14.1033 2.14659 14.0459 2.03692C13.7543 1.48001 13.367 0.977121 12.9046 0.548096C12.6936 0.352311 12.7932 0 13.0869 0C15.6803 0 17.7826 2.01472 17.7826 4.5C17.7826 6.98528 15.6803 9 13.0869 9C12.7932 9 12.6936 8.64769 12.9046 8.4519C13.367 8.02288 13.7543 7.51999 14.0459 6.96308Z" fill="#009245"/>
                            </svg>
                        </div>
                            <?php
                            // 🟢 Count all customers
                            $stmt = $con->prepare("SELECT COUNT(*) FROM tblclient");
                            $stmt->execute();
                            $totalClients = $stmt->fetchColumn();

                            // 🟢 Count active customers
                            $stmt = $con->prepare("SELECT COUNT(*) FROM tblclient WHERE clientActive = 1");
                            $stmt->execute();
                            $activeClients = $stmt->fetchColumn();

                            // 🟢 Count inactive customers
                            $stmt = $con->prepare("SELECT COUNT(*) FROM tblclient WHERE clientActive = 0");
                            $stmt->execute();
                            $inactiveClients = $stmt->fetchColumn();

                            // 🟢 Count blocked customers
                            $stmt = $con->prepare("SELECT COUNT(*) FROM tblclient WHERE clientBlock = 1");
                            $stmt->execute();
                            $blockedClients = $stmt->fetchColumn();
                            ?>

                            <div class="card_info_client">
                                <div class="part">
                                    <label>All Customers</label>
                                    <span><?= $totalClients ?></span>
                                </div>
                                <div class="part">
                                    <label>Active Customers</label>
                                    <span><?= $activeClients ?></span>
                                </div>
                                <div class="part">
                                    <label>Inactive Customers</label>
                                    <span><?= $inactiveClients ?></span>
                                </div>
                                <div class="part">
                                    <label>Blocked Customers</label>
                                    <span><?= $blockedClients ?></span>
                                </div>
                            </div>
                        
                    </div>
                    <div class="sectiontablecontaint">
                        <div class="filter-section">
                            <div class="filter-item">
                                <label for="search">Search</label>
                                <input type="text" id="search" placeholder="Search by name or email">
                            </div>

                            <div class="filter-item">
                                <label for="date">Date</label>
                                <input type="date" id="date">
                            </div>

                            <div class="filter-item">
                                <label for="status">Active</label>
                                <select id="status">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="block">Blocked</label>
                                <select id="block">
                                    <option value="">All</option>
                                    <option value="1">Blocked</option>
                                    <option value="0">Not Blocked</option>
                                </select>
                            </div>
                        </div>
                        <div class="tblClients">
                            <table>
                                <thead>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Certivicate</th>
                                    <th>Orders</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Blocked</th>
                                    <th>Action</th>
                                </thead>
                                <tbody id="tblresult">

                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php
                }elseif($do=='add'){?>
                    <div class="containernewClient">
                        <div class="title">
                            <h4>Add New Customer</h4>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="double">
                                <label for="">Client Name</label>
                                <div class="inputs">
                                    <input type="text" name="clientFname" id="" placeholder="First Name" required>
                                    <input type="text" name="clientLname" id="" placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="double">
                                <label for="">Contact:</label>
                                <div class="inputs">
                                    <input type="text" name="clientPhoneNumber" id="" placeholder="Phone Number">
                                    <input type="email" name="clientEmail" id="txtemail" placeholder="E-mail" required>
                                </div>
                                <span id="erroremail"></span>
                            </div>
                            <div class="double">
                                <label for="">Certivicate</label>
                                <div class="inputs">
                                    <select name="profession" id="" required>
                                        <option value="0">SELECT ONE</option>
                                        <?php
                                            $sql =$con->prepare('SELECT professionID ,profession FROM tblprofession WHERE professionAcctive = 1');
                                            $sql->execute();
                                            $professions = $sql->fetchAll();
                                            foreach($professions as $pro){
                                                echo '<option value="'.$pro['professionID'].'">'.$pro['profession'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <input type="file" name="certificate" id="">
                                </div>
                            </div>
                            <div class="double">
                                <label for="">Password</label>
                                <input type="password" name="clientPassword" id="" required>
                            </div>
                            <div class="double">
                                <label for="">About Client</label>
                                <textarea name="clientAbout" id="" placeholder="Note about Client"></textarea>
                            </div>
                            <div class="btncontrol">
                                <button type="submit" class="btn btn-primary" name="btnsaveuser">Add Client</button>
                            </div>

                            <?php
                                if(isset($_POST['btnsaveuser'])){
                                    $email = $_POST['clientEmail'];
                                    $checkemail = checkItem('clientEmail','tblclient', $email);
                                    
                                    if($checkemail == 0){
                                        $newfilename = '';
                                        if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
                                            $temp = explode(".", $_FILES['certificate']['name']);
                                            $ext = strtolower(end($temp));
                                            $allowed = ['jpg','jpeg','png','pdf'];

                                            if (!in_array($ext, $allowed)) {
                                                $response['message'] = "Invalid file type. Allowed: jpg, jpeg, png, pdf.";
                                                echo json_encode($response); exit;
                                            }

                                            $newfilename = round(microtime(true)) . '.' . $ext;
                                            $uploadPath = '../documents/' . $newfilename;

                                            if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $uploadPath)) {
                                                $response['message'] = "File upload failed.";
                                                echo json_encode($response); exit;
                                            }
                                        }

                                        $clientFname        = trim($_POST['clientFname'] ?? '');
                                        $clientLname        = trim($_POST['clientLname'] ?? '');
                                        $clientPhoneNumber  = trim($_POST['clientPhoneNumber'] ?? '');
                                        $clientEmail        = trim($_POST['clientEmail'] ?? '');
                                        $profession         = (int)($_POST['profession'] ?? 0);
                                        $clientPassword     = $_POST['clientPassword'] ?? '';
                                        $hashedPassword     = sha1($clientPassword);
                                        $clientActive       = 1;
                                        $clientActivation   = sha1(date('Y.m.d'));
                                        $clientBlock        = 0;
                                        $pushId             = '';
                                        $clientAbout        = $_POST['clientAbout'];

                                        $sql = $con->prepare("INSERT INTO tblclient 
                                                                (clientFname, clientLname, clientPhoneNumber, clientEmail, certificate, profession, clientPassword, clientActive, clientActivation, clientBlock, pushId, clientAbout) 
                                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                                        
                                        $sql->execute([
                                            $clientFname,
                                            $clientLname,
                                            $clientPhoneNumber,
                                            $clientEmail,
                                            $newfilename,
                                            $profession,
                                            $hashedPassword,
                                            $clientActive,
                                            $clientActivation,
                                            $clientBlock,
                                            $pushId,
                                            $clientAbout
                                        ]);
                                        echo "<script>alert('Client added successfully!');</script>";
                                    }
                                }
                            ?>
                        </form>
                    </div>
                <?php
                }elseif($do=='view'){
                    $userID = isset($_GET['clientID'])?$_GET['clientID']:0;
                    $checkUser = checkItem('clientID','tblclient', $userID);

                    if($checkUser == 1){?>
                        <div class="btnblock">
                            <?php
                                $sql= $con->prepare('SELECT clientActive , clientBlock  FROM tblclient WHERE clientID  = ?');
                                $sql->execute([$userID]);
                                $resultbtn= $sql->fetch();
                                $isActive = $resultbtn['clientActive'];
                                $isBlocked = $resultbtn['clientBlock'];

                                if ($isActive == 1) {
                                    $txtbtn = 'Set Inactive';
                                    $class_btnactive = 'btn btn-red';
                                } else {
                                    $txtbtn = 'Set Active';
                                    $class_btnactive = 'btn btn-green';
                                }

                                if($isBlocked == 0){
                                    $txtbtnblock = 'Block';
                                    $class_btnblock = 'btn btn-red';
                                }else{
                                    $txtbtnblock = 'UnBlock';
                                    $class_btnblock = 'btn btn-green';
                                }
                            ?>
                            <button class="<?=$class_btnactive?> btngotoactive" value="<?=$userID?>"><?=$txtbtn?></button>
                            <button class="<?=$class_btnblock?> btngotoblock" value="<?=$userID?>"><?=$txtbtnblock?></button>
                        </div>
                        <?php
                            $sql = "
                                    SELECT 
                                        IFNULL(SUM(CASE WHEN invoiceStatus < 5 THEN invoiceAmount ELSE 0 END), 0) AS totalBalance,
                                        COUNT(*) AS allOrders,
                                        SUM(CASE WHEN invoiceStatus < 4 THEN 1 ELSE 0 END) AS processing,
                                        SUM(CASE WHEN invoiceStatus = 6 THEN 1 ELSE 0 END) AS returned,
                                        SUM(CASE WHEN invoiceStatus = 5 THEN 1 ELSE 0 END) AS cancelled,
                                        SUM(CASE WHEN invoiceStatus = 4 THEN 1 ELSE 0 END) AS completed
                                    FROM tblinvoice
                                    WHERE clientID = :userID
                                    ";

                            // تنفيذ الاستعلام
                            $stmt = $con->prepare($sql);
                            $stmt->execute([':userID' => $userID]);
                            $data = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="statistic_card">
                            <div class="totalBalance">
                                <label for="">Total Balance</label>
                                <h4><?= number_format($data['totalBalance'], 2) ?></h4>
                            </div>
                            <div class="daitaailorders">
                                <div class="card_number">
                                    <label for="">All Orders</label>
                                    <h5><?= $data['allOrders'] ?></h5>
                                </div>
                                <div class="card_number">
                                    <label for="">Processing</label>
                                    <h5><?= $data['processing'] ?></h5>
                                </div>
                                <div class="card_number">
                                    <label for="">Returned</label>
                                    <h5><?= $data['returned'] ?></h5>
                                </div>
                                <div class="card_number">
                                    <label for="">Cancelled</label>
                                    <h5><?= $data['cancelled'] ?></h5>
                                </div>
                                <div class="card_number">
                                    <label for="">Completed</label>
                                    <h5><?= $data['completed'] ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="info_client">
                            <div class="client_info">
                                <?php
                                    // Prepare and execute query (LEFT JOIN allows clients without profession)
                                    $sql = $con->prepare('
                                        SELECT 
                                            clientFname, clientLname, clientPhoneNumber, clientEmail, 
                                            certificate, tblprofession.profession, clientFirstLogin
                                        FROM tblclient
                                        LEFT JOIN tblprofession ON tblprofession.professionID = tblclient.profession
                                        WHERE clientID = ?
                                    ');
                                    $sql->execute([$userID]);
                                    $result = $sql->fetch(PDO::FETCH_ASSOC);

                                    if ($result) {
                                        // Safely extract values with defaults
                                        $fname   = $result['clientFname'] ?? '';
                                        $lname   = $result['clientLname'] ?? '';
                                        $fullname = trim("$fname $lname");
                                        $initials = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
                                        $firstLetter = strtoupper(substr($fname ?: 'A', 0, 1));

                                        // Letter → color map
                                        $colors = [
                                            'A' => '#4285F4','B' => '#34A853','C' => '#FBBC05','D' => '#EA4335','E' => '#9C27B0',
                                            'F' => '#FF5722','G' => '#009688','H' => '#795548','I' => '#3F51B5','J' => '#CDDC39',
                                            'K' => '#607D8B','L' => '#E91E63','M' => '#00BCD4','N' => '#8BC34A','O' => '#FFC107',
                                            'P' => '#673AB7','Q' => '#FF9800','R' => '#F44336','S' => '#4CAF50','T' => '#03A9F4',
                                            'U' => '#9E9E9E','V' => '#FFEB3B','W' => '#8E24AA','X' => '#1E88E5','Y' => '#D32F2F','Z' => '#2E7D32',
                                        ];
                                        $bgColor = $colors[$firstLetter] ?? '#333';
                                ?>
                                    <!-- Client Name -->
                                    <div class="name_client">
                                        <div class="design" style="background-color:<?= $bgColor ?>">
                                            <?= htmlspecialchars($initials ?: '?') ?>
                                        </div>
                                        <h5><?= htmlspecialchars($fullname ?: 'Unknown Client') ?></h5>
                                    </div>

                                    <!-- Contact Info -->
                                    <div class="comonication_info">
                                        <!-- Phone -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.6443 14.5494L13.1861 12.0912L11.5643 14.2389L10.9751 14.0063L10.9713 14.0048L10.964 14.0019L10.9388 13.9917C10.9175 13.9831 10.8872 13.9706 10.8489 13.9545C10.7722 13.9223 10.6629 13.8753 10.5278 13.8145C10.2581 13.6929 9.88341 13.515 9.45902 13.2874C8.62205 12.8386 7.54176 12.1704 6.70099 11.3297C5.86021 10.4889 5.19201 9.40854 4.74313 8.57153C4.51552 8.14711 4.33755 7.77241 4.21599 7.50267C4.15512 7.3676 4.10814 7.25829 4.07591 7.18158C4.05979 7.14321 4.04735 7.11294 4.03868 7.09164L4.02852 7.06649L4.02559 7.05915L4.02433 7.05599L3.79146 6.46613L5.9392 4.84431L3.48104 2.38616C2.99362 3.18265 2.36654 4.53715 2.36253 6.22129C2.35837 7.96782 3.02334 10.178 5.43797 12.5927C7.85262 15.0073 10.0628 15.6722 11.8093 15.668C13.4934 15.664 14.8478 15.0368 15.6443 14.5494ZM5.82718 7.01737L6.94356 6.17436C7.75121 5.56448 7.83334 4.38143 7.11771 3.6658L4.58762 1.13572C3.92966 0.477757 2.74215 0.445369 2.15584 1.36211C1.56243 2.28996 0.701122 4.01133 0.69587 6.21733C0.690547 8.45339 1.5655 11.0772 4.25946 13.7712C6.95344 16.4652 9.57725 17.3401 11.8133 17.3347C14.0193 17.3294 15.7406 16.468 16.6684 15.8746C17.5851 15.2882 17.5527 14.1008 16.8947 13.4428L14.3646 10.9127C13.649 10.1971 12.4659 10.2792 11.856 11.0869L11.013 12.2032C10.7974 12.1022 10.5346 11.973 10.2467 11.8186C9.46638 11.4002 8.55127 10.8229 7.8795 10.1511C7.20772 9.47937 6.63041 8.5642 6.21191 7.78384C6.05745 7.49582 5.92821 7.23298 5.82718 7.01737Z" fill="#17163A"/>
                                        </svg>
                                        <label><?= htmlspecialchars($result['clientPhoneNumber'] ?: 'No phone number') ?></label>

                                        <!-- Email -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14" fill="none">
                                            <path d="M1.25 0.75L10 7L18.75 0.75M1.25 13.25H18.75V0.75H1.25V13.25Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <label><?= htmlspecialchars($result['clientEmail'] ?: 'No email') ?></label>
                                    </div>

                                    <!-- Profession & Certificate -->
                                    <div class="certivicate_info">
                                        <label>Profession:</label>
                                        <span><?= htmlspecialchars($result['profession'] ?: 'Not specified') ?></span>

                                        <label>Certificate:</label>
                                        <?php if (!empty($result['certificate'])): ?>
                                            <a href="../documents/<?= htmlspecialchars($result['certificate']) ?>" target="_blank">Show</a>
                                        <?php else: ?>
                                            <span>No certificate uploaded</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Customer Since -->
                                    <div class="customersince">
                                        <label>Customer since:</label>
                                        <span><?= !empty($result['clientFirstLogin']) ? date("d M Y - h:i a", strtotime($result['clientFirstLogin'])) : 'N/A' ?></span>
                                    </div>

                                <?php
                                    } else {
                                        // No client found at all
                                        echo '<div class="alert alert-warning">No client record found for the provided ID.</div>';
                                    }
                                ?>
                            </div>
                            <div class="shipping_add">
                                <div class="title_shipping">
                                    <h4>Shipping Address</h4>
                                </div>
                                <div class="add">
                                    <?php
                                        $sql = $con->prepare('SELECT addresseID,NameAdd,emailAdd,phoneNumber,street, bultingNo, doorNo, poatalCode, cityName, provinceName 
                                                            FROM tbladdresse 
                                                            INNER JOIN tblcity ON tblcity.cityID = tbladdresse.cityID
                                                            INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
                                                            WHERE mainAdd = 1 AND userID = ?');
                                        $sql->execute([$userID]);
                                        $row = $sql->fetch(PDO::FETCH_ASSOC);

                                        if ($row) {
                                            // Display like a Canadian mailing address
                                            echo "<h5>{$row['NameAdd']}</h5>";
                                            echo "<address>
                                                    {$row['street']} {$row['bultingNo']} {$row['doorNo']}<br>
                                                    {$row['cityName']}, {$row['provinceName']}<br>
                                                    {$row['poatalCode']}
                                                </address>";
                                            echo "<label>{$row['emailAdd']}</label> <br>
                                                    <label> {$row['phoneNumber']} </label><br>";
                                            
                                        } else {
                                            // Show danger alert
                                            echo '
                                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                    <div>No address has been registered for this client.</div>
                                                </div>';

                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="tblorders_history">
                            <div class="title_history">
                                <h5>Order History</h5>
                            </div>
                            <div class="tblhistory">
                                <input type="hidden" id="clientID" value="<?= $userID ?>">
                                <table>
                                    <thead>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Produts</th>
                                        <th>Total</th>
                                        <th>Commition</th>
                                        <th>Trasaction NO:</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody id="tblordershistory">
                                        
                                    </tbody>
                                </table>        
                            </div>
                        </div>
                    <?php
                    }else{
                        echo '<script>location.href="manageClients.php"</script>';
                    }

                }elseif($do=='active'){
                    $userID = isset($_GET['clientID'])?$_GET['clientID']:0;
                    $checkUser = checkItem('clientID','tblclient', $userID);
                    if($checkUser == 1){
                        $sql = "UPDATE tblclient 
                                    SET clientActive = CASE WHEN clientActive = 1 THEN 0 ELSE 1 END
                                    WHERE clientID = :clientID";
                            
                        $stmt = $con->prepare($sql);
                        $stmt->bindValue(':clientID', $userID, PDO::PARAM_INT);
                        $stmt->execute();
                        echo '<script> location.href="manageClients.php?do=view&clientID='.$userID.'"</script>';
                    }else{
                        echo '<script>location.href="manageClients.php"</script>';
                    }
                }elseif($do=='block'){
                    $userID = isset($_GET['clientID'])?$_GET['clientID']:0;
                    $checkUser = checkItem('clientID','tblclient', $userID);
                    if($checkUser == 1){
                        $sql = "UPDATE tblclient 
                                    SET clientBlock = CASE WHEN clientBlock = 1 THEN 0 ELSE 1 END
                                    WHERE clientID = :clientID";
                            
                        $stmt = $con->prepare($sql);
                        $stmt->bindValue(':clientID', $userID, PDO::PARAM_INT);
                        $stmt->execute();
                        echo '<script> location.href="manageClients.php?do=view&clientID='.$userID.'"</script>';
                    }else{
                        echo '<script>location.href="manageClients.php"</script>';
                    }
                }else{
                    echo '<script>location.href="manageClients.php"</script>';
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageClients.js"></script>
</body>