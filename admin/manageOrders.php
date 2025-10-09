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
                                <th>Trasaction NO:</th>
                                <th>Status</th>
                            </thead>
                            <tbody id="tblorders">
                                
                            </tbody>
                        </table>                    
                    </div>
                <?php
                }elseif($do == 'order'){

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