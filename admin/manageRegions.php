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
    <link rel="stylesheet" href="css/manageRegions.css">
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
            <div class="region-header">
                <h2>Manage Regions</h2>
                <div class="search-box">
                    <input type="text" id="searchRegion" placeholder="Search province or city..." />
                    <button class="btn-search">
                    <i class="fa fa-search"></i>
                    </button>
                </div>
                <button class="btn btn-primary" id="addProvinceBtn">+ Add Province</button>
            </div>
            <div id="provinceList"></div>
        </div>
    </main> 
    <div id="popupModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalTitle">Title</h3>
            <input type="text" id="modalInput" placeholder="Enter name..." />
            <input type="number" step="0.01" id="modalShippingFee" placeholder="Enter shipping fee..." />
            <button id="modalSaveBtn" class="btn btn-primary">Save</button>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageRegions.js"></script>
</body> 