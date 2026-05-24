<?php
    session_start();
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];  
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = (int) $_COOKIE['user_id'];  
    } else {
        $user_id = 0; // if neither session nor cookie exist
    };


    $stat = $con->prepare('SELECT COUNT(*) AS totalitem FROM  tblitems WHERE itmActive = 1');
    $stat->execute();
    $result = $stat->fetch(PDO::FETCH_ASSOC);
    if ($result['totalitem'] < 6) {
        header('Location: commingsoon.php');
        exit(); // Always call exit after redirect
    }

    $getProgram = $_GET['proId'] ?? null;
    $checkProgram = checkItem('ProgramID'  ,'tblprogramm', $getProgram);
    if (!$checkProgram) {
        header('Location: program.php.php');
        exit(); // Always call exit after redirect
    }
    $getProgram = $_GET['proId'] ?? 0;

    $stmt = $con->prepare("
        SELECT ProgramID, ProgramName, ProgramLogo, ProgramDiscription, ProgramSlideshow
        FROM tblprogramm
        WHERE ProgramID = ? AND ProgramActive = 1
        LIMIT 1
    ");

    $stmt->execute([$getProgram]);

    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtItems = $con->prepare("
        SELECT 
            i.itmId,
            i.itmName,
            i.itmDesc,
            i.ingredients,
            i.mainpic
        FROM tblitemprogram ip
        INNER JOIN tblitems i ON ip.ItemID = i.itmId
        WHERE ip.ProgramID = ?
    ");

    $stmtItems->execute([$getProgram]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/specialprogram.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php';
    ?>
    <div class="program_top">

        <div class="program_slideshow">
            <img src="images/programs/<?php echo $program['ProgramSlideshow']; ?>" alt="">
        </div>

        <h1 class="program_title">
            <?php echo $program['ProgramName']; ?>
        </h1>

        <p class="program_desc">
            <?php echo $program['ProgramDiscription']; ?>
            
        </p>

    </div>
    <?php 
        $sections = $con->prepare("SELECT * FROM tblprogramsection WHERE ProgramID = ?");
        $sections->execute([$getProgram]);

        $i = 0;
        while($sec = $sections->fetch(PDO::FETCH_ASSOC)){
            $reverse = ($i % 2 == 1) ? 'reverse' : '';
        ?>
            
        <div class="program_section <?php echo $reverse; ?>">

            <!-- TEXT -->
            <div class="section_text">
                <h2><?php echo $sec['SectionTitle']; ?></h2>
                <p><?php echo $sec['SectionDiscription']; ?></p>
            </div>

            <!-- IMAGE -->
            <div class="section_img">
                <img src="images/programs/<?php echo $sec['SectionPhoto']; ?>">
            </div>

        </div>

    <?php $i++; } ?>

    <?php foreach($items as $item){ ?>

    <div class="item_section">

        <!-- LEFT TEXT -->
        <div class="item_text">

            <h2><?php echo $item['itmName']; ?></h2>

            <p><?php echo $item['itmDesc']; ?></p>

            <!-- Ingredients Accordion -->
            <div class="ingredients_box">

                <div class="ingredients_header">
                    <span>Ingredients</span>
                    <i class="arrow">▼</i>
                </div>

                <div class="ingredients_content">
                    <?php echo $item['ingredients']; ?>
                </div>

            </div>

            <button class="add_cart_btn" data-id="<?php echo $item['itmId']; ?>">
                Add to Cart
            </button>

        </div>

        <!-- RIGHT IMAGE -->
        <div class="item_img">
            <img src="images/items/<?php echo $item['mainpic']; ?>">
        </div>

    </div>

    <?php } ?>
    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/specialprogram.js?v=1.1"></script>
</body>