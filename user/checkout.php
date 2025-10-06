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

     if(isset($_POST['btnaddAdd'])){
        $userID         = $user_id;
        $NameAdd        = $_POST['NameAdd'];
        $phoneNumber    = $_POST['phoneNumber'];
        $emailAdd       = $_POST['emailAdd'];
        $provinceID     = $_POST['provinceID'];
        $cityID         = $_POST['cityID'];
        $street         = $_POST['street'];
        $poatalCode     = $_POST['poatalCode'];
        $bultingNo      = $_POST['bultingNo'];
        $doorNo         = $_POST['doorNo'];
        $noteAdd        = $_POST['noteAdd'];
        $mainAdd        = isset($_POST['mainAdd'])?1:0;
        $addActive      = 1;

        if($mainAdd == 1){
            $sql=$con->prepare('UPDATE tbladdresse SET mainAdd = 0 WHERE userID = ?');
            $sql->execute([$userID]);
        }

        $stat = $con->prepare('INSERT INTO tbladdresse (userID,NameAdd,phoneNumber,emailAdd,provinceID,cityID,street,poatalCode,bultingNo,doorNo,noteAdd,mainAdd,addActive) 
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stat->execute([$userID,$NameAdd,$phoneNumber,$emailAdd,$provinceID,$cityID,$street,$poatalCode,$bultingNo,$doorNo,$noteAdd,$mainAdd,$addActive]);
    }


    $stat = $con->prepare('SELECT PK, SK FROM tblfinancesetting WHERE SettingID = 1');
    $stat->execute();
    $result_Keys = $stat->fetch();
    $PK = $result_Keys['PK'];
    $SK =$result_Keys['SK'];
    
    include '../common/head.php';
?>
    <link rel="stylesheet" href="../common/root.css">
    <link rel="stylesheet" href="css/checkout.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <?php
        include 'include/header.php';
        include 'include/clientheader.php';
        include 'include/catecorysname.php';
    ?>
    <div class="titleCatecory">
        <div class="navbarsection">
            <h5>Home/ Cart /  <strong>Checkout</strong></h5>
        </div> 
        <div class="catecoryname">
        </div>      
        <div class="desgin">
        </div>
    </div>
    <div class="title_page">
        <h4>Checkout</h4>
    </div>
    <div class="order_information">
        <div class="addresses_info">
            <div class="addresses">
                <div class="titleAddresses">
                    <h4>Billing Information</h4>
                </div>
                <div class="cards_addresses">
                    <?php
                        $sql= $con->prepare('SELECT addresseID ,NameAdd,phoneNumber,street,bultingNo,doorNo,poatalCode,cityName, provinceName
                                            FROM tbladdresse
                                            INNER JOIN tblcity ON tblcity.cityID = tbladdresse.cityID
                                            INNER JOIN tblprovince ON tblprovince.provinceID = tbladdresse.provinceID
                                            WHERE  userID = ? AND addActive = 1
                                            ORDER BY mainAdd DESC');
                        $sql->execute([$user_id]);
                        $addresses = $sql->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($addresses as $useradd){
                            echo '
                                <div class="add">
                                    <div class="generalinfo">
                                        <input type="radio" name="" id="" class="choiceAdd" value="'.$useradd['addresseID'].'">
                                        <h5>'.$useradd['NameAdd'].'</h5>
                                        <span>'.$useradd['phoneNumber'].'</span>
                                    </div>
                                    <div class="addinformation">
                                        <label for="">'.$useradd['street'].' '.$useradd['bultingNo'].' '.$useradd['doorNo'].'</label><br>
                                        <label for="">'.$useradd['cityName'].' - '.$useradd['provinceName'].'</label><br>
                                        <label for="">'.$useradd['poatalCode'].'</label>
                                    </div>
                                </div>
                            ';
                        }
                    ?>
                </div>
                <div class="addnewadd">
                    <div class="openformadd">
                        <h5 id="openform"><span>+</span> Add New Address</h5>
                    </div>
                    <form action="" method="post" class="frmadd">
                        <h5>Adding New Address</h5>
                        <div class="long">
                            <label for="">Name :</label>
                            <input type="text" name="NameAdd" id="" required>
                        </div>
                        <div class="double">
                            <div class="insite">
                                <label for="">Province</label>
                                <select name="provinceID" id="">
                                    <option value="0">SELECT ONE</option>
                                    <?php
                                        $sql= $con->prepare('SELECT provinceID , provinceName FROM  tblprovince');
                                        $sql->execute();
                                        $provicnces = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($provicnces as $pro){
                                            echo '<option value="'.$pro['provinceID'].'">'.$pro['provinceName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="insite">
                                <label for="">City</label>
                                <select name="cityID" id="" required>
                                    <option value="">SELECT ONE</option>
                                    <?php 
                                        $sql= $con->prepare('SELECT cityID ,cityName FROM tblcity');
                                        $sql->execute();
                                        $citys =$sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($citys as $city){
                                            echo '<option value="'.$city['cityID'].'">'.$city['cityName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="insite">
                                <label for="">Postal Code</label>
                                <input type="text" name="poatalCode" id="" required>
                            </div>
                        </div>
                        <div class="long">
                            <label for="">Street</label>
                            <input type="text" name="street" id="" required>
                        </div>
                        <div class="double">
                            <div class="insite">
                                <label for="">Bulting No </label>
                                <input type="text" name="bultingNo" id="" required>
                            </div>
                            <div class="insite">
                                <label for="">Door No </label>
                                <input type="text" name="doorNo" id="" required>
                            </div>
                        </div>
                        <div class="long">
                            <label for="">Phone Number</label>
                            <input type="text" name="phoneNumber" id="" >
                        </div>
                        <div class="long">
                            <label for="">E-mail</label>
                            <input type="email" name="emailAdd" id="">
                        </div>
                        <div class="long">
                            <label for="">Delivery Instruction</label>
                            <textarea name="noteAdd" id=""></textarea>
                        </div>
                        <div class="set">
                            <input type="checkbox" name="mainAdd" id="">
                            <label for="">Set as default shipping address.</label>
                        </div>
                        <div class="btncontrol">
                            <button type="submit" class="btn-primary" name="btnaddAdd">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="addinfo">
                <h5>Additional Info</h5>
                <label for="">Order Notes (Optional)</label>
                <textarea name="" id="txtinvoiceNote" placeholder="I need it quickly"></textarea>
            </div>
        </div>
        <div class="order_summary">
            <div class="tile_summary">
                <h6>Order Summary</h6>
            </div>
            <div class="cart_info">
                <?php
                    foreach ($_SESSION['cart'] as $id => $qty) {
                        $stmt = $con->prepare("SELECT * FROM tblitems WHERE itmId = ?");
                        $stmt->execute([$id]);
                        $item = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!$item) continue;

                        $price = $item['sellPrice'];
                        $minQty = $item['minQuantity'];

                        // Check discount
                        $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity  <= ? ORDER BY quatity DESC LIMIT 1");
                        $stmt->execute([$id, $qty]);
                        $discountPercent = $stmt->fetchColumn() ?: 0;

                        $discountAmount = ($price * $discountPercent / 100);
                        $discountedPrice = $price - $discountAmount;
                        $subtotal = $discountedPrice * $qty;

                        echo "
                            <div class='data_itm'>
                                <img src='../images/items/{$item['mainpic']}'> 
                                <h6>{$item['itmName']}</h6>
                                <span>\$" . number_format($subtotal, 2) . "</span>
                            </div>
                        ";
                    }
                ?>
            </div>
            <div class="cart_summary">
                <?php
                    $stmt = $con->prepare("SELECT taxPercent, includeTax FROM tblfinancesetting WHERE SettingID = 1");
                    $stmt->execute();
                    $finance = $stmt->fetch(PDO::FETCH_ASSOC);
                    $taxPercent = $finance['taxPercent'] ?? 0;
                    $includeTax = $finance['includeTax'] ?? 0;

                    // Initialize totals
                    $totalSubtotal = 0;  // before discount
                    $totalDiscount = 0;
                    $totalTax = 0;

                    // Loop over cart
                    foreach ($_SESSION['cart'] as $itemId => $qty) {
                        // Get item details
                        $stmt = $con->prepare("SELECT itmId, itmName, sellPrice, minQuantity FROM tblitems WHERE itmId = ?");
                        $stmt->execute([$itemId]);
                        $item = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$item) continue;

                        $price = $item['sellPrice'];
                        $subtotal = $price * $qty;

                        // Get discount percent (if any)
                        $stmt = $con->prepare("SELECT precent FROM tbldiscountitem WHERE itemID = ? AND quatity <= ? ORDER BY quatity DESC LIMIT 1");
                        $stmt->execute([$itemId, $qty]);
                        $discountPercent = $stmt->fetchColumn() ?: 0;
                        $discountAmount = ($price * $qty * $discountPercent) / 100;

                        // Accumulate totals
                        $totalSubtotal += $subtotal;
                        $totalDiscount += $discountAmount;
                    }

                    // --- Apply tax logic ---
                    if ($includeTax == 1) {
                        // Prices already include tax
                        $subtotalExcludingTax = $totalSubtotal / (1 + ($taxPercent / 100)); // real subtotal (without tax)
                        $totalTax = $totalSubtotal - $subtotalExcludingTax; // extracted tax
                        $grandTotal = $totalSubtotal - $totalDiscount; // tax already included in price
                    } else {
                        // Prices do not include tax → add tax after discount
                        $taxBase = $totalSubtotal - $totalDiscount;
                        $totalTax = $taxBase * ($taxPercent / 100);
                        $grandTotal = $taxBase + $totalTax;
                    }
                ?>
                <table>
                    <tr>
                        <th>Subtotal</th>
                        <td class="lblnumbers">$<?= number_format(($includeTax ? $subtotalExcludingTax : $totalSubtotal), 2) ?></td>
                    </tr>
                    <tr>
                        <th>Saving</th>
                        <td class="lblnumbers">− $<?= number_format($totalDiscount, 2) ?></td>
                    </tr>
                    <tr>
                        <th>Tax (<?= $taxPercent ?>%)</th>
                        <td class="lblnumbers">$<?= number_format($totalTax, 2) ?></td>
                    </tr>
                    <tr>
                        <th><strong>Grand Total</strong></th>
                        <th class="lblnumbers"><strong>$<?= number_format($grandTotal, 2) ?></strong></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <form action="">
        <input type="text" name="address" id="txtaddress">
        <input type="text" name="" id="txtnote">
        <input type="number" name="" id="txtsubtotal" value="<?=($includeTax ? $subtotalExcludingTax : $totalSubtotal)?>">
        <input type="number" name="" id="txtdiscount" value="<?=$totalDiscount?>">
        <input type="number" name="" id="txtgrandtotal" value="<?=$grandTotal?>">
    </form>
    
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/checkout.js"></script>
</body>