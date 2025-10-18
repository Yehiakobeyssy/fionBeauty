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
                }elseif($do== 'add'){

                }elseif($do == 'edid'){

                }elseif($do == 'block'){

                }elseif($do == 'delete'){

                }else{
                    echo '<script> location.href = "manageAdmins.php" </script>';
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageAdmins.js"></script>
</body>