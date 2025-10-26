
<!DOCTYPE html>
<html lang="en">
    <?php include 'common/head.php' ?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
  <style>
    :root {
      --main-green: #024d27;
      --main-white: #ffffff;
    }

    body {
      margin: 0;
      padding: 0;
      background: var(--main-green);
      color: var(--main-white);
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
      text-align: center;
    }

    img {
      width: 200px;
      animation: zoomFade 3s ease-in-out infinite alternate;
    }

    @keyframes zoomFade {
      from {
        opacity: 0.7;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1.05);
      }
    }

    h1 {
      font-size: 28px;
      margin: 20px 0 10px;
    }

    p {
      font-size: 18px;
      max-width: 600px;
      margin: 0 auto 20px;
      line-height: 1.5;
    }

    input[type="email"] {
      padding: 10px;
      border: none;
      border-radius: 6px;
      width: 250px;
      margin-right: 10px;
    }

    button {
      padding: 10px 20px;
      background: var(--main-white);
      color: var(--main-green);
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>
  <img src="images/logo.png" alt="FION Logo">
  <h1>Coming Soon</h1>
  <p>
    Get ready for <strong>FION BEAUTY SUPPLIES</strong>, the comprehensive online platform designed exclusively for beauty professionals. <br>
    Soon, you'll be able to shop leading brands, book advanced training sessions, and download all the professional resources you need to succeed.  
  </p>
  <p><strong>Be the first to know! Sign up for launch updates.</strong></p>


</body>
</html>
