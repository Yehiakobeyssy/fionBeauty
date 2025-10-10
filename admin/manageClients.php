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
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageClients.js"></script>
</body>