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
    <link rel="stylesheet" href="css/managePayment.css">
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
            <div class="statistic_cat">
                <div class="synpole">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <g clip-path="url(#clip0_2002_42513)">
                            <path d="M17.6752 13.2417C17.145 14.4955 16.3158 15.6002 15.2601 16.4595C14.2043 17.3187 12.9541 17.9063 11.6189 18.1707C10.2836 18.4352 8.90386 18.3685 7.6003 17.9766C6.29673 17.5846 5.10903 16.8793 4.14102 15.9223C3.17302 14.9653 2.45419 13.7857 2.04737 12.4867C1.64055 11.1877 1.55814 9.8088 1.80734 8.47059C2.05653 7.13238 2.62975 5.87559 3.47688 4.81009C4.324 3.74459 5.41924 2.90283 6.66684 2.3584" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.3333 10.0003C18.3333 8.90598 18.1178 7.82234 17.699 6.8113C17.2802 5.80025 16.6664 4.88159 15.8926 4.10777C15.1187 3.33395 14.2001 2.72012 13.189 2.30133C12.178 1.88254 11.0943 1.66699 10 1.66699V10.0003H18.3333Z" stroke="#009245" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_2002_42513">
                            <rect width="20" height="20" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <?php
                $sql = "
                    SELECT 
                        COALESCE(SUM(totalAmount), 0) AS totalRevenue,
                        COALESCE(COUNT(*), 0) AS allTransactions,
                        COALESCE(SUM(CASE WHEN source = 'invoice' THEN 1 ELSE 0 END), 0) AS totalOrders,
                        COALESCE(SUM(CASE WHEN source = 'workshop' THEN 1 ELSE 0 END), 0) AS totalWorkshops,
                        COALESCE(SUM(CASE WHEN statusID = 6 THEN 1 ELSE 0 END), 0) AS refunded
                    FROM (
                        SELECT 
                            invoiceID,
                            invoiceAmount AS totalAmount,
                            paymentMethod,
                            transactionNO AS transactionID,
                            invoiceStatus AS statusID,
                            'invoice' AS source
                        FROM tblinvoice

                        UNION ALL

                        SELECT 
                            invoiceID,
                            totalAmount,
                            method AS paymentMethod,
                            transactionID,
                            status AS statusID,
                            'workshop' AS source
                        FROM tblinvoiceworkshop
                    ) AS merged;

                ";

                $stmt = $con->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="totalbalance">
                    <label>Total Revenue</label>
                    <h5>$<?= number_format($data['totalRevenue'], 2) ?></h5>
                </div>

                <div class="allstatistic">
                    <div class="cardstatistic">
                        <label>All Transactions</label>
                        <h5><?= $data['allTransactions'] ?></h5>
                    </div>
                    <div class="cardstatistic">
                        <label>Orders</label>
                        <h5><?= $data['totalOrders'] ?></h5>
                    </div>
                    <div class="cardstatistic">
                        <label>Workshops</label>
                        <h5><?= $data['totalWorkshops'] ?></h5>
                    </div>
                    <div class="cardstatistic">
                        <label>Refunded</label>
                        <h5><?= $data['refunded'] ?></h5>
                    </div>
                </div>
            </div>
            <div class="filter_section">
                <div class="timeinterval">
                    <div class="times">
                        <span class="Activetime" data-time="999999" id="txtAll">All Time</span>
                        <span data-time="365" id="txtyear" >12 Months</span>
                        <span data-time="30" id="txtmonth">30 Days</span>
                        <span data-time="7" id="txtweek">7 Days</span>
                        <span data-time="1" id="txttoday">Today</span>
                    </div>
                </div>
                <div class="searchbar">
                    <input type="text" name="" id="txtSearchbar" placeholder="Search....">
                </div>
                <div class="dates">
                    <input type="date" name="" id="txtdateofinvoice">
                </div>
                
                <div class="paid">
                    <select name="" id="txtpaid">
                        <option value="">All</option>
                        <?php
                            $sql=$con->prepare('SELECT statusID ,statusName FROM tblstatus ');
                            $sql->execute();
                            $status = $sql->fetchAll();
                            foreach($status as $sta){
                                echo '<option value="'.$sta['statusID'].'">'.$sta['statusName'].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="tbl">
                <table>
                    <thead>
                        <th>Invoice Code</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>TransactionID</th>
                        <th>Payment Method</th>
                        <th>Status  </th>
                        <th>Action</th>
                    </thead>
                    <tbody id="fetchworkshop"></tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/managePayment.js"></script>
</body>