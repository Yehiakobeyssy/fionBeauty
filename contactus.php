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


            require 'mail.php';

        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = htmlspecialchars($_POST['name']);
            $email   = htmlspecialchars($_POST['email']);
            $phone   = htmlspecialchars($_POST['phone']);
            $message = htmlspecialchars($_POST['message']);
            $sendetto="info@fionbeautysupplies.ca";

            // Create a new PHPMailer instance

            try {
                //Server settings


                //Recipients
                $mail->setFrom($applicationemail, 'Fion Beauty Supplies'); // Sender
                $mail->addAddress($sendetto);              // Receiver

                //Content
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'New Contact Request from Website';    
                $mail->Body    = "
                    <h2>New Contact Request</h2>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Phone:</strong> {$phone}</p>
                    <p><strong>Message:</strong><br>{$message}</p>
                ";

                $mail->send();

                // Redirect or show success message
                $_SESSION['success'] = "Your request has been sent successfully!";
                header("Location: index.php");
                exit;

            } catch (Exception $e) {
                $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                header("Location: index.php");
                exit;
            }

        } 
?>    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="common/root.css">
    <link rel="stylesheet" href="css/contactus.css">
</head>
<body>
    <?php 
        include 'include/header.php';
        include 'include/clientheader.php'; 
        include 'include/catecorysname.php';
    ?>
    <main>
        <div class="side_decor">
            <img src="images/img_app/contactusimg.png" alt="" srcset="">
        </div>
        <div class="contactus_form">
            <div class="container_form">
                <div class="title_form">
                    <h3>We Respond Fast</h3>
                </div>
                <form action="" method="post">
                    <label for="">Name</label>
                    <input type="text" name="name" id="">
                    <label for="">Email</label>
                    <input type="email" name="email" id="">
                    <label for="">Phone Number</label>
                    <input type="text" name="phone" id="">
                    <label for="">Massage</label>
                    <textarea name="message" id="" rows="4"></textarea>
                    <div class="btncontrol">
                        <button type="submit">
                            Send
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                <path d="M8.25997 10.8807H3.3333L1.6858 4.32653C1.67525 4.28844 1.66882 4.24933 1.66663 4.20986C1.6483 3.60903 2.30997 3.19236 2.8833 3.46736L18.3333 10.8807L2.8833 18.294C2.31663 18.5665 1.6633 18.1615 1.66663 17.5715C1.66832 17.5188 1.67758 17.4666 1.69413 17.4165L2.91663 13.3807" stroke="#F5F5F5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </main>

    <?php include  'include/footer.php' ?>
    <?php include 'common/jslinks.php'?>
    <script src="js/contactus.js"></script>

</body>