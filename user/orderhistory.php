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

    $do= (isset($_GET['do']))?$_GET['do']:'manage'
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/orderhistory.css">
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
        <?php include 'include/aside.php' ?>
        <div class="sections_side">
            <?php
                if($do == 'manage'){?>
                    <div class="title_section">
                        <h3>Order History</h3>
                        <div class="sereachdiv">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <circle cx="8.80589" cy="8.30589" r="7.49047" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.0156 13.9043L16.9523 16.8334" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="" id="txtsearchorder" placeholder="Search">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tblorders">
                        </tbody>
                    </table>
                    <div id="pagination"></div>
                <?php
                }elseif($do=='detail'){
                        $orderID = isset($_GET['id'])?$_GET['id']:0;
                        $check_order= checkItem('invoiceID','tblinvoice',$orderID);

                        if($check_order == 0){
                            header("Location: orderhistory.php");
                            exit(); 
                        }else{
                            $sql=$con->prepare('SELECT invoiceDate FROM tblinvoice WHERE invoiceID  = ? ');
                            $sql->execute([$orderID]);
                            $result=$sql->fetch();
                            $dateInvoice = $result['invoiceDate'];
                            $formatdate =  date("j F, Y", strtotime($dateInvoice));

                            $sql= $con->prepare('SELECT COUNT(daitailInvoiceId) AS items FROM tbldatailinvoice WHERE invoiceID = ?');
                            $sql->execute([$orderID]);
                            $result= $sql->fetch();
                            $items = $result['items'];

                        }
                    ?>
                    <div class="title_section">
                        <h3>Order Details</h3>
                        <span><?= $formatdate .' - ' . '('. $items .'Products )'?></span>
                        <a href="orderhistory.php">Back to List</a>
                    </div>
                    <div class="invoice_info">
                        <div class="addreese">
                            <div class="main_add">
                                <div class="title_main_add">
                                    <h4>Billing Address</h4>
                                </div>
                                <?php
                                    
                                    
                                    $sql = $con->prepare('SELECT NameAdd,emailAdd,phoneNumber,street, bultingNo, doorNo, poatalCode, cityName, provinceName 
                                                        FROM tbladdresse 
                                                        INNER JOIN tblcity ON tblcity.cityID = tbladdresse.cityID
                                                        INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
                                                        WHERE mainAdd = 1 AND userID = ?');
                                    $sql->execute([$user_id]);
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
                                        echo '<div class="alert alert-danger">You haven\'t an address</div>';
                                    }
                                ?>
                            </div>
                            <div class="shiping_add">
                                <div class="title_main_add">
                                    <h4>Shipping Address</h4>
                                </div>
                                <?php
                                    $stat= $con->prepare('SELECT addresseId FROM  tblinvoice WHERE invoiceID  = ? ');
                                    $stat->execute([$orderID]);
                                    $result=$stat->fetch(PDO::FETCH_ASSOC);

                                    $sql = $con->prepare('SELECT NameAdd,emailAdd,phoneNumber,street, bultingNo, doorNo, poatalCode, cityName, provinceName 
                                                        FROM tbladdresse 
                                                        INNER JOIN tblcity ON tblcity.cityID = tbladdresse.cityID
                                                        INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
                                                        WHERE addresseId= ?');
                                    $sql->execute([$result['addresseId']]);
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
                                        
                                    } 
                                ?>
                            </div>
                        </div>
                        <div class="invoiceinfo">
                            <div class="no_pay">
                                <div class="invoice_no">
                                    <h4>Order ID</h4>
                                    <span># <?php echo  $orderID ?></span>
                                </div>
                                <div class="method">
                                    <?php 
                                        $sql=$con->prepare('SELECT paymentMethod FROM tblinvoice WHERE invoiceID = ? ');
                                        $sql->execute([$orderID]);
                                        $result_method = $sql->fetch(PDO::FETCH_ASSOC);
                                        $payment_method = $result_method['paymentMethod']
                                    ?>
                                    <h4>Payment Method:</h4>
                                    <span><?= $payment_method ?></span>
                                </div>
                            </div>
                            <div class="amount">
                                <?php
                                    $sql =$con->prepare('SELECT Amount,discount,tax,invoiceAmount FROM tblinvoice WHERE invoiceID = ?');
                                    $sql->execute([$orderID]);
                                    $amounts = $sql->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <table>
                                    <tr>
                                        <td class="lbltitle">Subtotal:</td>
                                        <td class="txttable"><?= number_format($amounts['Amount'],2)?> $</td>
                                    </tr>
                                    <tr>
                                        <td class="lbltitle">Discount</td>
                                        <td class="txttable"><?= number_format($amounts['discount'],2)?> $</td>
                                    </tr>
                                    <tr>
                                        <td class="lbltitle">Tax</td>
                                        <td class="txttable"><?= number_format($amounts['tax'],2)?> $</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <th style="text-align:right;color:var(--color-primary);font-size:18px;"><?= number_format($amounts['invoiceAmount'],2)?> $</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="progressbar">
                        <?php
                            $stmt = $con->prepare("SELECT statusID, statusName FROM tblstatus WHERE statusName != 'Cancelled' ORDER BY statusID ASC");
                            $stmt->execute();
                            $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);


                            $stmt = $con->prepare("SELECT invoiceStatus FROM tblinvoice WHERE invoiceID = ?");
                            $stmt->execute([$orderID]);
                            $currentStatus = $stmt->fetchColumn(); 
                        ?>
                        <?php foreach ($statuses as $status): ?>
                            <?php 
                            $id = $status['statusID'];
                            $name = $status['statusName'];
                            $isActive = ($currentStatus >= $id);
                            $isCompleted = ($currentStatus > $id); 
                            ?>
                            <div class="step <?php echo $isActive ? 'active' : ''; ?>">
                            <div class="circle">
                                <?php echo $isCompleted ? "✔" : str_pad($id, 2, "0", STR_PAD_LEFT); ?>
                            </div>
                            <div class="label"><?php echo htmlspecialchars($name); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="daitail_invoice">
                        <?php
                            $sql= $con->prepare('SELECT quantity,up,itmName,mainpic
                                                FROM tbldatailinvoice 
                                                INNER JOIN tblitems ON tblitems.itmId  =  tbldatailinvoice.itmID
                                                WHERE invoiceID = ?');
                            $sql->execute([$orderID]);
                            $items = $sql->fetchAll(PDO::FETCH_ASSOC);

                        ?>
                        <table>
                            <thead>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $itm): ?>
                                    <tr>
                                        <td>
                                            <img src="../images/items/<?= $itm['mainpic']?>" alt="" srcset="">
                                            <?= $itm['itmName']?>
                                        </td>
                                        <td><?= number_format($itm['up'],2)?> $</td>
                                        <td>x <?= $itm['quantity'] ?></td>
                                        <td><?= number_format($itm['up']*$itm['quantity'],2) ?> $</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }else{
                    header("Location: ../login.php");
                    exit(); 
                }
            ?>
            
        </div>
    </main>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/orderhistory.js"></script>
</body>