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
                            $sql=$con->prepare('SELECT invoiceDate,invoiceCode FROM tblinvoice WHERE invoiceID  = ? ');
                            $sql->execute([$orderID]);
                            $result=$sql->fetch();
                            $dateInvoice = $result['invoiceDate'];
                            $formatdate =  date("j F, Y", strtotime($dateInvoice));
                            $invoiceCode = $result['invoiceCode'];

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
                                    <span># <?php echo  $invoiceCode ?></span>
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
                                    $sql =$con->prepare('SELECT Amount,discount,tax,invoiceAmount,shippfee FROM tblinvoice WHERE invoiceID = ?');
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
                                        <td class="lbltitle">Shipping Fee</td>
                                        <td class="txttable"><?= number_format($amounts['shippfee'],2)?> $</td>
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
                            $sql= $con->prepare('SELECT quantity,up,itmName,mainpic,tblitemstatus.Status  AS st,tbldatailinvoice.itmID
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
                                <th></th>
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
                                            <?php if($currentStatus >= 3): // only show review button if status > 3 ?>
                                                <button title="to Review" class="btn_toreview" data-index="<?=$itm['itmID']?>"> 
                                                    <svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.5" d="M22 10.5V12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2H13.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                                                        <path d="M17.3009 2.80624L16.652 3.45506L10.6872 9.41993C10.2832 9.82394 10.0812 10.0259 9.90743 10.2487C9.70249 10.5114 9.52679 10.7957 9.38344 11.0965C9.26191 11.3515 9.17157 11.6225 8.99089 12.1646L8.41242 13.9L8.03811 15.0229C7.9492 15.2897 8.01862 15.5837 8.21744 15.7826C8.41626 15.9814 8.71035 16.0508 8.97709 15.9619L10.1 15.5876L11.8354 15.0091C12.3775 14.8284 12.6485 14.7381 12.9035 14.6166C13.2043 14.4732 13.4886 14.2975 13.7513 14.0926C13.9741 13.9188 14.1761 13.7168 14.5801 13.3128L20.5449 7.34795L21.1938 6.69914C22.2687 5.62415 22.2687 3.88124 21.1938 2.80624C20.1188 1.73125 18.3759 1.73125 17.3009 2.80624Z" stroke="#1C274C" stroke-width="1.5"/>
                                                        <path opacity="0.5" d="M16.6522 3.45508C16.6522 3.45508 16.7333 4.83381 17.9499 6.05034C19.1664 7.26687 20.5451 7.34797 20.5451 7.34797M10.1002 15.5876L8.4126 13.9" stroke="#1C274C" stroke-width="1.5"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        </td>
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
    <div id="reviewPopup" style="display:none;">
        <div class="popup-overlay"></div>
        <div class="popup-content">
            <h3>Leave a Review</h3>
            <form id="reviewForm">
                <input type="hidden" name="itemID" id="itemID">
                <input type="hidden" name="rateScore" id="rateScore" value="0">
                
                <div class="form-group">
                    <label for="rateScore">Rating:</label>
                    <div class="star-rating">
                        <i class="fa-regular fa-star" data-value="1"></i>
                        <i class="fa-regular fa-star" data-value="2"></i>
                        <i class="fa-regular fa-star" data-value="3"></i>
                        <i class="fa-regular fa-star" data-value="4"></i>
                        <i class="fa-regular fa-star" data-value="5"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="commentClient">Comment:</label>
                    <textarea name="commentClient" id="commentClient" rows="4" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-inboder">Submit</button>
                    <button type="button" id="closeReview" class="btn btn-outboder">Cancel</button>
                </div> 
            </form>
            <div id="reviewMessage" style="margin-top:10px;"></div>
        </div>
    </div>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/orderhistory.js"></script>
</body>