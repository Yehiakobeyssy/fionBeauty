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

    $do=isset($_GET['do'])?$_GET['do']:'manage'
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/manageWorkshops.css">
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
            <div class="btnadd">
                <a href="manageWorkshops.php?do=add" class="btn btn-success">New Workshop</a>
            </div>
            <?php
                if($do=='manage'){?>
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
                            // Assuming you already have $con as your PDO connection

                            // 1. Total Revenue
                            $stmt = $con->prepare("SELECT COALESCE(SUM(totalAmount), 0) AS totalRevenue FROM tblinvoiceworkshop ");
                            $stmt->execute();
                            $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['totalRevenue'];

                            // 2. All Workshops
                            $stmt = $con->prepare("SELECT COUNT(*) AS totalWorkshops FROM workshops");
                            $stmt->execute();
                            $totalWorkshops = $stmt->fetch(PDO::FETCH_ASSOC)['totalWorkshops'];

                            // 3. Upcoming Workshops
                            $stmt = $con->prepare("SELECT COUNT(*) AS upcomingWorkshops FROM workshops WHERE workshop_date >= CURDATE()");
                            $stmt->execute();
                            $upcomingWorkshops = $stmt->fetch(PDO::FETCH_ASSOC)['upcomingWorkshops'];

                            // 4. Total Client Bookings
                            $stmt = $con->prepare("SELECT COUNT(*) AS totalBookings FROM workshop_bookings");
                            $stmt->execute();
                            $totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['totalBookings'];

                            // 5. Upcoming Client Bookings
                            $stmt = $con->prepare("
                                SELECT COUNT(*) AS upcomingClientBookings 
                                FROM workshop_bookings wb
                                JOIN workshops w ON wb.workshop_id = w.id
                                WHERE w.workshop_date >= CURDATE()
                            ");
                            $stmt->execute();
                            $upcomingClientBookings = $stmt->fetch(PDO::FETCH_ASSOC)['upcomingClientBookings'];
                            ?>

                            <!-- 🎨 Dashboard HTML -->
                            <div class="totalbalance">
                                <label>Total Revenue</label>
                                <h5><?= number_format($totalRevenue, 2) ?> $</h5>
                            </div>

                            <div class="allstatistic">
                                <div class="cardstatistic">
                                    <label>All Workshops</label>
                                    <h5><?= $totalWorkshops ?></h5>
                                </div>
                                <div class="cardstatistic">
                                    <label>Upcoming Workshops</label>
                                    <h5><?= $upcomingWorkshops ?></h5>
                                </div>
                                <div class="cardstatistic">
                                    <label>Total Client Bookings</label>
                                    <h5><?= $totalBookings ?></h5>
                                </div>
                                <div class="cardstatistic">
                                    <label>Upcoming Client Bookings</label>
                                    <h5><?= $upcomingClientBookings ?></h5>
                                </div>
                            </div>

                    </div>
                    <div class="filter_section">
                        <div class="dates">
                            <input type="date" name="" id="txtdateofworkshop">
                        </div>
                        <div class="searchbar">
                            <input type="text" name="" id="txtSearchbar" placeholder="Search....">
                        </div>
                        <div class="paid">
                            <select name="" id="txtpaid">
                                <option value="">All</option>
                                <option value="0">Free</option>
                                <option value="1">Cost</option>
                            </select>
                        </div>
                    </div>
                    <div class="tbl">
                        <table>
                            <thead>
                                <th>Title</th>
                                <th>Date of Workshop</th>
                                <th>Hour</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Client Booked</th>
                                <th>Action  </th>
                            </thead>
                            <tbody id="fetchworkshop"></tbody>
                        </table>
                    </div>
                <?php
                }
            ?>
            
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageWorkshops.js"></script>
</body>