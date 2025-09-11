<?php
    
    function checkItem($select, $from, $value) {
        global $con;
        $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();
        return $count;
    }
    
    function getCount($con, $table, $condition = '') {
        $sql = "SELECT COUNT(*) AS count FROM $table $condition";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    function countItems($item, $table) {
		global $con;
		$stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");
		$stmt2->execute();
		return $stmt2->fetchColumn();
	}

    function countbetweendates($table,$column,$begin,$end){
        global $con;
        $stmt3 = $con->prepare("SELECT COUNT(*) AS C FROM $table WHERE $column  BETWEEN  ?  AND ? ");
        $stmt3->execute(array($begin,$end));
        $result=$stmt3->fetch();
        return $result['C'];
    }

    function countitemRS($select, $from,$column,$value) {
        global $con;
        $statement = $con->prepare("SELECT $select FROM $from WHERE $column = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();
        return $count;
    }

    function getID($PrimeryKEY,$from,$column,$value){
        global $con;
        $sql = $con->prepare("SELECT $PrimeryKEY FROM $from WHERE $column =? ");
        $sql->execute(array($value));
        $result=$sql->fetch();
        return $result[$PrimeryKEY];
    }

    
    function get_last_ID($PrimeryKEY,$tableName){
        global $con;
        $sql=$con->prepare("SELECT $PrimeryKEY FROM $tableName ORDER BY $PrimeryKEY DESC LIMIT 1");
        $sql->execute();
        $result=$sql->fetch();
        return $result[$PrimeryKEY];
    }

    function calculateDistanceBetweenTwoPoints($latitudeOne='', $longitudeOne='', $latitudeTwo='', $longitudeTwo='',$distanceUnit ='',$round=false,$decimalPoints='')
    {
        if (empty($decimalPoints)) 
        {
            $decimalPoints = '3';
        }
        if (empty($distanceUnit)) {
            $distanceUnit = 'KM';
        }
        $distanceUnit = strtolower($distanceUnit);
        $pointDifference = $longitudeOne - $longitudeTwo;
        $toSin = (sin(deg2rad($latitudeOne)) * sin(deg2rad($latitudeTwo))) + (cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * cos(deg2rad($pointDifference)));
        $toAcos = acos($toSin);
        $toRad2Deg = rad2deg($toAcos);

        $toMiles  =  $toRad2Deg * 60 * 1.1515;
        $toKilometers = $toMiles * 1.609344;
        $toNauticalMiles = $toMiles * 0.8684;
        $toMeters = $toKilometers * 1000;
        $toFeets = $toMiles * 5280;
        $toYards = $toFeets / 3;


              switch (strtoupper($distanceUnit)) 
              {
                  case 'ML'://miles
                         $toMiles  = ($round == true ? round($toMiles) : round($toMiles, $decimalPoints));
                         return $toMiles;
                      break;
                  case 'KM'://Kilometers
                        $toKilometers  = ($round == true ? round($toKilometers) : round($toKilometers, $decimalPoints));
                        return $toKilometers;
                      break;
                  case 'MT'://Meters
                        $toMeters  = ($round == true ? round($toMeters) : round($toMeters, $decimalPoints));
                        return $toMeters;
                      break;
                  case 'FT'://feets
                        $toFeets  = ($round == true ? round($toFeets) : round($toFeets, $decimalPoints));
                        return $toFeets;
                      break;
                  case 'YD'://yards
                        $toYards  = ($round == true ? round($toYards) : round($toYards, $decimalPoints));
                        return $toYards;
                      break;
                  case 'NM'://Nautical miles
                        $toNauticalMiles  = ($round == true ? round($toNauticalMiles) : round($toNauticalMiles, $decimalPoints));
                        return $toNauticalMiles;
                      break;
              }


    }


    function calculatePaymentDetails($invoiceAmount, $amountPaid = 0) {
        if ($invoiceAmount < 150) {
            $numberOfPayments = 1;
            $paymentAmount = $invoiceAmount;
        } elseif ($invoiceAmount >= 150 && $invoiceAmount <= 500) {
            $numberOfPayments = 2;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 500 && $invoiceAmount <= 1000) {
            $numberOfPayments = 3;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 1000 && $invoiceAmount <= 3000) {
            $numberOfPayments = 4;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 3000 && $invoiceAmount <= 6000) {
            $numberOfPayments = 5;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 6000 && $invoiceAmount <= 10000) {
            $numberOfPayments = 6;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 10000 && $invoiceAmount <= 15000) {
            $numberOfPayments = 7;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 15000 && $invoiceAmount <= 20000) {
            $numberOfPayments = 8;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } else {
            $numberOfPayments = 10;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        }
        
        $paymentsMade = floor($amountPaid / $paymentAmount);
        $remainingPayments = max(0, $numberOfPayments - $paymentsMade);
    
        $overpayment = $amountPaid - ($paymentsMade * $paymentAmount);
    
        $paymentDetails = array(
            'numberOfPayments' => $numberOfPayments,
            'paymentAmount' => $paymentAmount,
            'overpayment' => $overpayment,
            'paymentsMade' => $paymentsMade,
            'remainingPayments' => $remainingPayments
        );
    
        return $paymentDetails;
    }

    function calculateCommission($invoiceID, $userPayment, $con) {
        $result = array(
            'commission' => 0,
            'SalemanID' => null
        );
    
        // Get the total amount, total tax, client ID, and promo_Code from tblinvoice
        $invoiceQuery = "SELECT TotalAmount, TotalTax, ClientID FROM tblinvoice WHERE InvoiceID = :invoiceID";
        $invoiceStmt = $con->prepare($invoiceQuery);
        $invoiceStmt->bindParam(':invoiceID', $invoiceID);
        $invoiceStmt->execute();
        $invoiceRow = $invoiceStmt->fetch();
    
        if ($invoiceRow) {
            $totalAmount = $invoiceRow['TotalAmount'];
            $totalTax = $invoiceRow['TotalTax'];
            $clientID = $invoiceRow['ClientID'];
    
            // Calculate the total invoice amount
            $totalInvoiceAmount = $totalAmount + $totalTax;
    
            // Get the promo_Code associated with the client
            $clientQuery = "SELECT promo_Code FROM tblclients WHERE ClientID = :clientID";
            $clientStmt = $con->prepare($clientQuery);
            $clientStmt->bindParam(':clientID', $clientID);
            $clientStmt->execute();
            $clientRow = $clientStmt->fetch();
    
            if ($clientRow) {
                $promoCode = strtolower($clientRow['promo_Code']); // Convert promoCode to lowercase
    
                // Get the SalemanID and commission rate based on the promo code
                $salesmanData = getSalesmanDataByPromoCode($promoCode, $con);
    
                if ($salesmanData) {
                    $SalemanID = $salesmanData['SalePersonID'];
                    $commissionRate = $salesmanData['ComitionRate'];
    
                    // Check if the service allows commission based on Get_commission
                    $detailInvoiceQuery = "SELECT Service,UnitPrice FROM tbldetailinvoice WHERE Invoice = :invoiceID";
                    $detailInvoiceStmt = $con->prepare($detailInvoiceQuery);
                    $detailInvoiceStmt->bindParam(':invoiceID', $invoiceID);
                    $detailInvoiceStmt->execute();
    
                    while ($detailInvoiceRow = $detailInvoiceStmt->fetch()) {
                        $serviceID = $detailInvoiceRow['Service'];
                        $servicePrice = $detailInvoiceRow['UnitPrice'];
    
                        // Check if the service allows commission based on Get_commission
                        $serviceQuery = "SELECT Get_commission FROM tblservices WHERE ServiceID = :serviceID";
                        $serviceStmt = $con->prepare($serviceQuery);
                        $serviceStmt->bindParam(':serviceID', $serviceID);
                        $serviceStmt->execute();
                        $serviceRow = $serviceStmt->fetch();
    
                        if ($serviceRow) {
                            $allowsCommission = ($serviceRow['Get_commission'] == 1) ? 1 : 0;

                            if($userPayment > $totalInvoiceAmount){
                                $percentofpayment = 100;
                            }else{
                                $percentofpayment = $userPayment *100 /$totalInvoiceAmount;
                            }
                            
                            if ($allowsCommission == 1) {
                                $itemnewprice = $commissionRate * $servicePrice /100;
                                // Calculate commission for the item based on user's payment and total invoice amount
                                $itemCommission = $percentofpayment * $itemnewprice / 100;

                                // Add the item's commission to the total commission
                                $result['commission'] += $itemCommission;
                            }
                        }
                    }
    
                    // Set the SalemanID in the result array
                    $result['SalemanID'] = $SalemanID;
                }
            }
        }
    
        return $result;
    }
    
    // Function to get SalemanID and commission rate based on the promo code
    function getSalesmanDataByPromoCode($promoCode, $con) {
        $promoCode = strtolower($promoCode); // Convert promoCode to lowercase
        $salesmanQuery = "SELECT SalePersonID,ComitionRate FROM tblsalesperson WHERE saleActive = 1 AND  LOWER(PromoCode) = :promoCode";
        $salesmanStmt = $con->prepare($salesmanQuery);
        $salesmanStmt->bindParam(':promoCode', $promoCode);
        $salesmanStmt->execute();
        return $salesmanStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    function generateRandomPassword($length = 12) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_+=';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }
    
    

    function countCard($con, $promocode) {
        // Initialize an array to store the counts
        $counts = array();
    
        // Count Clients
        $clientQuery = "SELECT COUNT(ClientID) FROM tblclients WHERE promo_Code = :promocode";
        $clientStmt = $con->prepare($clientQuery);
        $clientStmt->bindParam(':promocode', $promocode);
        $clientStmt->execute();
        $counts['clients'] = $clientStmt->fetchColumn();
    
        // Count Services
        $servicesQuery = "SELECT COUNT(ServicesID) FROM tblclientservices WHERE ClientID IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode)";
        $servicesStmt = $con->prepare($servicesQuery);
        $servicesStmt->bindParam(':promocode', $promocode);
        $servicesStmt->execute();
        $counts['services'] = $servicesStmt->fetchColumn();
    
        // Count Services - In Process
        $inProcessQuery = "SELECT COUNT(ServicesID) FROM tblclientservices WHERE ClientID IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode) AND ServiceDone = 1";
        $inProcessStmt = $con->prepare($inProcessQuery);
        $inProcessStmt->bindParam(':promocode', $promocode);
        $inProcessStmt->execute();
        $counts['in_process_services'] = $inProcessStmt->fetchColumn();
    
        // Count Services - Expire Soon
        $expireSoonQuery = "SELECT COUNT(ServicesID) FROM tblclientservices WHERE ClientID IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode) AND serviceStatus = 2";
        $expireSoonStmt = $con->prepare($expireSoonQuery);
        $expireSoonStmt->bindParam(':promocode', $promocode);
        $expireSoonStmt->execute();
        $counts['expire_soon_services'] = $expireSoonStmt->fetchColumn();
    
        // Add counting logic for Domein and Tickets here
        // Count Domein
        $domeinQuery = "SELECT COUNT(DomeinID) FROM tbldomeinclients WHERE Client IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode)";
        $domeinStmt = $con->prepare($domeinQuery);
        $domeinStmt->bindParam(':promocode', $promocode);
        $domeinStmt->execute();
        $counts['domein'] = $domeinStmt->fetchColumn();
    
        // Count Domein - In Process
        $inProcessDomeinQuery = "SELECT COUNT(DomeinID) FROM tbldomeinclients WHERE Client IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode) AND Status = 1";
        $inProcessDomeinStmt = $con->prepare($inProcessDomeinQuery);
        $inProcessDomeinStmt->bindParam(':promocode', $promocode);
        $inProcessDomeinStmt->execute();
        $counts['in_process_domein'] = $inProcessDomeinStmt->fetchColumn();
    
        // Count Domein - Expire Soon
        $expireSoonDomeinQuery = "SELECT COUNT(DomeinID) FROM tbldomeinclients WHERE Client IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode) AND Status = 2";
        $expireSoonDomeinStmt = $con->prepare($expireSoonDomeinQuery);
        $expireSoonDomeinStmt->bindParam(':promocode', $promocode);
        $expireSoonDomeinStmt->execute();
        $counts['expire_soon_domein'] = $expireSoonDomeinStmt->fetchColumn();
    
        // Count Tickets
        $ticketsQuery = "SELECT COUNT(ticketID) FROM tblticket WHERE ClientID IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode)";
        $ticketsStmt = $con->prepare($ticketsQuery);
        $ticketsStmt->bindParam(':promocode', $promocode);
        $ticketsStmt->execute();
        $counts['tickets'] = $ticketsStmt->fetchColumn();
    
        // Count Tickets - Open
        $openTicketsQuery = "SELECT COUNT(ticketID) FROM tblticket WHERE ClientID IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode) AND ticketStatus = 1";
        $openTicketsStmt = $con->prepare($openTicketsQuery);
        $openTicketsStmt->bindParam(':promocode', $promocode);
        $openTicketsStmt->execute();
        $counts['open_tickets'] = $openTicketsStmt->fetchColumn();
    
        // Count Tickets - Client Respond
        $clientRespondTicketsQuery = "SELECT COUNT(ticketID) FROM tblticket WHERE ClientID IN (SELECT ClientID FROM tblclients WHERE promo_Code = :promocode) AND ticketStatus = 2";
        $clientRespondTicketsStmt = $con->prepare($clientRespondTicketsQuery);
        $clientRespondTicketsStmt->bindParam(':promocode', $promocode);
        $clientRespondTicketsStmt->execute();
        $counts['client_respond_tickets'] = $clientRespondTicketsStmt->fetchColumn();
    
        return $counts;
    }
    
    // Retrieve promo code from your result


    function create_checkout($amount,$discription,$invoiceID){
        global $con;
        $sql=$con->prepare('SELECT Sumup_AccessToken,Sumup_merchat_code,Sumup_Email FROM tblsetting LIMIT 1');
        $sql->execute();
        $result = $sql->fetch();
        
        $api_endpoint = 'https://api.sumup.com/v0.1/checkouts';
        $access_token = $result['Sumup_AccessToken']; 
        $merchant_code = $result['Sumup_merchat_code'];
        $invoiceID = $invoiceID;
        $myemail = $result['Sumup_Email'];
        $amount = $amount;
        $discription = $discription;
    
        // Data to be sent in the request body
        $data = [
            'amount'             => $amount,
            'checkout_reference' => $invoiceID . time(),
            'currency'           => 'EUR',
            'description'        => $discription,
            'merchant_code'      => $merchant_code,
            'pay_to_email'       => $myemail,
            'status'             => 'PENDING',
            'valid_until'        => date('Y-m-d\TH:i:s\Z', strtotime('+5 minutes'))
        ];
     
        // Initialize cURL session
        $ch = curl_init($api_endpoint);
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Execute cURL session
        $response = curl_exec($ch);
    
        // Check for errors
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
            curl_close($ch);
            exit;
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Decode JSON response
        $response_data = json_decode($response, true);
    
        // Check response status
        if (isset($response_data['id'])) {
            $checkout_id = $response_data['id'];
            return $checkout_id;
        } else {
            //echo 'Failed to create checkout. Response: ' . $response;
        }
    }

    function uploadFile($file, $targetDir) {
        if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return 'No Document'; // or handle error
        }

        $temp = explode(".", $file['name']);
        $extension = strtolower(end($temp));
        $allowedExt = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']; // Optional: security

        if (!in_array($extension, $allowedExt)) {
            return 'No Document'; // or handle invalid extension
        }

        $newfilename = round(microtime(true)) . '.' . $extension;
        $destination = $targetDir . $newfilename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $newfilename;
        } else {
            return 'No Document'; // or handle move failure
        }
    }
?>