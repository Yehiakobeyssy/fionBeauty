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
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
        include 'include/catecorysname.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ user's Account/ <strong>Dashboard</strong></h5>
        </div>
        <div class="catecoryname">
            <h2>Dashboard</h2>
        </div>      
        <div class="desgin">

        </div>
    </div>
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
        <?php include 'include/aside.php'?>
        <div class="sections_side">
            <div class="info_add">
                <div class="info">
                    <div class="design" style="background-color:<?= $bgColor ?>">
                        <?= $initials ?>
                    </div>
                    <h4><?= $fullname ?></h4>
                    <label for="">Client</label>
                    <button>Edit Profile</button>
                </div>
                <div class="main_add">
                    <div class="title_addresse">
                        <h4>Default Address</h4>
                        <button>View All -></button>
                    </div>
                    <div class="add">
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
                                echo "<div class='settingadd'><a href=''>Edid Address</a></div>";
                            } else {
                                // Show danger alert
                                echo '<div class="alert alert-danger">You haven\'t an address</div>';
                            }
                        ?>
                    </div>
                    
                </div>
            </div>
            <div class="order_history">
                <div class="table_title">
                    <h3>Recent Order History</h3>
                    <button>View All</button>
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
                    <tbody>
                        <?php
                            $sql = $con->prepare('SELECT invoiceID, invoiceCode, invoiceDate, statusName
                                                FROM tblinvoice 
                                                INNER JOIN tblstatus ON tblstatus.statusID = tblinvoice.invoiceStatus
                                                WHERE clientID = ?
                                                ORDER BY invoiceID DESC
                                                LIMIT 5');
                            $sql->execute([$user_id]);
                            $orders = $sql->fetchAll();

                            foreach($orders as $order){
                                $stat = $con->prepare('SELECT COUNT(daitailInvoiceId) AS items, SUM(quantity*up) AS total 
                                                        FROM tbldatailinvoice 
                                                        WHERE invoiceID = ?');
                                $stat->execute([$order['invoiceID']]);
                                $result = $stat->fetch();

                                $count_item = $result['items'];
                                $total = number_format($result['total'], 2);

                                // صيغة التاريخ
                                $formattedDate = date("j F, Y", strtotime($order['invoiceDate']));

                                // حالة الطلب
                                $statusClass = '';
                                switch ($order['statusName']) {
                                    case 'Pending':
                                        $statusClass = 'alert alert-info p-1 m-0';
                                        break;
                                    case 'Paid':
                                        $statusClass = 'alert alert-light p-1 m-0';
                                        break;
                                    case 'Shipped':
                                        $statusClass = 'alert alert-primary p-1 m-0';
                                        break;
                                    case 'Delivered':
                                        $statusClass = 'alert alert-success p-1 m-0';
                                        break;
                                    case 'Cancelled':
                                        $statusClass = 'alert alert-danger p-1 m-0';
                                        break;
                                    default:
                                        $statusClass = 'alert alert-secondary p-1 m-0';
                                }

                                echo "
                                    <tr>
                                        <td>{$order['invoiceCode']}</td>
                                        <td>{$formattedDate}</td>
                                        <td><strong>\${$total}</strong> ({$count_item} items)</td>
                                        <td><div class='{$statusClass}'>{$order['statusName']}</div></td>
                                        <td><a href='order_details.php?id={$order['invoiceID']}'>View -></a></td>
                                    </tr>
                                ";
                            }


                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
</body>