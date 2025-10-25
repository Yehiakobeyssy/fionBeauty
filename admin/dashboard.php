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
    <link rel="stylesheet" href="css/dashboard.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
            <div class="welcome_note">
                <div class="nameofAdmin">
                    <h3>Welcome , <span> <?= $admin_name ?></span></h3>
                    <label for="">Here is the information about all your orders</label>
                </div>
                <div class="timeinterval">
                    <div class="times">
                        <span class="Activetime" data-time="999999" id="txtAll">All Time</span>
                        <span data-time="365" id="txtyear" >12 Months</span>
                        <span data-time="30" id="txtmonth">30 Days</span>
                        <span data-time="7" id="txtweek">7 Days</span>
                        <span data-time="1" id="txttoday">Today</span>
                    </div>
                </div>
            </div>
            <div class="mainstatistic">
                <div class="card">
                    <div class="imges">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="16" viewBox="0 0 21 16" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5217 12C12.8269 12 14.6956 10.2091 14.6956 8C14.6956 5.79086 12.8269 4 10.5217 4C8.21651 4 6.34778 5.79086 6.34778 8C6.34778 10.2091 8.21651 12 10.5217 12ZM10.5217 10C11.6743 10 12.6087 9.10457 12.6087 8C12.6087 6.89543 11.6743 6 10.5217 6C9.3691 6 8.43474 6.89543 8.43474 8C8.43474 9.10457 9.3691 10 10.5217 10Z" fill="#07D776"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0869141 3C0.0869141 1.34315 1.48846 0 3.21735 0H17.826C19.5549 0 20.9565 1.34315 20.9565 3V13C20.9565 14.6569 19.5549 16 17.826 16H3.21735C1.48846 16 0.0869141 14.6569 0.0869141 13V3ZM16.9607 2H17.826C18.4023 2 18.8695 2.44771 18.8695 3V3.82929C17.98 3.52801 17.2751 2.85241 16.9607 2ZM14.8 2H6.24342C5.82844 3.95913 4.21818 5.5023 2.17387 5.89998V10.1C4.21818 10.4977 5.82844 12.0409 6.24342 14H14.8C15.2149 12.0409 16.8252 10.4977 18.8695 10.1V5.89998C16.8252 5.5023 15.2149 3.95913 14.8 2ZM18.8695 12.1707C17.98 12.472 17.2751 13.1476 16.9607 14H17.826C18.4023 14 18.8695 13.5523 18.8695 13V12.1707ZM4.0827 14C3.76832 13.1476 3.06335 12.472 2.17387 12.1707V13C2.17387 13.5523 2.64105 14 3.21735 14H4.0827ZM2.17387 3.82929C3.06335 3.52801 3.76831 2.85241 4.0827 2H3.21735C2.64105 2 2.17387 2.44772 2.17387 3V3.82929Z" fill="#07D776"/>
                        </svg>
                    </div>
                    <div class="info">
                        <label for="">Total Revenue</label>
                        <h5 id="lbltotalRevenue">0</h5>
                    </div>
                </div>
                <div class="card">
                    <div class="imges">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="19" viewBox="0 0 21 19" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.1304 4.5C13.1304 6.98528 11.0281 9 8.43474 9C5.8414 9 3.73909 6.98528 3.73909 4.5C3.73909 2.01472 5.8414 0 8.43474 0C11.0281 0 13.1304 2.01472 13.1304 4.5ZM11.0434 4.5C11.0434 5.88071 9.87548 7 8.43474 7C6.994 7 5.82604 5.88071 5.82604 4.5C5.82604 3.11929 6.994 2 8.43474 2C9.87548 2 11.0434 3.11929 11.0434 4.5Z" fill="#FFAD33"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0869141 16.9231C0.0869141 13.0996 3.32125 10 7.31099 10H9.55849C13.5482 10 16.7826 13.0996 16.7826 16.9231C16.7826 18.0701 15.8123 19 14.6153 19H2.25414C1.05721 19 0.0869141 18.0701 0.0869141 16.9231ZM2.17387 16.9231C2.17387 14.2041 4.47384 12 7.31099 12H9.55849C12.3956 12 14.6956 14.2041 14.6956 16.9231C14.6956 16.9656 14.6597 17 14.6153 17H2.25414C2.20981 17 2.17387 16.9656 2.17387 16.9231Z" fill="#FFAD33"/>
                            <path d="M17.7423 18.0973C17.6345 18.4981 17.9068 19 18.3387 19H18.7893C19.9862 19 20.9565 18.0701 20.9565 16.9231C20.9565 13.0996 17.7222 10 13.7324 10C13.584 10 13.5299 10.1975 13.6559 10.2727C14.6428 10.8614 15.4996 11.6314 16.1755 12.5343C16.2213 12.5955 16.2803 12.6466 16.348 12.685C17.8575 13.5424 18.8695 15.1195 18.8695 16.9231C18.8695 16.9656 18.8336 17 18.7893 17H18.295C18.0332 17 17.8261 17.2106 17.8261 17.4615C17.8261 17.6811 17.797 17.8941 17.7423 18.0973Z" fill="#FFAD33"/>
                            <path d="M13.5676 6.96308C13.625 6.85341 13.7195 6.76601 13.8325 6.70836C14.6564 6.28814 15.2173 5.45685 15.2173 4.5C15.2173 3.54315 14.6564 2.71186 13.8325 2.29164C13.7195 2.23399 13.625 2.14659 13.5676 2.03692C13.276 1.48001 12.8887 0.977121 12.4263 0.548096C12.2153 0.352311 12.3149 0 12.6086 0C15.202 0 17.3043 2.01472 17.3043 4.5C17.3043 6.98528 15.202 9 12.6086 9C12.3149 9 12.2153 8.64769 12.4263 8.4519C12.8887 8.02288 13.276 7.51999 13.5676 6.96308Z" fill="#FFAD33"/>
                        </svg>
                    </div>
                    <div class="info">
                        <label for="">Total Customers</label>
                        <h5 id="lbltotalcustomer">0</h5>
                    </div>
                </div>
                <div class="card">
                    <div class="imges">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24" fill="none">
                            <path d="M4.15027 9.84C4.19224 9.33881 4.42966 8.87115 4.81525 8.53017C5.20084 8.18918 5.70629 7.9999 6.23096 8H18.8132C19.3379 7.9999 19.8433 8.18918 20.2289 8.53017C20.6145 8.87115 20.8519 9.33881 20.8939 9.84L21.7318 19.84C21.7549 20.1152 21.7182 20.392 21.6242 20.6529C21.5301 20.9139 21.3807 21.1533 21.1854 21.3562C20.99 21.5592 20.7529 21.7211 20.4891 21.8319C20.2252 21.9427 19.9403 21.9999 19.6522 22H5.392C5.10392 21.9999 4.81898 21.9427 4.55511 21.8319C4.29125 21.7211 4.05417 21.5592 3.85882 21.3562C3.66346 21.1533 3.51406 20.9139 3.42001 20.6529C3.32596 20.392 3.28931 20.1152 3.31235 19.84L4.15027 9.84V9.84Z" stroke="#1075F6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.6956 11V6C16.6956 4.93913 16.2559 3.92172 15.4731 3.17157C14.6903 2.42143 13.6287 2 12.5217 2C11.4147 2 10.353 2.42143 9.57029 3.17157C8.78753 3.92172 8.34778 4.93913 8.34778 6V11" stroke="#1075F6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="info">
                        <label for="">Total Commtion</label>
                        <h5 id="totalComtion">0</h5>
                    </div>
                </div>
                <div class="bgCard card">
                    <div class="imges">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="20" viewBox="0 0 22 20" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.4049 2.12231C5.21593 1.18063 4.35612 0.5 3.35549 0.5H1.60872C1.03243 0.5 0.565247 0.947715 0.565247 1.5C0.565247 2.05228 1.03243 2.5 1.60872 2.5L3.35549 2.5L5.31309 12.2554C5.69103 14.1388 7.41065 15.5 9.4119 15.5H15.8912C17.7872 15.5 19.4451 14.2752 19.9276 12.518L21.2332 7.76348C21.7556 5.86122 20.2584 4 18.2059 4H5.78169L5.4049 2.12231ZM6.18303 6L7.3625 11.8777C7.55147 12.8194 8.41128 13.5 9.4119 13.5H15.8912C16.8392 13.5 17.6681 12.8876 17.9094 12.009L19.215 7.25449C19.3891 6.62041 18.89 6 18.2059 6H6.18303Z" fill="#E01212"/>
                            <path d="M7.60869 19.5C6.74425 19.5 6.04348 18.8284 6.04348 18C6.04348 17.1716 6.74425 16.5 7.60869 16.5C8.47314 16.5 9.17391 17.1716 9.17391 18C9.17391 18.8284 8.47314 19.5 7.60869 19.5Z" fill="#E01212"/>
                            <path d="M16.7391 19.5C15.8747 19.5 15.1739 18.8284 15.1739 18C15.1739 17.1716 15.8747 16.5 16.7391 16.5C17.6036 16.5 18.3043 17.1716 18.3043 18C18.3043 18.8284 17.6036 19.5 16.7391 19.5Z" fill="#E01212"/>
                        </svg>
                    </div>
                    <div class="order_numbers">
                        <div class="numberorder">
                            <label for="">All Orders</label>
                            <h5 id="lblallorders">0</h5>
                        </div>
                        <div class="numberorder">
                            <label for="">Processing</label>
                            <h5 id="lblProcessing">0</h5>
                        </div>
                        <div class="numberorder">
                            <label for="">Completed</label>
                            <h5 id="lblComplte">0</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overview">
                <div class="titleoverview">
                    <h4>Overview</h4>
                </div>
                <div class="overviewstatistic">
                    <div id="chart_div" style=" height: 500px;"></div>
                    <div id="piechart_3d" style=" height: 500px;"></div>
                </div>
            </div>
            <div class="reoprts">
                <div class="report-card">
                    <div class="title_rep">
                        <h4>Reports</h4>
                        <p id="lblRangeTitle">Last 9999 Days</p>
                    </div>
                    
                    
                    <div class="stats">
                        <div class="stat-card active_card" data-type="customers">
                            <h5 id="lblCustomers">0</h5><span>Customers</span>
                        </div>
                        <div class="stat-card" data-type="totalProducts">
                            <h5 id="lblItems">0</h5><span>Total Products</span>
                        </div>
                        <div class="stat-card" data-type="inStock">
                            <h5 id="lblStock">0</h5><span>Stock Items</span>
                        </div>
                        <div class="stat-card" data-type="outStock">
                            <h5 id="lblOutStock">0</h5><span>Out of Stock</span>
                        </div>
                        <div class="stat-card" data-type="revenue">
                            <h5 id="lblRevenue">0</h5><span>Revenue</span>
                        </div>
                    </div>

                    <!-- Chart for details -->
                    <div id="detailChartContainer" style="margin-top: 20px;">
                        <h4 id="detailTitle" style="text-align:center;"></h4>
                        <div id="detailChart" style="height:300px;"></div>
                    </div>
                </div>
                <div class="recentOrders">
                    <div class="title">
                        <h5>Recent Order</h5>
                        <a href="manageOrders.php">View All -></a>
                    </div>
                    <div class="container_orders">
                        <?php
                            $sql=$con->prepare("SELECT 
                                                    i.invoiceID,
                                                    DATE_FORMAT(i.invoiceDate, '%d %b %Y') AS formattedDate,
                                                    CONCAT(c.clientFname, ' ', c.clientLname) AS clientName,
                                                    s.statusName,
                                                    
                                                    -- First item info
                                                    fi.itmName AS firstItemName,
                                                    fi.mainpic AS firstItemPic,
                                                    
                                                    -- Count of remaining items
                                                    (COUNT(di.itmID) - 1) AS remainingItemsCount

                                                FROM tblinvoice i
                                                JOIN tblclient c ON i.clientID = c.clientID
                                                JOIN tblstatus s ON i.invoiceStatus = s.statusID
                                                JOIN tbldatailinvoice di ON i.invoiceID = di.invoiceID
                                                JOIN tblitems fi ON fi.itmID = (
                                                    SELECT di2.itmID
                                                    FROM tbldatailinvoice di2
                                                    WHERE di2.invoiceID = i.invoiceID
                                                    ORDER BY di2.daitailInvoiceId ASC
                                                    LIMIT 1
                                                )

                                                WHERE i.invoiceStatus IS NOT NULL

                                                GROUP BY i.invoiceID
                                                ORDER BY i.invoiceDate DESC
                                                LIMIT 5;
                                                ");
                            $sql->execute();
                            $orders = $sql->fetchAll();
                            foreach($orders as $order){
                                $statusClass = '';
                                switch ($order['statusName']) {
                                    case 'Order received':
                                        $statusClass = 'alert alert-info p-1 m-0';
                                        break;
                                    case 'Processing':
                                        $statusClass = 'alert alert-light p-1 m-0';
                                        break;
                                    case 'On the way':
                                        $statusClass = 'alert alert-primary p-1 m-0';
                                        break;
                                    case 'Delivered':
                                        $statusClass = 'alert alert-success p-1 m-0';
                                        break;
                                    case 'Cancelled':
                                        $statusClass = 'alert alert-danger p-1 m-0';
                                        break;
                                    case 'Full Refund':
                                        $statusClass = 'alert alert-warning p-1 m-0';
                                        break;
                                    default:
                                        $statusClass = 'alert alert-secondary p-1 m-0';
                                }

                                echo '
                                     <div class="order_card" data-index="'.$order['invoiceID'].'">
                                        <div class="itemimg">
                                            <img src="../images/items/'.$order['firstItemPic'].'" alt="" srcset="">
                                        </div>
                                        <div class="orderdaitail">
                                            <div class="itemnameanddate">
                                                <h6>'.$order['firstItemName'].'</h6>
                                                <span>'.$order['formattedDate'].'</span>
                                            </div>
                                            <div class="numberofitems">
                                                <label for="">+ '.$order['remainingItemsCount'].' items</label>
                                            </div>
                                            <div class="general">
                                                <h6>Ordered By <span>'.$order['clientName'].'</span></h6>
                                                <label for="" class="'.$statusClass.'">'.$order['statusName'].'</label>
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }

                        ?>
                    </div>
                </div>
            </div>
            <div class="top">
                <div class="topProducts">
                    <div class="title">
                        <h5>Top Selling Product</h5>
                        <a href="manageproducts.php">View All</a>
                    </div>
                    <table>
                        <thead>
                            <th>Product</th>
                            <th>Orders</th>
                            <th>Amount</th>
                            <th>Price</th>
                        </thead>
                        <tbody>
                            <?php
                                $sql= $con->prepare('SELECT 
                                                        i.itmID,
                                                        i.mainpic AS mainImage,
                                                        i.itmName,
                                                        c.catName AS categoryName,
                                                        i.sellPrice,
                                                        SUM(d.quantity) AS totalQuantity,
                                                        SUM(d.up * d.quantity) AS totalRevenue

                                                    FROM tbldatailinvoice d
                                                    JOIN tblitems i ON d.itmID = i.itmID
                                                    JOIN tblcategory c ON i.catId = c.categoryId

                                                    WHERE d.status = 1

                                                    GROUP BY i.itmID
                                                    ORDER BY totalQuantity DESC
                                                    LIMIT 5;
                                                    ');
                                $sql->execute();
                                $topitems = $sql->fetchAll();
                                foreach ($topitems as $top){
                                    echo '
                                        <tr>
                                            <td>
                                                <div class="itemdis">
                                                    <div class="itmimg">
                                                        <img src="../images/items/'.$top['mainImage'].'">
                                                    </div>
                                                    <div class="iteminfo">
                                                        <h6>'.$top['itmName'].'</h6>
                                                        <span>'.$top['categoryName'].'</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>'.$top['totalQuantity'].'</td>
                                            <td>'.number_format($top['totalRevenue'],2).'</td>
                                            <td>'.number_format($top['sellPrice'],2).'</td>
                                        </tr>
                                    ';
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="topClients">
                    <div class="title">
                        <h6>Active Customers</h6>
                        <a href="manageClients.php">View All</a>
                    </div>
                    <table>
                        <thead>
                            <th>Customer</th>
                            <th>Orders</th>
                            <th>Amount</th>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "
                                        SELECT 
                                            c.clientID,
                                            c.clientFname,
                                            c.clientLname,
                                            c.clientEmail,
                                            COUNT(i.invoiceID) AS totalOrders,
                                            COALESCE(SUM(i.invoiceAmount), 0) AS totalAmount
                                        FROM tblclient c
                                        LEFT JOIN tblinvoice i ON c.clientID = i.clientID
                                        WHERE invoiceStatus < 5
                                        GROUP BY c.clientID
                                        ORDER BY totalAmount DESC
                                        LIMIT 5
                                        ";

                                $stmt = $con->prepare($sql);
                                $stmt->execute();
                                $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Colors for avatars (Gmail style)
                                $colors = ['#e57373','#64b5f6','#81c784','#ba68c8','#ffb74d','#4db6ac','#7986cb'];
                            

                            $i = 0;
                            foreach ($clients as $c):
                                $initials = strtoupper(substr($c['clientFname'], 0, 1) . substr($c['clientLname'], 0, 1));
                                $color = $colors[$i % count($colors)];
                            ?>
                            <tr>
                                <td>
                                    <div class="client-info">
                                        <div class="avatar" style="background-color: <?= $color ?>;">
                                            <?= $initials ?>
                                        </div>
                                        <div class="client-details">
                                            <span class="name"><?= htmlspecialchars($c['clientFname'] . ' ' . $c['clientLname']) ?></span>
                                            <span class="email"><?= htmlspecialchars($c['clientEmail']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= (int)$c['totalOrders'] ?></td>
                                <td>$<?= number_format((float)$c['totalAmount'], 2) ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
</body>