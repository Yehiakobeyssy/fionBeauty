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
    $result_Keys = $stat->fetch(PDO::FETCH_ASSOC);
    $PK = $result_Keys['PK'] ?? '';
    $SK = $result_Keys['SK'] ?? '';
    
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
    <?php
            $sql = $con->prepare('SELECT clientBlock FROM  tblclient WHERE clientID  = ?');
            $sql->execute([$user_id]);
            $result_block = $sql->fetch();
            $isBlock = $result_block['clientBlock'];

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
                        foreach ($addresses as $i => $useradd){
                            $checked = ($i === 0) ? 'checked' : ''; // first address = main
                            echo '
                                <div class="add">
                                    <div class="generalinfo">
                                        <input type="radio" name="choiceAdd" class="choiceAdd" value="'.$useradd['addresseID'].'" '.$checked.'>
                                        <h5>'.$useradd['NameAdd'].'</h5>
                                        <span>'.$useradd['phoneNumber'].'</span>
                                    </div>
                                    <div class="addinformation">
                                        <label>'.$useradd['street'].' '.$useradd['bultingNo'].' '.$useradd['doorNo'].'</label><br>
                                        <label>'.$useradd['cityName'].' - '.$useradd['provinceName'].'</label><br>
                                        <label>'.$useradd['poatalCode'].'</label>
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
                                <select name="provinceID" id="provinceSelect" required>
                                    <option value="0">SELECT ONE</option>
                                    <?php
                                        $sql = $con->prepare('SELECT provinceID, provinceName FROM tblprovince WHERE provinceActive = 1');
                                        $sql->execute();
                                        $provinces = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($provinces as $pro) {
                                            echo '<option value="'.$pro['provinceID'].'">'.$pro['provinceName'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="insite">
                                <label for="">City</label>
                                <select name="cityID" id="citySelect" required>
                                    <option value="">SELECT ONE</option>
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
    
    <form action="" id="cart-form">
        <input type="text" name="address" id="txtaddress"  hidden>
        <input type="text" name="" id="txtnote" hidden>
        <input type="number" name="" id="txtsubtotal" value="<?=($includeTax ? $subtotalExcludingTax : $totalSubtotal)?>" hidden>
        <input type="number" name="" id="txtTax" value="<?=$totalTax?>" hidden>
        <input type="number" name="" id="txtdiscount" value="<?=$totalDiscount?>" hidden>
        <input type="number" name="" id="txtgrandtotal" value="<?=$grandTotal?>" hidden>
    </form>
    <div class="payment_methods">
        <h4>Choose Payment Method</h4>
        <?php 
            $sql = $con->prepare("SELECT methodName, methodNote FROM tblpaymentmethods WHERE methodActive = 1");
            $sql->execute();
            $methods = $sql->fetchAll();
        ?>
        <div class="containerpayment">
            <?php foreach ($methods as $method): 
                $slug = strtolower(str_replace([' ', '/'], '_', $method['methodName']));
            ?>
            <div class="payment_option" data-method="<?=$slug?>">
                <label>
                    <input type="radio" name="paymentMethod" value="<?=$slug?>" <?= $slug === 'card' ? 'checked' : '' ?>>
                    <strong><?=$method['methodName']?></strong>
                    
                    <?php if($slug==='klarna'): ?><?php endif; ?>
                    <?php if($slug==='afterpay_clearpay'): ?><?php endif; ?>
                    <?php if($slug==='paypal'): ?><?php endif; ?>
                    <?php if($slug==='card'): ?><?php endif; ?>
                        <!-- <img src="img/card.png" alt="Card" class="pm-logo"> -->
                </label>
                <p class="note"><?=$method['methodNote']?></p>
                <div class="method_container" id="method_<?=$slug?>"></div>
            </div>
            <?php endforeach; ?>
        </div>
        
    </div>
    <?php include 'include/footer.php' ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/checkout.js"></script>
        
    <script>
        const addressRadios = document.querySelectorAll(".choiceAdd");
    const addressField = document.getElementById("txtaddress");

    // Fill hidden field when a radio is clicked
    addressRadios.forEach(radio => {
        radio.addEventListener("change", function(){
            addressField.value = this.value;
        });
    });

    // Auto-fill hidden field with the checked radio on page load
    const checkedRadio = document.querySelector(".choiceAdd:checked");
    if(checkedRadio) {
        addressField.value = checkedRadio.value;
    }

        document.addEventListener("DOMContentLoaded", function(){
            const radios = document.querySelectorAll("input[name='paymentMethod']");
            const containers = document.querySelectorAll(".method_container");

            async function loadPayment(method) {
                // Check if an address is selected
                const addressField = document.getElementById("txtaddress");
                if (!addressField.value) {
                    alert("Please select a billing/shipping address before paying.");
                    // Optionally, scroll to the addresses section
                    document.querySelector(".addresses_info").scrollIntoView({behavior: "smooth"});
                    return; // stop further execution
                }

                // Hide other containers
                const containers = document.querySelectorAll(".method_container");
                containers.forEach(c => c.style.display = "none");

                // Show selected container
                const container = document.getElementById("method_" + method);
                container.style.display = "block";
                container.innerHTML = "<div style='text-align:center;color:#009245;'>Loading...</div>";

                // Load via AJAX
                const resp = await fetch("ajax_payment_section.php?method=" + method);
                const html = await resp.text();
                container.innerHTML = html;

                // Execute inline scripts
                container.querySelectorAll("script").forEach(scr => eval(scr.innerText));
            }
            
            radios.forEach(radio => {
                radio.addEventListener("change", function(){
                    loadPayment(this.value);
                });
            });

            // Load the default checked radio on page load
            const defaultRadio = document.querySelector("input[name='paymentMethod']:checked");
            if(defaultRadio) loadPayment(defaultRadio.value);
        });

        function saveOrderInfo() {
            const orderData = {
                address: document.getElementById("txtaddress").value,
                note: document.getElementById("txtnote").value,
                subtotal: document.getElementById("txtsubtotal").value,
                discount: document.getElementById("txtdiscount").value,
                tax:document.getElementById('txtTax').value,
                grandtotal: document.getElementById("txtgrandtotal").value
            };

            // Send to server to store in session
            fetch("ajaxuser/save_order_session.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(orderData)
            });
        }


        $('#provinceSelect').on('change', function() {
            const provinceID = $(this).val();

            if (provinceID == 0) {
                $('#citySelect').html('<option value="">SELECT ONE</option>');
                return;
            }

            $.ajax({
                url: 'ajaxuser/getCities.php',
                type: 'POST',
                data: { provinceID },
                dataType: 'json',
                success: function(response) {
                    let options = '<option value="">SELECT ONE</option>';
                    if (response.length > 0) {
                        $.each(response, function(index, city) {
                            options += `<option value="${city.cityID}">${city.cityName}</option>`;
                        });
                    } else {
                        options += '<option value="">No cities available</option>';
                    }
                    $('#citySelect').html(options);
                }
            });
        });

        // 🔹 (Optional) When City changes → auto-select its province
        $('#citySelect').on('change', function() {
            const cityID = $(this).val();
            if (cityID === "") return;

            $.ajax({
                url: 'ajaxuser/getProvinceByCity.php',
                type: 'POST',
                data: { cityID },
                dataType: 'json',
                success: function(response) {
                    if (response && response.provinceID) {
                        $('#provinceSelect').val(response.provinceID);
                    }
                }
            });
        });
    </script>
</body>