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
    <link rel="stylesheet" href="css/manageOrders.css">
    
</head>
<body> 
    <?php include 'include/adminheader.php' ?>
    <main>
        <?php include 'include/adminaside.php'?>
        <div class="container_info">
            <?php 
                if($do == 'manage'){?>
                    <div class="mainstatistic">
                        <div class="title">
                            <h5>Order Summery</h5>
                        </div>
                        <div class="card">
                            <div class="logo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.9049 2.12231C4.71593 1.18063 3.85612 0.5 2.85549 0.5H1.10872C0.532428 0.5 0.0652466 0.947715 0.0652466 1.5C0.0652466 2.05228 0.532428 2.5 1.10872 2.5L2.85549 2.5L4.81309 12.2554C5.19103 14.1388 6.91065 15.5 8.9119 15.5H15.3912C17.2872 15.5 18.9451 14.2752 19.4276 12.518L20.7332 7.76348C21.2556 5.86122 19.7584 4 17.7059 4H5.28169L4.9049 2.12231ZM5.68303 6L6.8625 11.8777C7.05147 12.8194 7.91128 13.5 8.9119 13.5H15.3912C16.3392 13.5 17.1681 12.8876 17.4094 12.009L18.715 7.25449C18.8891 6.62041 18.39 6 17.7059 6H5.68303Z" fill="#009245"/>
                                    <path d="M7.10869 19.5C6.24425 19.5 5.54348 18.8284 5.54348 18C5.54348 17.1716 6.24425 16.5 7.10869 16.5C7.97314 16.5 8.67391 17.1716 8.67391 18C8.67391 18.8284 7.97314 19.5 7.10869 19.5Z" fill="#009245"/>
                                    <path d="M16.2391 19.5C15.3747 19.5 14.6739 18.8284 14.6739 18C14.6739 17.1716 15.3747 16.5 16.2391 16.5C17.1036 16.5 17.8043 17.1716 17.8043 18C17.8043 18.8284 17.1036 19.5 16.2391 19.5Z" fill="#009245"/>
                                </svg>
                                <?php
                                    $sql = "
                                    SELECT 
                                        COUNT(*) AS allOrders,
                                        SUM(CASE WHEN invoiceStatus	 BETWEEN 1 AND 3 THEN 1 ELSE 0 END) AS processing,
                                        SUM(CASE WHEN invoiceStatus	 = 4 THEN 1 ELSE 0 END) AS completed,
                                        SUM(CASE WHEN invoiceStatus	 IN (5,6) THEN 1 ELSE 0 END) AS cancelled
                                    FROM tblinvoice
                                    ";
                                    $stmt = $con->prepare($sql);
                                    $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>

                                <div class="numbers">
                                    <div class="card_num">
                                        <label>All Orders</label>
                                        <span><?= (int)$row['allOrders'] ?></span>
                                    </div>
                                    <div class="card_num">
                                        <label>Processing</label>
                                        <span><?= (int)$row['processing'] ?></span>
                                    </div>
                                    <div class="card_num">
                                        <label>Completed</label>
                                        <span><?= (int)$row['completed'] ?></span>
                                    </div>
                                    <div class="card_num">
                                        <label>Cancelled</label>
                                        <span><?= (int)$row['cancelled'] ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="filter-section">
                            <div class="filter-row">

                                <!-- Time Interval -->
                                <div class="filter-group">
                                    <label>Time Interval:</label>
                                    <div class="interval-buttons">
                                        <button class="active" value="9999">All Time</button>
                                        <button value="365">12 Months</button>
                                        <button value="30">30 Days</button>
                                        <button value="7">7 Days</button>
                                        <button value="1">Today</button>
                                    </div>
                                </div>

                                <!-- Search -->
                                <div class="filter-group">
                                    <label>Search:</label>
                                    <input type="text" placeholder="Search orders..." class="search-input">
                                </div>

                                <!-- Selected Day -->
                                <div class="filter-group">
                                    <label>Select Day:</label>
                                    <input type="date" class="date-input">
                                </div>

                                <!-- Status Filter -->
                                <div class="filter-group">
                                    <label>Status:</label>
                                    <select class="status-select">
                                        <option value="">All Status</option>
                                        <?php
                                        $stmt = $con->prepare("SELECT statusID, statusName FROM tblstatus");
                                        $stmt->execute();
                                        while ($s = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="'.$s['statusID'].'">'.$s['statusName'].'</option>';
                                        }
                                        ?>
                                    </select>

                                </div>

                            </div>
                        </div>
                        <table>
                            <thead>
                                <th>Order ID</th>
                                <th>Client Name</th>
                                <th>Date</th>
                                <th>Produts</th>
                                <th>Total</th>
                                <th>Commition</th>
                                <th>Trasaction NO:</th>
                                <th>Status</th>
                            </thead>
                            <tbody id="tblorders">
                                
                            </tbody>
                        </table>                    
                    </div>
                <?php
                }elseif($do == 'order'){
                    
                        $orderID = isset($_GET['orderID'])?$_GET['orderID']:0;
                        $check_order= checkItem('invoiceID','tblinvoice',$orderID);

                        if($check_order == 0){
                            header("Location: dashboard.php");
                            exit(); 
                        }else{
                            $sql=$con->prepare('SELECT invoiceDate,invoiceCode,invoiceStatus	 FROM tblinvoice WHERE invoiceID  = ? ');
                            $sql->execute([$orderID]);
                            $result=$sql->fetch();
                            $dateInvoice = $result['invoiceDate'];
                            $formatdate =  date("j F, Y", strtotime($dateInvoice));
                            $invoiceCode = $result['invoiceCode'];
                            $invStat = $result['invoiceStatus'];

                            $sql= $con->prepare('SELECT COUNT(daitailInvoiceId) AS items FROM tbldatailinvoice WHERE invoiceID = ?');
                            $sql->execute([$orderID]);
                            $result= $sql->fetch();
                            $items = $result['items'];

                        }
                    ?>
                    <div class="title_section">
                        <h3>Order Details</h3>
                        <span><?= $formatdate .' - ' . '('. $items .'Products )'?></span>
                        <div class="status">
                            <label for="">Current Status :</label>
                            <select name="" id="updateinvoice" data-index="<?=$orderID?>" <?= ($invStat == 6) ? 'disabled' : '' ?>>
                                <?php
                                    $sql=$con->prepare('SELECT statusID ,statusName FROM  tblstatus ');
                                    $sql->execute();
                                    $statuses = $sql->fetchAll();
                                    foreach ($statuses as $sta){
                                        if($sta['statusID'] ==  $invStat ){
                                            echo '<option value="'.$sta['statusID'] .'" selected>'.$sta['statusName'] .'</option>';
                                        }else{
                                            echo '<option value="'.$sta['statusID'] .'" >'.$sta['statusName'] .'</option>';
                                        }
                                        
                                    }
                                ?>
                            </select>
                        </div>
                        <a href="manageOrders.php">Back to List</a>
                    </div>
                    <div class="invoice_info">
                        <div class="addreese">
                            <div class="main_add">
                                <div class="title_main_add">
                                    <h4>Client info</h4>
                                </div>
                                <?php
                                    $stat=$con->prepare('SELECT clientID FROM  tblinvoice WHERE invoiceID  = ? ');
                                    $stat->execute([$orderID]);
                                    $result=$stat->fetch();
                                    $sql= $con->prepare('SELECT clientFname,clientLname,clientPhoneNumber,clientEmail FROM  tblclient WHERE clientID = ?');
                                    $sql->execute([$result['clientID']]);
                                    $client = $sql->fetch();

                                    echo '
                                        <h5>'.$client['clientFname'].' '.$client['clientLname'].'</h5>
                                        <label>'.$client['clientPhoneNumber'].'</label><br>
                                        <label>'.$client['clientEmail'].'</label>
                                    ';
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
                                    <span># <?php echo   $invoiceCode ?></span>
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
                                    <?php
                                        $sql = "
                                                SELECT 
                                                    SUM(
                                                        (d.quantity * d.up - (d.quantity * d.up * d.discount / 100)) * (t.commtion / 100)
                                                    ) AS totalCommition
                                                FROM tbldatailinvoice d
                                                INNER JOIN tblitems t ON d.itmID = t.itmID
                                                WHERE d.invoiceID = :invoiceID
                                                ";

                                        $stmt = $con->prepare($sql);
                                        $stmt->bindValue(':invoiceID', $orderID, PDO::PARAM_INT);
                                        $stmt->execute();
                                        $totalComm = $stmt->fetchColumn();
                                    ?>
                                    <tr>
                                        <th>Commission</th>
                                        <th style="text-align:right;color:var(--color-primary);font-size:18px;"><?= number_format($totalComm,2)?> $</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="progressbar">
                        <?php
                            // Fetch all statuses except Cancelled and Full Refund
                            $stmt = $con->prepare("SELECT statusID, statusName 
                                                FROM tblstatus 
                                                WHERE statusName NOT IN ('Cancelled', 'Full Refund') 
                                                ORDER BY statusID ASC");
                            $stmt->execute();
                            $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Get the current invoice status
                            $stmt = $con->prepare("SELECT invoiceStatus FROM tblinvoice WHERE invoiceID = ?");
                            $stmt->execute([$orderID]);
                            $currentStatus = $stmt->fetchColumn(); 
                            ?>

                            <?php
                            // ✅ Show alert if order is Cancelled or Full Refund
                            if ($currentStatus === 5) {
                                echo '<div class="alert alert-danger p-2 m-0 text-center" style="width:100%">❌ The order has been cancelled.</div>';
                            } elseif ($currentStatus === 6) {
                                echo '<div class="alert alert-warning p-2 m-0 text-center" style="width:100%">💰 A full refund has been issued for this order.</div>';
                            } else {
                                // ✅ Otherwise show the step progress
                                foreach ($statuses as $status):
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
                                <?php endforeach;
                            }
                        ?>

                    </div>
                    <div class="daitail_invoice">
                        <?php
                            $sql= $con->prepare('SELECT daitailInvoiceId,quantity,up,itmName,mainpic,tblitemstatus.Status AS st
                                                FROM tbldatailinvoice 
                                                INNER JOIN tblitems ON tblitems.itmId  =  tbldatailinvoice.itmID
                                                INNER JOIN tblitemstatus ON tblitemstatus.StatusID  =  tbldatailinvoice.status
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
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $itm): 
                                    if($itm['st'] == 'On Stock'){
                                        $style = 'alert alert-success p-1 m-0';
                                    }elseif($itm['st']== 'Out ouf Stock'){
                                        $style = 'alert alert-danger p-1 m-0';
                                    }elseif($itm['st'] == 'Refound'){
                                        $style = 'alert alert-warning p-1 m-0';
                                    }
                                ?>
                                    
                                    <tr>
                                        <td>
                                            <img src="../images/items/<?= $itm['mainpic']?>" alt="" srcset="">
                                            <?= $itm['itmName']?>
                                        </td>
                                        <td><?= number_format($itm['up'],2)?> $</td>
                                        <td>x <?= $itm['quantity'] ?></td>
                                        <td><?= number_format($itm['up']*$itm['quantity'],2) ?> $</td>
                                        <td><span class="<?= $style ?>"><?= $itm['st']?></span></td>
                                        <td>
                                            <?php if ($itm['st'] != 'Refound'): ?>
                                            <button class="btn_status" data-invoice="<?= $itm['daitailInvoiceId'] ?>" data-status="1" title="In stock">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <circle cx="12" cy="12" r="11.25" stroke="#16A34A" stroke-width="1.5"/>
                                                    <path d="M8 12l3 3 5-5" stroke="#16A34A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                            <button class="btn_status" data-invoice="<?= $itm['daitailInvoiceId'] ?>" data-status="2" title="Out of stock">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <g clip-path="url(#clip0)">
                                                    <path d="M8 16L12 12M16 8L12 12M12 12L8 8M12 12L16 16" stroke="#E01212" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="12" r="11.25" stroke="#E01212" stroke-width="1.5"/>
                                                    </g>
                                                    <defs>
                                                    <clipPath id="clip0"><rect width="24" height="24" fill="white"/></clipPath>
                                                    </defs>
                                                </svg>
                                            </button>

                                            <button class="btn_status" data-invoice="<?= $itm['daitailInvoiceId'] ?>" data-status="3" title="Refund">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <circle cx="12" cy="12" r="11.25" stroke="#F59E0B" stroke-width="1.5"/>
                                                <path d="M12 8v4l3 3" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            </button>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }else{
                    header("Location: index.php");
                    exit(); 
                }
            ?>
            
            
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageOrders.js"></script>
</body>