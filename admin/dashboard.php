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
</head>
<body>
    <?php include 'include/adminheader.php' ?>
    <main>
        <?php include 'include/adminaside.php'?>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
</body>