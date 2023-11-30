<?php
include_once('../db.php');
error_reporting(0);

function saveRememberMeToken($userId, $token)
{
  global $conn;

  $query = "UPDATE user SET remember_me_token = ? WHERE user_id = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "si", $token, $userId);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

function getUserIdByRememberMeToken($token)
{
  global $conn;

  $query = "SELECT user_id FROM user WHERE remember_me_token = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);
  mysqli_stmt_close($stmt);

  return $row['user_id'];
}

// function deleteRememberMeToken($userId)
// {
//   global $conn;

//   $query = "UPDATE user SET remember_me_token = NULL WHERE user_id = ?";
//   $stmt = mysqli_prepare($conn, $query);
//   mysqli_stmt_bind_param($stmt, "i", $userId);
//   mysqli_stmt_execute($stmt);
//   mysqli_stmt_close($stmt);
// }

if (isset($_POST['submit'])) {
  if (!empty($_POST['user_name']) && !empty($_POST['user_pass'])) {
    $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
    $user_pass = filter_var($_POST['user_pass'], FILTER_SANITIZE_STRING);

    $query = "SELECT * FROM `user` WHERE `user_name`=?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $user_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_assoc($result);

      if (password_verify($user_pass, $row['user_password'])) {
        $_SESSION['id'] = $row['user_id'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_name'] = $row['user_name'];
        $_SESSION['user_email'] = $row['user_email'];
        $_SESSION['user_contact'] = $row['user_contact'];
        $_SESSION['user_logged_in'] = true;

        if (isset($_POST['remember_me'])) {
          $token = bin2hex(random_bytes(32));

          saveRememberMeToken($row['user_id'], $token);

          $expireTime = time() + (30 * 24 * 60 * 60);
          setcookie('remember_me', $token, $expireTime, '/');
        }

        header("Location: UserIndex.php");
        exit();
      } else {
        echo "<script>alert('Wrong Password, Please try again');</script>";
      }
    } else {
      echo "<script>alert('User does not exist, Please try again');</script>";
    }

    mysqli_stmt_close($stmt);
  } else {
    echo "<script>alert('Please Insert Username and Password');</script>";
  }
}

if (!isset($_SESSION['user_logged_in']) && isset($_COOKIE['remember_me'])) {
  $token = $_COOKIE['remember_me'];
  $userId = getUserIdByRememberMeToken($token);

  if ($userId) {
    // Use the user ID to get the user details
    $query = "SELECT * FROM `user` WHERE `user_id`=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
      $userDetails = mysqli_fetch_assoc($result);

      $defaultUserName = $userDetails['user_name'];

      $rememberMeChecked = 'checked';

    }

    mysqli_stmt_close($stmt);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/reset.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/site.css">

  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/container.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/grid.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/header.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/image.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/menu.css">

  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/divider.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/segment.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/form.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/input.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/button.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/list.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/message.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/components/icon.css">
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/semantic.min.css">
  <link rel="stylesheet" href="Css/Main.css">
  <!-- <link rel="stylesheet" type="text/css" href="Main.css"> -->
  <!-- <link rel="stylesheet" href="Utility.css"> -->
  <title>Digital Library</title>
</head>

<body>
<div class="loginBG">
<div clas="starBox">
          <div id="stars"></div>
          <div id="stars2"></div>
          <!-- <div id="stars3"></div> -->
        </div>
<div class="ui login-box">
    <!-- <div class="login-bg">
      <img src="" alt="">
    </div> -->
    <div class="login-form">
      <form class="" action="UserLogin.php" method="POST">
        <div class="login-text">

          <div class="logo">
            <p>LOGIN</p>
          </div>

          <div class="lboxcss">
            <input type="text" class="lbox-input" name="user_name" autocomplete="off"
              value="<?= isset($defaultUserName) ? $defaultUserName : '' ?>" required>
            <label for="text" class="label-name">
              <span class="content-name">
                Username
              </span>
            </label>
          </div>

          <div class="lboxcss" style="margin-top: 20px;">
            <button id="showPasswordButton" type="button">
              <i id="showPasswordIcon" class="eye icon"></i>
            </button>
            <input type="password" id="passwordInput" class="lbox-input" name="user_pass" autocomplete="off" required>
            <label for="passwordInput" class="label-name">
              <span class="content-name">
                Password
              </span>
            </label>
          </div>
          <div class="ui checkbox">
            <input type="checkbox" name="remember_me" <?php echo isset($rememberMeChecked) ? $rememberMeChecked : ''; ?>>
            <label>Remember Me</label>
          </div>
          <a class="forgot-password" href="ForgotPassword.php">Forgot Password</a>

          <div class="login-function">
            <button type='submit' name='submit' value='submit' class="ui black button" id="login-button">Login</button>
            <div class="ui horizontal divider">
              Or
            </div>
            <span>New to us? <a class="signup" href="SignUp.php">Sign Up</a></span>

          </div>

          <!-- <div class="ui error message"></div> -->
        </div>
      </form>
    </div>
  </div>
  <div style="position:absolute; bottom:0; width:100%;">
      <svg id="" preserveAspectRatio="xMidYMax meet" class="svg-separator sep1" viewBox="0 0 1600 160"
        style="z-index:7; background:transparent;" data-height="100">
        <path class="animated-path" style="opacity: 1;fill: #ddd;" d="M1040,56c0.5,0,1,0,1.6,0c-16.6-8.9-36.4-15.7-66.4-15.7c-56,0-76.8,23.7-106.9,41C881.1,89.3,895.6,96,920,96
C979.5,96,980,56,1040,56z"></path>
        <path class="animated-path" style="opacity: 1;" d="M1699.8,96l0,10H1946l-0.3-6.9c0,0,0,0-88,0s-88.6-58.8-176.5-58.8c-51.4,0-73,20.1-99.6,36.8
c14.5,9.6,29.6,18.9,58.4,18.9C1699.8,96,1699.8,96,1699.8,96z"></path>
        <path class="animated-path" style="opacity: 1;" d="M1400,96c19.5,0,32.7-4.3,43.7-10c-35.2-17.3-54.1-45.7-115.5-45.7c-32.3,0-52.8,7.9-70.2,17.8
c6.4-1.3,13.6-2.1,22-2.1C1340.1,56,1340.3,96,1400,96z"></path>
        <path class="animated-path" style="opacity: 1;" d="M320,56c6.6,0,12.4,0.5,17.7,1.3c-17-9.6-37.3-17-68.5-17c-60.4,0-79.5,27.8-114,45.2
c11.2,6,24.6,10.5,44.8,10.5C260,96,259.9,56,320,56z"></path>
        <path class="animated-path" style="opacity: 1;" d="M680,96c23.7,0,38.1-6.3,50.5-13.9C699.6,64.8,679,40.3,622.2,40.3c-30,0-49.8,6.8-66.3,15.8
c1.3,0,2.7-0.1,4.1-0.1C619.7,56,620.2,96,680,96z"></path>
        <path class="animated-path" style="opacity: 1;" d="M-40,95.6c28.3,0,43.3-8.7,57.4-18C-9.6,60.8-31,40.2-83.2,40.2c-14.3,0-26.3,1.6-36.8,4.2V106h60V96L-40,95.6
z"></path>
        <path class="animated-path" style="opacity: 1;" d="M504,73.4c-2.6-0.8-5.7-1.4-9.6-1.4c-19.4,0-19.6,13-39,13c-19.4,0-19.5-13-39-13c-14,0-18,6.7-26.3,10.4
C402.4,89.9,416.7,96,440,96C472.5,96,487.5,84.2,504,73.4z"></path>
        <path class="animated-path" style="opacity: 1;" d="M1205.4,85c-0.2,0-0.4,0-0.6,0c-19.5,0-19.5-13-39-13s-19.4,12.9-39,12.9c0,0-5.9,0-12.3,0.1
c11.4,6.3,24.9,11,45.5,11C1180.6,96,1194.1,91.2,1205.4,85z"></path>
        <path class="animated-path" style="opacity: 1;" d="M1447.4,83.9c-2.4,0.7-5.2,1.1-8.6,1.1c-19.3,0-19.6-13-39-13s-19.6,13-39,13c-3,0-5.5-0.3-7.7-0.8
c11.6,6.6,25.4,11.8,46.9,11.8C1421.8,96,1435.7,90.7,1447.4,83.9z"></path>
        <path class="animated-path" style="opacity: 1;" d="M985.8,72c-17.6,0.8-18.3,13-37,13c-19.4,0-19.5-13-39-13c-18.2,0-19.6,11.4-35.5,12.8
c11.4,6.3,25,11.2,45.7,11.2C953.7,96,968.5,83.2,985.8,72z"></path>
        <path class="animated-path" style="opacity: 1;" d="M743.8,73.5c-10.3,3.4-13.6,11.5-29,11.5c-19.4,0-19.5-13-39-13s-19.5,13-39,13c-0.9,0-1.7,0-2.5-0.1
c11.4,6.3,25,11.1,45.7,11.1C712.4,96,727.3,84.2,743.8,73.5z"></path>
        <path class="animated-path" style="opacity: 1;" d="M265.5,72.3c-1.5-0.2-3.2-0.3-5.1-0.3c-19.4,0-19.6,13-39,13c-19.4,0-19.6-13-39-13
c-15.9,0-18.9,8.7-30.1,11.9C164.1,90.6,178,96,200,96C233.7,96,248.4,83.4,265.5,72.3z"></path>
        <path class="animated-path" style="opacity: 1;" d="M1692.3,96V85c0,0,0,0-19.5,0s-19.6-13-39-13s-19.6,13-39,13c-0.1,0-0.2,0-0.4,0c11.4,6.2,24.9,11,45.6,11
C1669.9,96,1684.8,96,1692.3,96z"></path>
        <path class="animated-path" style="opacity: 1;"
          d="M25.5,72C6,72,6.1,84.9-13.5,84.9L-20,85v8.9C0.7,90.1,12.6,80.6,25.9,72C25.8,72,25.7,72,25.5,72z"></path>
        <path class="animated-path" style="" d="M-40,95.6C20.3,95.6,20.1,56,80,56s60,40,120,40s59.9-40,120-40s60.3,40,120,40s60.3-40,120-40
s60.2,40,120,40s60.1-40,120-40s60.5,40,120,40s60-40,120-40s60.4,40,120,40s59.9-40,120-40s60.3,40,120,40s60.2-40,120-40
s60.2,40,120,40s59.8,0,59.8,0l0.2,143H-60V96L-40,95.6z"></path>

        <animate attributeName="d" dur="3s" repeatCount="indefinite"
          values="M10 80 Q 95 10 180 80; M10 80 Q 180 150 320 80; M10 80 Q 95 10 180 80" />
      </svg>

    </div>
</div>
<!-- <div style="height:500px; width:100%; background:#ddd; z-index:10;">

</div>
  -->

</body>

</html>
<script>
  const passwordInput = document.getElementById("passwordInput");
  const showPasswordButton = document.getElementById("showPasswordButton");
  const showPasswordIcon = document.getElementById("showPasswordIcon");

  showPasswordButton.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      showPasswordIcon.className = "eye slash icon";
    } else {
      passwordInput.type = "password";
      showPasswordIcon.className = "eye icon";
    }
  });
</script>
<style type="text/css">
/* ............................................................................................ */
/* no need add to main css */
@keyframes move {
    0% {
      transform: translateX(0);
    }

    50% {
      transform: translateX(-90px);
    }

    100% {
      transform: translateX(0);
    }
  }

  .animated-path {
    animation: move 16s ease-in-out infinite;
  }


  .svg-separator {
    display: block;
    background: 0 0;
    position: relative;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 20;
    -webkit-transform: translateY(0%) translateY(2px);
    transform: translateY(0%) translateY(2px);
    width: 100%;
    filter: unset;
  }

  .svg-separator path {
    fill: #F5F7F8 !important;
  }

  .svg-separator.bottom {
    top: auto;
    bottom: 0;
  }

  .sep1 {
    transform: translateY(0%) translateY(0px) scale(1, 1);
    transform-origin: top;
  }


  /* star */
  .starBox{
  z-index:3;
}

  #stars {
    width: 1px;
    height: 1px;
    background: transparent;
    box-shadow: 1602px 661px #FFF, 1779px 795px #FFF, 1154px 812px #FFF, 1328px 800px #FFF, 1262px 1251px #FFF, 1976px 432px #FFF, 1650px 1853px #FFF, 1001px 468px #FFF, 609px 405px #FFF, 1842px 1576px #FFF, 742px 1365px #FFF, 321px 1286px #FFF, 462px 926px #FFF, 481px 458px #FFF, 484px 1952px #FFF, 380px 1267px #FFF, 1122px 820px #FFF, 1626px 1217px #FFF, 1953px 1255px #FFF, 1728px 140px #FFF, 1430px 1262px #FFF, 7px 463px #FFF, 1664px 338px #FFF, 63px 11px #FFF, 552px 438px #FFF, 3px 783px #FFF, 1247px 361px #FFF, 803px 121px #FFF, 755px 1435px #FFF, 659px 1572px #FFF, 462px 256px #FFF, 654px 1979px #FFF, 1747px 1521px #FFF, 1222px 1922px #FFF, 1615px 1672px #FFF, 980px 918px #FFF, 1477px 1509px #FFF, 1311px 365px #FFF, 286px 1255px #FFF, 897px 1108px #FFF, 770px 330px #FFF, 337px 598px #FFF, 1192px 711px #FFF, 1656px 1284px #FFF, 1808px 543px #FFF, 1099px 608px #FFF, 1215px 157px #FFF, 1670px 748px #FFF, 110px 734px #FFF, 1513px 1678px #FFF, 137px 1969px #FFF, 242px 1029px #FFF, 670px 606px #FFF, 1173px 1915px #FFF, 1730px 1946px #FFF, 1617px 1395px #FFF, 294px 1214px #FFF, 942px 1551px #FFF, 327px 885px #FFF, 1961px 128px #FFF, 314px 333px #FFF, 845px 1457px #FFF, 1293px 408px #FFF, 1058px 582px #FFF, 1981px 1771px #FFF, 1473px 311px #FFF, 1227px 955px #FFF, 1937px 1262px #FFF, 754px 624px #FFF, 266px 619px #FFF, 182px 1621px #FFF, 29px 802px #FFF, 1075px 298px #FFF, 1986px 1900px #FFF, 1770px 447px #FFF, 1291px 57px #FFF, 782px 1535px #FFF, 1312px 111px #FFF, 1194px 651px #FFF, 899px 193px #FFF, 282px 163px #FFF, 1341px 998px #FFF, 379px 1540px #FFF, 406px 1584px #FFF, 1227px 223px #FFF, 1525px 402px #FFF, 66px 1842px #FFF, 1531px 1873px #FFF, 1303px 376px #FFF, 1949px 1797px #FFF, 1093px 1018px #FFF, 937px 886px #FFF, 1954px 1675px #FFF, 1957px 304px #FFF, 74px 400px #FFF, 556px 1123px #FFF, 1659px 1330px #FFF, 1110px 916px #FFF, 503px 271px #FFF, 1574px 1851px #FFF, 436px 1833px #FFF, 175px 1150px #FFF, 43px 1691px #FFF, 1758px 1857px #FFF, 413px 257px #FFF, 596px 336px #FFF, 1608px 338px #FFF, 333px 108px #FFF, 794px 1318px #FFF, 1150px 73px #FFF, 891px 1149px #FFF, 1614px 387px #FFF, 1930px 1770px #FFF, 465px 591px #FFF, 394px 472px #FFF, 438px 1877px #FFF, 537px 1345px #FFF, 583px 470px #FFF, 1493px 1112px #FFF, 1437px 1380px #FFF, 494px 1304px #FFF, 17px 1388px #FFF, 1001px 1558px #FFF, 10px 978px #FFF, 25px 1053px #FFF, 732px 576px #FFF, 1169px 297px #FFF, 915px 1519px #FFF, 280px 1149px #FFF, 1430px 761px #FFF, 45px 208px #FFF, 1790px 1104px #FFF, 953px 296px #FFF, 1480px 733px #FFF, 1024px 1171px #FFF, 1473px 1335px #FFF, 631px 329px #FFF, 1498px 1792px #FFF, 385px 1825px #FFF, 483px 781px #FFF, 1888px 85px #FFF, 1659px 715px #FFF, 192px 177px #FFF, 275px 1243px #FFF, 1203px 1087px #FFF, 412px 857px #FFF, 1302px 1270px #FFF, 787px 389px #FFF, 751px 1838px #FFF, 1381px 1377px #FFF, 196px 878px #FFF, 134px 277px #FFF, 145px 549px #FFF, 1957px 1961px #FFF, 1120px 1050px #FFF, 1354px 972px #FFF, 1158px 1771px #FFF, 47px 352px #FFF, 1112px 584px #FFF, 164px 1386px #FFF, 1273px 1782px #FFF, 110px 780px #FFF, 502px 299px #FFF, 1768px 933px #FFF, 886px 1287px #FFF, 1309px 1130px #FFF, 847px 776px #FFF, 1713px 269px #FFF, 1718px 795px #FFF, 1984px 717px #FFF, 985px 341px #FFF, 1462px 1078px #FFF, 1702px 1461px #FFF, 167px 1873px #FFF, 862px 1117px #FFF, 1968px 932px #FFF, 814px 91px #FFF, 1530px 607px #FFF, 1799px 1571px #FFF, 1868px 1734px #FFF, 1800px 1137px #FFF, 52px 178px #FFF, 901px 853px #FFF, 1181px 1232px #FFF, 209px 650px #FFF, 1165px 1321px #FFF, 1046px 159px #FFF, 578px 1748px #FFF, 18px 337px #FFF, 70px 1062px #FFF, 1670px 270px #FFF, 1828px 1321px #FFF, 1817px 336px #FFF, 124px 1935px #FFF, 1840px 594px #FFF, 836px 1194px #FFF, 1292px 1801px #FFF, 1547px 945px #FFF, 500px 1563px #FFF, 569px 376px #FFF, 1107px 1627px #FFF, 662px 717px #FFF, 1352px 762px #FFF, 771px 1694px #FFF, 1529px 484px #FFF, 1609px 798px #FFF, 652px 627px #FFF, 498px 20px #FFF, 1308px 164px #FFF, 245px 1239px #FFF, 1131px 860px #FFF, 1028px 333px #FFF, 765px 1760px #FFF, 1627px 684px #FFF, 70px 1586px #FFF, 63px 1230px #FFF, 1727px 465px #FFF, 1946px 1862px #FFF, 169px 956px #FFF, 121px 1826px #FFF, 1615px 725px #FFF, 1225px 1925px #FFF, 1678px 323px #FFF, 606px 371px #FFF, 616px 985px #FFF, 490px 1898px #FFF, 516px 1019px #FFF, 878px 1448px #FFF, 1533px 98px #FFF, 625px 1152px #FFF, 224px 522px #FFF, 215px 245px #FFF, 153px 669px #FFF, 1530px 1652px #FFF, 1796px 1697px #FFF, 908px 1456px #FFF, 1326px 1947px #FFF, 1294px 872px #FFF, 806px 1296px #FFF, 783px 556px #FFF, 1075px 973px #FFF, 613px 505px #FFF, 1160px 833px #FFF, 1053px 1793px #FFF, 1343px 1990px #FFF, 937px 254px #FFF, 1084px 234px #FFF, 575px 374px #FFF, 367px 1656px #FFF, 494px 510px #FFF, 1403px 1242px #FFF, 1827px 1741px #FFF, 1239px 616px #FFF, 579px 1670px #FFF, 971px 836px #FFF, 1025px 813px #FFF, 707px 1407px #FFF, 188px 1777px #FFF, 1576px 18px #FFF, 1px 533px #FFF, 1123px 589px #FFF, 88px 705px #FFF, 1844px 679px #FFF, 121px 350px #FFF, 1853px 470px #FFF, 1333px 263px #FFF, 1702px 957px #FFF, 475px 725px #FFF, 1650px 75px #FFF, 1372px 11px #FFF, 714px 353px #FFF, 968px 461px #FFF, 1413px 1400px #FFF, 1856px 1724px #FFF, 793px 1524px #FFF, 1717px 962px #FFF, 1263px 1567px #FFF, 1621px 1961px #FFF, 537px 243px #FFF, 912px 1140px #FFF, 659px 1300px #FFF, 113px 516px #FFF, 1111px 1738px #FFF, 336px 953px #FFF, 1038px 248px #FFF, 692px 935px #FFF, 516px 1451px #FFF, 1057px 401px #FFF, 1014px 388px #FFF, 1363px 1764px #FFF, 855px 745px #FFF, 57px 767px #FFF, 779px 1263px #FFF, 1746px 1797px #FFF, 1975px 848px #FFF, 1051px 941px #FFF, 79px 1146px #FFF, 1945px 1015px #FFF, 1506px 1855px #FFF, 955px 730px #FFF, 27px 1865px #FFF, 1378px 1691px #FFF, 969px 1738px #FFF, 206px 842px #FFF, 1068px 91px #FFF, 1466px 1725px #FFF, 533px 1091px #FFF, 1205px 748px #FFF, 324px 398px #FFF, 1417px 1048px #FFF, 349px 1827px #FFF, 730px 1048px #FFF, 1671px 690px #FFF, 1441px 1068px #FFF, 251px 925px #FFF, 144px 1631px #FFF, 108px 1524px #FFF, 187px 1172px #FFF, 183px 403px #FFF, 907px 1873px #FFF, 973px 104px #FFF, 65px 1390px #FFF, 372px 1486px #FFF, 424px 765px #FFF, 1410px 350px #FFF, 760px 827px #FFF, 1179px 612px #FFF, 251px 1106px #FFF, 657px 851px #FFF, 63px 925px #FFF, 1272px 1791px #FFF, 1361px 1189px #FFF, 778px 1101px #FFF, 817px 1436px #FFF, 1884px 1440px #FFF, 1662px 1781px #FFF, 276px 990px #FFF, 1835px 1617px #FFF, 1516px 246px #FFF, 544px 1792px #FFF, 667px 1652px #FFF, 1142px 1221px #FFF, 1417px 38px #FFF, 33px 1467px #FFF, 1087px 1608px #FFF, 1406px 1323px #FFF, 440px 1564px #FFF, 1697px 758px #FFF, 743px 340px #FFF, 1244px 218px #FFF, 1382px 1783px #FFF, 539px 1285px #FFF, 273px 592px #FFF, 1268px 444px #FFF, 1302px 70px #FFF, 989px 478px #FFF, 1044px 1477px #FFF, 395px 803px #FFF, 1609px 1799px #FFF, 999px 100px #FFF, 1444px 2000px #FFF, 1966px 1675px #FFF, 51px 822px #FFF, 1387px 825px #FFF, 983px 440px #FFF, 1269px 624px #FFF, 1303px 1455px #FFF, 666px 313px #FFF, 1776px 132px #FFF, 1041px 1523px #FFF, 168px 1599px #FFF, 1714px 604px #FFF, 657px 594px #FFF, 1301px 1318px #FFF, 961px 541px #FFF, 310px 422px #FFF, 636px 1847px #FFF, 814px 1005px #FFF, 444px 1093px #FFF, 583px 1551px #FFF, 1746px 1807px #FFF, 1550px 1359px #FFF, 283px 1837px #FFF, 1257px 1772px #FFF, 1620px 1691px #FFF, 727px 1180px #FFF, 798px 1744px #FFF, 754px 1707px #FFF, 1871px 189px #FFF, 1355px 1000px #FFF, 1839px 599px #FFF, 7px 1689px #FFF, 775px 966px #FFF, 231px 1980px #FFF, 1231px 717px #FFF, 1748px 1658px #FFF, 385px 1352px #FFF, 1168px 182px #FFF, 576px 997px #FFF, 1955px 279px #FFF, 314px 1162px #FFF, 626px 1525px #FFF, 1372px 597px #FFF, 168px 809px #FFF, 1559px 1646px #FFF, 1316px 600px #FFF, 1903px 1615px #FFF, 1899px 173px #FFF, 1936px 1042px #FFF, 977px 180px #FFF, 80px 1132px #FFF, 1258px 287px #FFF, 1944px 1624px #FFF, 1710px 1271px #FFF, 859px 315px #FFF, 658px 601px #FFF, 444px 516px #FFF, 1443px 70px #FFF, 61px 1468px #FFF, 67px 338px #FFF, 1888px 711px #FFF, 1838px 1848px #FFF, 1252px 774px #FFF, 1986px 1392px #FFF, 1734px 213px #FFF, 1904px 76px #FFF, 1503px 1882px #FFF, 268px 113px #FFF, 1124px 387px #FFF, 565px 1591px #FFF, 446px 1116px #FFF, 1861px 661px #FFF, 55px 1090px #FFF, 328px 837px #FFF, 775px 85px #FFF, 436px 632px #FFF, 1704px 904px #FFF, 406px 1068px #FFF, 1077px 719px #FFF, 125px 722px #FFF, 1735px 174px #FFF, 1541px 1590px #FFF, 1883px 498px #FFF, 733px 1195px #FFF, 1178px 1793px #FFF, 254px 719px #FFF, 1790px 545px #FFF, 799px 454px #FFF, 1076px 1737px #FFF, 360px 95px #FFF, 516px 1367px #FFF, 1016px 493px #FFF, 1340px 15px #FFF, 1843px 355px #FFF, 416px 1857px #FFF, 1745px 1967px #FFF, 1768px 1266px #FFF, 778px 1437px #FFF, 1892px 1480px #FFF, 1861px 1700px #FFF, 1973px 832px #FFF, 132px 1773px #FFF, 1928px 1974px #FFF, 470px 1852px #FFF, 1855px 463px #FFF, 892px 1018px #FFF, 374px 1670px #FFF, 140px 391px #FFF, 1868px 1945px #FFF, 505px 1988px #FFF, 1798px 1514px #FFF, 134px 873px #FFF, 1319px 1278px #FFF, 1796px 1403px #FFF, 1400px 503px #FFF, 480px 1004px #FFF, 53px 434px #FFF, 1075px 507px #FFF, 1376px 13px #FFF, 150px 623px #FFF, 563px 1355px #FFF, 1464px 858px #FFF, 1123px 967px #FFF, 1273px 1412px #FFF, 1378px 1689px #FFF, 1390px 1248px #FFF, 273px 191px #FFF, 471px 46px #FFF, 1520px 780px #FFF, 830px 530px #FFF, 1369px 1915px #FFF, 434px 1541px #FFF, 1658px 753px #FFF, 1955px 1908px #FFF, 409px 1106px #FFF, 1609px 597px #FFF, 637px 1671px #FFF, 1957px 475px #FFF, 1595px 1496px #FFF, 1979px 1958px #FFF, 1364px 1658px #FFF, 929px 425px #FFF, 1197px 967px #FFF, 82px 1164px #FFF, 310px 176px #FFF, 1242px 1822px #FFF, 1081px 484px #FFF, 1436px 1211px #FFF, 992px 1826px #FFF, 1801px 714px #FFF, 1034px 16px #FFF, 1241px 145px #FFF, 1815px 811px #FFF, 517px 111px #FFF, 512px 1871px #FFF, 185px 1024px #FFF, 869px 1606px #FFF, 904px 1418px #FFF, 271px 807px #FFF, 1627px 1971px #FFF, 1048px 200px #FFF, 1543px 152px #FFF, 1559px 924px #FFF, 1205px 1281px #FFF, 1073px 697px #FFF, 1614px 426px #FFF, 1064px 1329px #FFF, 1963px 1252px #FFF, 444px 992px #FFF, 553px 1688px #FFF, 298px 578px #FFF, 772px 636px #FFF, 1755px 1446px #FFF, 1503px 450px #FFF, 504px 135px #FFF, 1714px 314px #FFF, 447px 1165px #FFF, 1903px 1258px #FFF, 1916px 434px #FFF, 1468px 1851px #FFF, 631px 244px #FFF, 201px 1060px #FFF, 88px 340px #FFF, 342px 1039px #FFF, 699px 488px #FFF, 1284px 1794px #FFF, 1458px 759px #FFF, 1335px 1629px #FFF, 1367px 1363px #FFF, 185px 235px #FFF, 76px 1315px #FFF, 1528px 198px #FFF, 471px 1165px #FFF, 1160px 1077px #FFF, 79px 473px #FFF, 114px 654px #FFF, 1400px 722px #FFF, 829px 1532px #FFF, 448px 941px #FFF, 627px 1987px #FFF, 820px 919px #FFF, 1506px 217px #FFF, 1946px 1562px #FFF, 142px 1489px #FFF, 793px 602px #FFF, 441px 91px #FFF, 259px 453px #FFF, 1538px 1732px #FFF, 1498px 1501px #FFF, 656px 805px #FFF, 1808px 1178px #FFF, 1954px 890px #FFF, 656px 1605px #FFF, 1177px 350px #FFF, 666px 992px #FFF, 1486px 305px #FFF, 661px 1647px #FFF, 1209px 1168px #FFF, 1499px 1450px #FFF, 554px 98px #FFF, 744px 1047px #FFF, 2px 524px #FFF, 383px 1086px #FFF, 1822px 244px #FFF, 1704px 1714px #FFF, 797px 1527px #FFF, 1916px 1610px #FFF, 1865px 860px #FFF, 1567px 1823px #FFF, 1340px 273px #FFF, 1133px 168px #FFF, 557px 1978px #FFF, 1419px 1724px #FFF, 668px 538px #FFF, 1411px 1026px #FFF, 1194px 1431px #FFF, 267px 1899px #FFF, 556px 950px #FFF, 1847px 1514px #FFF, 255px 131px #FFF, 442px 441px #FFF, 1089px 1011px #FFF, 1931px 1781px #FFF, 187px 119px #FFF, 1317px 1797px #FFF, 15px 971px #FFF, 1542px 618px #FFF, 594px 1216px #FFF, 523px 1980px #FFF, 682px 1977px #FFF, 1840px 1934px #FFF, 73px 1975px #FFF, 192px 37px #FFF, 1796px 1491px #FFF, 237px 418px #FFF, 635px 1936px #FFF, 735px 421px #FFF, 1785px 1564px #FFF, 85px 1401px #FFF, 288px 1436px #FFF, 972px 1682px #FFF, 986px 206px #FFF, 730px 1225px #FFF, 569px 557px #FFF, 1805px 1898px #FFF, 488px 1963px #FFF, 877px 997px #FFF, 545px 1029px #FFF, 1725px 1017px #FFF, 1320px 1855px #FFF, 1220px 409px #FFF, 1911px 453px #FFF, 784px 636px #FFF, 570px 862px #FFF, 1009px 7px #FFF, 432px 1640px #FFF, 1941px 1197px #FFF, 422px 994px #FFF, 465px 1879px #FFF, 918px 200px #FFF, 1978px 1313px #FFF, 520px 954px #FFF, 1270px 1594px #FFF, 1087px 578px #FFF, 1597px 58px #FFF, 1782px 550px #FFF, 274px 204px #FFF, 1743px 1662px #FFF, 441px 1338px #FFF, 545px 1844px #FFF, 1702px 894px #FFF, 1760px 1426px #FFF, 795px 1068px #FFF, 1076px 373px #FFF, 1666px 1594px #FFF, 1490px 1257px #FFF, 7px 1967px #FFF, 357px 1354px #FFF, 1740px 1071px #FFF, 1876px 1981px #FFF, 421px 1758px #FFF, 750px 1071px #FFF, 963px 266px #FFF, 918px 1422px #FFF, 811px 463px #FFF, 1084px 1186px #FFF, 950px 1852px #FFF, 709px 916px #FFF, 37px 1612px #FFF, 777px 747px #FFF, 749px 1663px #FFF, 1036px 600px #FFF, 1951px 1697px #FFF, 1208px 1139px #FFF, 549px 400px #FFF, 1144px 584px #FFF, 1466px 1315px #FFF, 131px 826px #FFF, 172px 1097px #FFF, 253px 97px #FFF, 197px 422px #FFF, 792px 25px #FFF, 957px 1632px #FFF, 1209px 889px #FFF, 826px 638px #FFF, 1464px 1137px #FFF, 1189px 2px #FFF, 1737px 1514px #FFF, 142px 954px #FFF, 1831px 544px #FFF, 1008px 779px #FFF, 1889px 257px #FFF, 1006px 1085px #FFF, 428px 1043px #FFF, 1915px 1585px #FFF, 232px 1935px #FFF, 767px 706px #FFF, 431px 334px #FFF, 897px 15px #FFF, 705px 146px #FFF;
    animation: animStar 80s linear infinite;
  }

  #stars:after {
    content: " ";
    position: absolute;
    top: 200px;
    width: 1px;
    height: 1px;
    background: transparent;
    box-shadow: 1602px 661px #FFF, 1779px 795px #FFF, 1154px 812px #FFF, 1328px 800px #FFF, 1262px 1251px #FFF, 1976px 432px #FFF, 1650px 1853px #FFF, 1001px 468px #FFF, 609px 405px #FFF, 1842px 1576px #FFF, 742px 1365px #FFF, 321px 1286px #FFF, 462px 926px #FFF, 481px 458px #FFF, 484px 1952px #FFF, 380px 1267px #FFF, 1122px 820px #FFF, 1626px 1217px #FFF, 1953px 1255px #FFF, 1728px 140px #FFF, 1430px 1262px #FFF, 7px 463px #FFF, 1664px 338px #FFF, 63px 11px #FFF, 552px 438px #FFF, 3px 783px #FFF, 1247px 361px #FFF, 803px 121px #FFF, 755px 1435px #FFF, 659px 1572px #FFF, 462px 256px #FFF, 654px 1979px #FFF, 1747px 1521px #FFF, 1222px 1922px #FFF, 1615px 1672px #FFF, 980px 918px #FFF, 1477px 1509px #FFF, 1311px 365px #FFF, 286px 1255px #FFF, 897px 1108px #FFF, 770px 330px #FFF, 337px 598px #FFF, 1192px 711px #FFF, 1656px 1284px #FFF, 1808px 543px #FFF, 1099px 608px #FFF, 1215px 157px #FFF, 1670px 748px #FFF, 110px 734px #FFF, 1513px 1678px #FFF, 137px 1969px #FFF, 242px 1029px #FFF, 670px 606px #FFF, 1173px 1915px #FFF, 1730px 1946px #FFF, 1617px 1395px #FFF, 294px 1214px #FFF, 942px 1551px #FFF, 327px 885px #FFF, 1961px 128px #FFF, 314px 333px #FFF, 845px 1457px #FFF, 1293px 408px #FFF, 1058px 582px #FFF, 1981px 1771px #FFF, 1473px 311px #FFF, 1227px 955px #FFF, 1937px 1262px #FFF, 754px 624px #FFF, 266px 619px #FFF, 182px 1621px #FFF, 29px 802px #FFF, 1075px 298px #FFF, 1986px 1900px #FFF, 1770px 447px #FFF, 1291px 57px #FFF, 782px 1535px #FFF, 1312px 111px #FFF, 1194px 651px #FFF, 899px 193px #FFF, 282px 163px #FFF, 1341px 998px #FFF, 379px 1540px #FFF, 406px 1584px #FFF, 1227px 223px #FFF, 1525px 402px #FFF, 66px 1842px #FFF, 1531px 1873px #FFF, 1303px 376px #FFF, 1949px 1797px #FFF, 1093px 1018px #FFF, 937px 886px #FFF, 1954px 1675px #FFF, 1957px 304px #FFF, 74px 400px #FFF, 556px 1123px #FFF, 1659px 1330px #FFF, 1110px 916px #FFF, 503px 271px #FFF, 1574px 1851px #FFF, 436px 1833px #FFF, 175px 1150px #FFF, 43px 1691px #FFF, 1758px 1857px #FFF, 413px 257px #FFF, 596px 336px #FFF, 1608px 338px #FFF, 333px 108px #FFF, 794px 1318px #FFF, 1150px 73px #FFF, 891px 1149px #FFF, 1614px 387px #FFF, 1930px 1770px #FFF, 465px 591px #FFF, 394px 472px #FFF, 438px 1877px #FFF, 537px 1345px #FFF, 583px 470px #FFF, 1493px 1112px #FFF, 1437px 1380px #FFF, 494px 1304px #FFF, 17px 1388px #FFF, 1001px 1558px #FFF, 10px 978px #FFF, 25px 1053px #FFF, 732px 576px #FFF, 1169px 297px #FFF, 915px 1519px #FFF, 280px 1149px #FFF, 1430px 761px #FFF, 45px 208px #FFF, 1790px 1104px #FFF, 953px 296px #FFF, 1480px 733px #FFF, 1024px 1171px #FFF, 1473px 1335px #FFF, 631px 329px #FFF, 1498px 1792px #FFF, 385px 1825px #FFF, 483px 781px #FFF, 1888px 85px #FFF, 1659px 715px #FFF, 192px 177px #FFF, 275px 1243px #FFF, 1203px 1087px #FFF, 412px 857px #FFF, 1302px 1270px #FFF, 787px 389px #FFF, 751px 1838px #FFF, 1381px 1377px #FFF, 196px 878px #FFF, 134px 277px #FFF, 145px 549px #FFF, 1957px 1961px #FFF, 1120px 1050px #FFF, 1354px 972px #FFF, 1158px 1771px #FFF, 47px 352px #FFF, 1112px 584px #FFF, 164px 1386px #FFF, 1273px 1782px #FFF, 110px 780px #FFF, 502px 299px #FFF, 1768px 933px #FFF, 886px 1287px #FFF, 1309px 1130px #FFF, 847px 776px #FFF, 1713px 269px #FFF, 1718px 795px #FFF, 1984px 717px #FFF, 985px 341px #FFF, 1462px 1078px #FFF, 1702px 1461px #FFF, 167px 1873px #FFF, 862px 1117px #FFF, 1968px 932px #FFF, 814px 91px #FFF, 1530px 607px #FFF, 1799px 1571px #FFF, 1868px 1734px #FFF, 1800px 1137px #FFF, 52px 178px #FFF, 901px 853px #FFF, 1181px 1232px #FFF, 209px 650px #FFF, 1165px 1321px #FFF, 1046px 159px #FFF, 578px 1748px #FFF, 18px 337px #FFF, 70px 1062px #FFF, 1670px 270px #FFF, 1828px 1321px #FFF, 1817px 336px #FFF, 124px 1935px #FFF, 1840px 594px #FFF, 836px 1194px #FFF, 1292px 1801px #FFF, 1547px 945px #FFF, 500px 1563px #FFF, 569px 376px #FFF, 1107px 1627px #FFF, 662px 717px #FFF, 1352px 762px #FFF, 771px 1694px #FFF, 1529px 484px #FFF, 1609px 798px #FFF, 652px 627px #FFF, 498px 20px #FFF, 1308px 164px #FFF, 245px 1239px #FFF, 1131px 860px #FFF, 1028px 333px #FFF, 765px 1760px #FFF, 1627px 684px #FFF, 70px 1586px #FFF, 63px 1230px #FFF, 1727px 465px #FFF, 1946px 1862px #FFF, 169px 956px #FFF, 121px 1826px #FFF, 1615px 725px #FFF, 1225px 1925px #FFF, 1678px 323px #FFF, 606px 371px #FFF, 616px 985px #FFF, 490px 1898px #FFF, 516px 1019px #FFF, 878px 1448px #FFF, 1533px 98px #FFF, 625px 1152px #FFF, 224px 522px #FFF, 215px 245px #FFF, 153px 669px #FFF, 1530px 1652px #FFF, 1796px 1697px #FFF, 908px 1456px #FFF, 1326px 1947px #FFF, 1294px 872px #FFF, 806px 1296px #FFF, 783px 556px #FFF, 1075px 973px #FFF, 613px 505px #FFF, 1160px 833px #FFF, 1053px 1793px #FFF, 1343px 1990px #FFF, 937px 254px #FFF, 1084px 234px #FFF, 575px 374px #FFF, 367px 1656px #FFF, 494px 510px #FFF, 1403px 1242px #FFF, 1827px 1741px #FFF, 1239px 616px #FFF, 579px 1670px #FFF, 971px 836px #FFF, 1025px 813px #FFF, 707px 1407px #FFF, 188px 1777px #FFF, 1576px 18px #FFF, 1px 533px #FFF, 1123px 589px #FFF, 88px 705px #FFF, 1844px 679px #FFF, 121px 350px #FFF, 1853px 470px #FFF, 1333px 263px #FFF, 1702px 957px #FFF, 475px 725px #FFF, 1650px 75px #FFF, 1372px 11px #FFF, 714px 353px #FFF, 968px 461px #FFF, 1413px 1400px #FFF, 1856px 1724px #FFF, 793px 1524px #FFF, 1717px 962px #FFF, 1263px 1567px #FFF, 1621px 1961px #FFF, 537px 243px #FFF, 912px 1140px #FFF, 659px 1300px #FFF, 113px 516px #FFF, 1111px 1738px #FFF, 336px 953px #FFF, 1038px 248px #FFF, 692px 935px #FFF, 516px 1451px #FFF, 1057px 401px #FFF, 1014px 388px #FFF, 1363px 1764px #FFF, 855px 745px #FFF, 57px 767px #FFF, 779px 1263px #FFF, 1746px 1797px #FFF, 1975px 848px #FFF, 1051px 941px #FFF, 79px 1146px #FFF, 1945px 1015px #FFF, 1506px 1855px #FFF, 955px 730px #FFF, 27px 1865px #FFF, 1378px 1691px #FFF, 969px 1738px #FFF, 206px 842px #FFF, 1068px 91px #FFF, 1466px 1725px #FFF, 533px 1091px #FFF, 1205px 748px #FFF, 324px 398px #FFF, 1417px 1048px #FFF, 349px 1827px #FFF, 730px 1048px #FFF, 1671px 690px #FFF, 1441px 1068px #FFF, 251px 925px #FFF, 144px 1631px #FFF, 108px 1524px #FFF, 187px 1172px #FFF, 183px 403px #FFF, 907px 1873px #FFF, 973px 104px #FFF, 65px 1390px #FFF, 372px 1486px #FFF, 424px 765px #FFF, 1410px 350px #FFF, 760px 827px #FFF, 1179px 612px #FFF, 251px 1106px #FFF, 657px 851px #FFF, 63px 925px #FFF, 1272px 1791px #FFF, 1361px 1189px #FFF, 778px 1101px #FFF, 817px 1436px #FFF, 1884px 1440px #FFF, 1662px 1781px #FFF, 276px 990px #FFF, 1835px 1617px #FFF, 1516px 246px #FFF, 544px 1792px #FFF, 667px 1652px #FFF, 1142px 1221px #FFF, 1417px 38px #FFF, 33px 1467px #FFF, 1087px 1608px #FFF, 1406px 1323px #FFF, 440px 1564px #FFF, 1697px 758px #FFF, 743px 340px #FFF, 1244px 218px #FFF, 1382px 1783px #FFF, 539px 1285px #FFF, 273px 592px #FFF, 1268px 444px #FFF, 1302px 70px #FFF, 989px 478px #FFF, 1044px 1477px #FFF, 395px 803px #FFF, 1609px 1799px #FFF, 999px 100px #FFF, 1444px 2000px #FFF, 1966px 1675px #FFF, 51px 822px #FFF, 1387px 825px #FFF, 983px 440px #FFF, 1269px 624px #FFF, 1303px 1455px #FFF, 666px 313px #FFF, 1776px 132px #FFF, 1041px 1523px #FFF, 168px 1599px #FFF, 1714px 604px #FFF, 657px 594px #FFF, 1301px 1318px #FFF, 961px 541px #FFF, 310px 422px #FFF, 636px 1847px #FFF, 814px 1005px #FFF, 444px 1093px #FFF, 583px 1551px #FFF, 1746px 1807px #FFF, 1550px 1359px #FFF, 283px 1837px #FFF, 1257px 1772px #FFF, 1620px 1691px #FFF, 727px 1180px #FFF, 798px 1744px #FFF, 754px 1707px #FFF, 1871px 189px #FFF, 1355px 1000px #FFF, 1839px 599px #FFF, 7px 1689px #FFF, 775px 966px #FFF, 231px 1980px #FFF, 1231px 717px #FFF, 1748px 1658px #FFF, 385px 1352px #FFF, 1168px 182px #FFF, 576px 997px #FFF, 1955px 279px #FFF, 314px 1162px #FFF, 626px 1525px #FFF, 1372px 597px #FFF, 168px 809px #FFF, 1559px 1646px #FFF, 1316px 600px #FFF, 1903px 1615px #FFF, 1899px 173px #FFF, 1936px 1042px #FFF, 977px 180px #FFF, 80px 1132px #FFF, 1258px 287px #FFF, 1944px 1624px #FFF, 1710px 1271px #FFF, 859px 315px #FFF, 658px 601px #FFF, 444px 516px #FFF, 1443px 70px #FFF, 61px 1468px #FFF, 67px 338px #FFF, 1888px 711px #FFF, 1838px 1848px #FFF, 1252px 774px #FFF, 1986px 1392px #FFF, 1734px 213px #FFF, 1904px 76px #FFF, 1503px 1882px #FFF, 268px 113px #FFF, 1124px 387px #FFF, 565px 1591px #FFF, 446px 1116px #FFF, 1861px 661px #FFF, 55px 1090px #FFF, 328px 837px #FFF, 775px 85px #FFF, 436px 632px #FFF, 1704px 904px #FFF, 406px 1068px #FFF, 1077px 719px #FFF, 125px 722px #FFF, 1735px 174px #FFF, 1541px 1590px #FFF, 1883px 498px #FFF, 733px 1195px #FFF, 1178px 1793px #FFF, 254px 719px #FFF, 1790px 545px #FFF, 799px 454px #FFF, 1076px 1737px #FFF, 360px 95px #FFF, 516px 1367px #FFF, 1016px 493px #FFF, 1340px 15px #FFF, 1843px 355px #FFF, 416px 1857px #FFF, 1745px 1967px #FFF, 1768px 1266px #FFF, 778px 1437px #FFF, 1892px 1480px #FFF, 1861px 1700px #FFF, 1973px 832px #FFF, 132px 1773px #FFF, 1928px 1974px #FFF, 470px 1852px #FFF, 1855px 463px #FFF, 892px 1018px #FFF, 374px 1670px #FFF, 140px 391px #FFF, 1868px 1945px #FFF, 505px 1988px #FFF, 1798px 1514px #FFF, 134px 873px #FFF, 1319px 1278px #FFF, 1796px 1403px #FFF, 1400px 503px #FFF, 480px 1004px #FFF, 53px 434px #FFF, 1075px 507px #FFF, 1376px 13px #FFF, 150px 623px #FFF, 563px 1355px #FFF, 1464px 858px #FFF, 1123px 967px #FFF, 1273px 1412px #FFF, 1378px 1689px #FFF, 1390px 1248px #FFF, 273px 191px #FFF, 471px 46px #FFF, 1520px 780px #FFF, 830px 530px #FFF, 1369px 1915px #FFF, 434px 1541px #FFF, 1658px 753px #FFF, 1955px 1908px #FFF, 409px 1106px #FFF, 1609px 597px #FFF, 637px 1671px #FFF, 1957px 475px #FFF, 1595px 1496px #FFF, 1979px 1958px #FFF, 1364px 1658px #FFF, 929px 425px #FFF, 1197px 967px #FFF, 82px 1164px #FFF, 310px 176px #FFF, 1242px 1822px #FFF, 1081px 484px #FFF, 1436px 1211px #FFF, 992px 1826px #FFF, 1801px 714px #FFF, 1034px 16px #FFF, 1241px 145px #FFF, 1815px 811px #FFF, 517px 111px #FFF, 512px 1871px #FFF, 185px 1024px #FFF, 869px 1606px #FFF, 904px 1418px #FFF, 271px 807px #FFF, 1627px 1971px #FFF, 1048px 200px #FFF, 1543px 152px #FFF, 1559px 924px #FFF, 1205px 1281px #FFF, 1073px 697px #FFF, 1614px 426px #FFF, 1064px 1329px #FFF, 1963px 1252px #FFF, 444px 992px #FFF, 553px 1688px #FFF, 298px 578px #FFF, 772px 636px #FFF, 1755px 1446px #FFF, 1503px 450px #FFF, 504px 135px #FFF, 1714px 314px #FFF, 447px 1165px #FFF, 1903px 1258px #FFF, 1916px 434px #FFF, 1468px 1851px #FFF, 631px 244px #FFF, 201px 1060px #FFF, 88px 340px #FFF, 342px 1039px #FFF, 699px 488px #FFF, 1284px 1794px #FFF, 1458px 759px #FFF, 1335px 1629px #FFF, 1367px 1363px #FFF, 185px 235px #FFF, 76px 1315px #FFF, 1528px 198px #FFF, 471px 1165px #FFF, 1160px 1077px #FFF, 79px 473px #FFF, 114px 654px #FFF, 1400px 722px #FFF, 829px 1532px #FFF, 448px 941px #FFF, 627px 1987px #FFF, 820px 919px #FFF, 1506px 217px #FFF, 1946px 1562px #FFF, 142px 1489px #FFF, 793px 602px #FFF, 441px 91px #FFF, 259px 453px #FFF, 1538px 1732px #FFF, 1498px 1501px #FFF, 656px 805px #FFF, 1808px 1178px #FFF, 1954px 890px #FFF, 656px 1605px #FFF, 1177px 350px #FFF, 666px 992px #FFF, 1486px 305px #FFF, 661px 1647px #FFF, 1209px 1168px #FFF, 1499px 1450px #FFF, 554px 98px #FFF, 744px 1047px #FFF, 2px 524px #FFF, 383px 1086px #FFF, 1822px 244px #FFF, 1704px 1714px #FFF, 797px 1527px #FFF, 1916px 1610px #FFF, 1865px 860px #FFF, 1567px 1823px #FFF, 1340px 273px #FFF, 1133px 168px #FFF, 557px 1978px #FFF, 1419px 1724px #FFF, 668px 538px #FFF, 1411px 1026px #FFF, 1194px 1431px #FFF, 267px 1899px #FFF, 556px 950px #FFF, 1847px 1514px #FFF, 255px 131px #FFF, 442px 441px #FFF, 1089px 1011px #FFF, 1931px 1781px #FFF, 187px 119px #FFF, 1317px 1797px #FFF, 15px 971px #FFF, 1542px 618px #FFF, 594px 1216px #FFF, 523px 1980px #FFF, 682px 1977px #FFF, 1840px 1934px #FFF, 73px 1975px #FFF, 192px 37px #FFF, 1796px 1491px #FFF, 237px 418px #FFF, 635px 1936px #FFF, 735px 421px #FFF, 1785px 1564px #FFF, 85px 1401px #FFF, 288px 1436px #FFF, 972px 1682px #FFF, 986px 206px #FFF, 730px 1225px #FFF, 569px 557px #FFF, 1805px 1898px #FFF, 488px 1963px #FFF, 877px 997px #FFF, 545px 1029px #FFF, 1725px 1017px #FFF, 1320px 1855px #FFF, 1220px 409px #FFF, 1911px 453px #FFF, 784px 636px #FFF, 570px 862px #FFF, 1009px 7px #FFF, 432px 1640px #FFF, 1941px 1197px #FFF, 422px 994px #FFF, 465px 1879px #FFF, 918px 200px #FFF, 1978px 1313px #FFF, 520px 954px #FFF, 1270px 1594px #FFF, 1087px 578px #FFF, 1597px 58px #FFF, 1782px 550px #FFF, 274px 204px #FFF, 1743px 1662px #FFF, 441px 1338px #FFF, 545px 1844px #FFF, 1702px 894px #FFF, 1760px 1426px #FFF, 795px 1068px #FFF, 1076px 373px #FFF, 1666px 1594px #FFF, 1490px 1257px #FFF, 7px 1967px #FFF, 357px 1354px #FFF, 1740px 1071px #FFF, 1876px 1981px #FFF, 421px 1758px #FFF, 750px 1071px #FFF, 963px 266px #FFF, 918px 1422px #FFF, 811px 463px #FFF, 1084px 1186px #FFF, 950px 1852px #FFF, 709px 916px #FFF, 37px 1612px #FFF, 777px 747px #FFF, 749px 1663px #FFF, 1036px 600px #FFF, 1951px 1697px #FFF, 1208px 1139px #FFF, 549px 400px #FFF, 1144px 584px #FFF, 1466px 1315px #FFF, 131px 826px #FFF, 172px 1097px #FFF, 253px 97px #FFF, 197px 422px #FFF, 792px 25px #FFF, 957px 1632px #FFF, 1209px 889px #FFF, 826px 638px #FFF, 1464px 1137px #FFF, 1189px 2px #FFF, 1737px 1514px #FFF, 142px 954px #FFF, 1831px 544px #FFF, 1008px 779px #FFF, 1889px 257px #FFF, 1006px 1085px #FFF, 428px 1043px #FFF, 1915px 1585px #FFF, 232px 1935px #FFF, 767px 706px #FFF, 431px 334px #FFF, 897px 15px #FFF, 705px 146px #FFF;
  }

  #stars2 {
    width: 2px;
    height: 2px;
    background: transparent;
    box-shadow: 743px 879px #FFF, 1145px 1260px #FFF, 1412px 672px #FFF, 507px 1211px #FFF, 52px 89px #FFF, 1045px 256px #FFF, 1022px 859px #FFF, 1520px 413px #FFF, 1575px 604px #FFF, 992px 877px #FFF, 914px 960px #FFF, 139px 685px #FFF, 720px 707px #FFF, 1235px 945px #FFF, 1256px 1153px #FFF, 1223px 641px #FFF, 1810px 466px #FFF, 1011px 1248px #FFF, 799px 1160px #FFF, 770px 1135px #FFF, 1000px 1841px #FFF, 1733px 385px #FFF, 1142px 610px #FFF, 1047px 487px #FFF, 1268px 1604px #FFF, 1877px 835px #FFF, 1003px 698px #FFF, 525px 714px #FFF, 703px 960px #FFF, 267px 1336px #FFF, 1343px 133px #FFF, 230px 731px #FFF, 1613px 1782px #FFF, 758px 1457px #FFF, 1877px 1912px #FFF, 1155px 1320px #FFF, 719px 932px #FFF, 746px 69px #FFF, 1148px 186px #FFF, 1642px 1323px #FFF, 728px 1138px #FFF, 815px 461px #FFF, 1281px 137px #FFF, 132px 1620px #FFF, 685px 500px #FFF, 1067px 1439px #FFF, 101px 1941px #FFF, 218px 857px #FFF, 181px 1464px #FFF, 1403px 769px #FFF, 744px 815px #FFF, 1052px 553px #FFF, 1447px 1035px #FFF, 814px 1090px #FFF, 1127px 1883px #FFF, 689px 83px #FFF, 1067px 1753px #FFF, 1948px 34px #FFF, 676px 1749px #FFF, 830px 1875px #FFF, 836px 512px #FFF, 1847px 800px #FFF, 920px 1950px #FFF, 368px 71px #FFF, 1773px 818px #FFF, 215px 1460px #FFF, 1246px 1249px #FFF, 1078px 470px #FFF, 401px 437px #FFF, 1711px 1570px #FFF, 1345px 506px #FFF, 397px 571px #FFF, 1610px 1971px #FFF, 1134px 1569px #FFF, 163px 322px #FFF, 1837px 371px #FFF, 485px 424px #FFF, 488px 1287px #FFF, 747px 354px #FFF, 944px 939px #FFF, 505px 100px #FFF, 470px 1438px #FFF, 1222px 1874px #FFF, 559px 316px #FFF, 1188px 1154px #FFF, 136px 11px #FFF, 1213px 1280px #FFF, 335px 422px #FFF, 1849px 388px #FFF, 181px 1449px #FFF, 1945px 1622px #FFF, 1609px 1373px #FFF, 901px 922px #FFF, 1413px 520px #FFF, 1526px 1563px #FFF, 1318px 1232px #FFF, 1623px 518px #FFF, 664px 76px #FFF, 1692px 1963px #FFF, 1752px 1104px #FFF, 1291px 1249px #FFF, 369px 1823px #FFF, 552px 1779px #FFF, 1007px 634px #FFF, 391px 1101px #FFF, 1480px 1260px #FFF, 1375px 251px #FFF, 594px 1939px #FFF, 1832px 333px #FFF, 1233px 1790px #FFF, 1802px 648px #FFF, 1934px 798px #FFF, 1781px 1814px #FFF, 1849px 1544px #FFF, 568px 771px #FFF, 527px 169px #FFF, 1094px 62px #FFF, 713px 1750px #FFF, 290px 563px #FFF, 1704px 935px #FFF, 1207px 1612px #FFF, 202px 1971px #FFF, 1451px 355px #FFF, 1749px 571px #FFF, 1292px 1565px #FFF, 1827px 1082px #FFF, 22px 984px #FFF, 1315px 1760px #FFF, 1699px 1391px #FFF, 1793px 1715px #FFF, 1501px 49px #FFF, 1480px 1502px #FFF, 1455px 1964px #FFF, 1787px 1539px #FFF, 462px 883px #FFF, 84px 1082px #FFF, 1589px 1414px #FFF, 121px 1323px #FFF, 788px 494px #FFF, 109px 109px #FFF, 951px 1299px #FFF, 872px 1535px #FFF, 1171px 806px #FFF, 671px 598px #FFF, 1767px 1069px #FFF, 192px 1881px #FFF, 1263px 171px #FFF, 1532px 224px #FFF, 664px 1191px #FFF, 145px 686px #FFF, 638px 1744px #FFF, 1099px 332px #FFF, 1119px 802px #FFF, 530px 1776px #FFF, 765px 1011px #FFF, 1528px 1117px #FFF, 1375px 1170px #FFF, 1454px 470px #FFF, 299px 558px #FFF, 960px 655px #FFF, 1816px 1220px #FFF, 1165px 1486px #FFF, 260px 731px #FFF, 548px 80px #FFF, 1003px 1239px #FFF, 1058px 230px #FFF, 505px 1085px #FFF, 140px 1772px #FFF, 1439px 326px #FFF, 1456px 778px #FFF, 915px 1545px #FFF, 430px 1368px #FFF, 849px 587px #FFF, 1231px 869px #FFF, 1662px 928px #FFF, 1903px 38px #FFF, 1052px 1977px #FFF, 1935px 1481px #FFF, 538px 1271px #FFF, 1511px 1304px #FFF, 1617px 1015px #FFF, 489px 1088px #FFF, 996px 1039px #FFF, 201px 1878px #FFF, 466px 1689px #FFF, 1363px 862px #FFF, 1374px 1714px #FFF, 549px 787px #FFF, 1669px 1043px #FFF, 697px 1874px #FFF, 1915px 1724px #FFF, 1488px 1629px #FFF, 1545px 1875px #FFF, 978px 1201px #FFF, 1126px 1916px #FFF, 412px 592px #FFF, 852px 732px #FFF, 1060px 444px #FFF, 323px 1676px #FFF, 1593px 1616px #FFF;
    animation: animStar 100s linear infinite;
  }

  #stars2:after {
    content: " ";
    position: absolute;
    top: 400px;
    width: 2px;
    height: 2px;
    background: transparent;
    box-shadow: 743px 879px #FFF, 1145px 1260px #FFF, 1412px 672px #FFF, 507px 1211px #FFF, 52px 89px #FFF, 1045px 256px #FFF, 1022px 859px #FFF, 1520px 413px #FFF, 1575px 604px #FFF, 992px 877px #FFF, 914px 960px #FFF, 139px 685px #FFF, 720px 707px #FFF, 1235px 945px #FFF, 1256px 1153px #FFF, 1223px 641px #FFF, 1810px 466px #FFF, 1011px 1248px #FFF, 799px 1160px #FFF, 770px 1135px #FFF, 1000px 1841px #FFF, 1733px 385px #FFF, 1142px 610px #FFF, 1047px 487px #FFF, 1268px 1604px #FFF, 1877px 835px #FFF, 1003px 698px #FFF, 525px 714px #FFF, 703px 960px #FFF, 267px 1336px #FFF, 1343px 133px #FFF, 230px 731px #FFF, 1613px 1782px #FFF, 758px 1457px #FFF, 1877px 1912px #FFF, 1155px 1320px #FFF, 719px 932px #FFF, 746px 69px #FFF, 1148px 186px #FFF, 1642px 1323px #FFF, 728px 1138px #FFF, 815px 461px #FFF, 1281px 137px #FFF, 132px 1620px #FFF, 685px 500px #FFF, 1067px 1439px #FFF, 101px 1941px #FFF, 218px 857px #FFF, 181px 1464px #FFF, 1403px 769px #FFF, 744px 815px #FFF, 1052px 553px #FFF, 1447px 1035px #FFF, 814px 1090px #FFF, 1127px 1883px #FFF, 689px 83px #FFF, 1067px 1753px #FFF, 1948px 34px #FFF, 676px 1749px #FFF, 830px 1875px #FFF, 836px 512px #FFF, 1847px 800px #FFF, 920px 1950px #FFF, 368px 71px #FFF, 1773px 818px #FFF, 215px 1460px #FFF, 1246px 1249px #FFF, 1078px 470px #FFF, 401px 437px #FFF, 1711px 1570px #FFF, 1345px 506px #FFF, 397px 571px #FFF, 1610px 1971px #FFF, 1134px 1569px #FFF, 163px 322px #FFF, 1837px 371px #FFF, 485px 424px #FFF, 488px 1287px #FFF, 747px 354px #FFF, 944px 939px #FFF, 505px 100px #FFF, 470px 1438px #FFF, 1222px 1874px #FFF, 559px 316px #FFF, 1188px 1154px #FFF, 136px 11px #FFF, 1213px 1280px #FFF, 335px 422px #FFF, 1849px 388px #FFF, 181px 1449px #FFF, 1945px 1622px #FFF, 1609px 1373px #FFF, 901px 922px #FFF, 1413px 520px #FFF, 1526px 1563px #FFF, 1318px 1232px #FFF, 1623px 518px #FFF, 664px 76px #FFF, 1692px 1963px #FFF, 1752px 1104px #FFF, 1291px 1249px #FFF, 369px 1823px #FFF, 552px 1779px #FFF, 1007px 634px #FFF, 391px 1101px #FFF, 1480px 1260px #FFF, 1375px 251px #FFF, 594px 1939px #FFF, 1832px 333px #FFF, 1233px 1790px #FFF, 1802px 648px #FFF, 1934px 798px #FFF, 1781px 1814px #FFF, 1849px 1544px #FFF, 568px 771px #FFF, 527px 169px #FFF, 1094px 62px #FFF, 713px 1750px #FFF, 290px 563px #FFF, 1704px 935px #FFF, 1207px 1612px #FFF, 202px 1971px #FFF, 1451px 355px #FFF, 1749px 571px #FFF, 1292px 1565px #FFF, 1827px 1082px #FFF, 22px 984px #FFF, 1315px 1760px #FFF, 1699px 1391px #FFF, 1793px 1715px #FFF, 1501px 49px #FFF, 1480px 1502px #FFF, 1455px 1964px #FFF, 1787px 1539px #FFF, 462px 883px #FFF, 84px 1082px #FFF, 1589px 1414px #FFF, 121px 1323px #FFF, 788px 494px #FFF, 109px 109px #FFF, 951px 1299px #FFF, 872px 1535px #FFF, 1171px 806px #FFF, 671px 598px #FFF, 1767px 1069px #FFF, 192px 1881px #FFF, 1263px 171px #FFF, 1532px 224px #FFF, 664px 1191px #FFF, 145px 686px #FFF, 638px 1744px #FFF, 1099px 332px #FFF, 1119px 802px #FFF, 530px 1776px #FFF, 765px 1011px #FFF, 1528px 1117px #FFF, 1375px 1170px #FFF, 1454px 470px #FFF, 299px 558px #FFF, 960px 655px #FFF, 1816px 1220px #FFF, 1165px 1486px #FFF, 260px 731px #FFF, 548px 80px #FFF, 1003px 1239px #FFF, 1058px 230px #FFF, 505px 1085px #FFF, 140px 1772px #FFF, 1439px 326px #FFF, 1456px 778px #FFF, 915px 1545px #FFF, 430px 1368px #FFF, 849px 587px #FFF, 1231px 869px #FFF, 1662px 928px #FFF, 1903px 38px #FFF, 1052px 1977px #FFF, 1935px 1481px #FFF, 538px 1271px #FFF, 1511px 1304px #FFF, 1617px 1015px #FFF, 489px 1088px #FFF, 996px 1039px #FFF, 201px 1878px #FFF, 466px 1689px #FFF, 1363px 862px #FFF, 1374px 1714px #FFF, 549px 787px #FFF, 1669px 1043px #FFF, 697px 1874px #FFF, 1915px 1724px #FFF, 1488px 1629px #FFF, 1545px 1875px #FFF, 978px 1201px #FFF, 1126px 1916px #FFF, 412px 592px #FFF, 852px 732px #FFF, 1060px 444px #FFF, 323px 1676px #FFF, 1593px 1616px #FFF;
  }

  #stars3 {
    width: 3px;
    height: 3px;
    background: transparent;
    box-shadow: 1703px 1208px #FFF, 381px 207px #FFF, 1516px 1990px #FFF, 1960px 982px #FFF, 922px 1278px #FFF, 689px 1766px #FFF, 538px 554px #FFF, 802px 1529px #FFF, 871px 1805px #FFF, 228px 1830px #FFF, 353px 186px #FFF, 1873px 1154px #FFF, 494px 1244px #FFF, 1415px 261px #FFF, 1020px 1990px #FFF, 1924px 1169px #FFF, 1205px 464px #FFF, 596px 830px #FFF, 1319px 504px #FFF, 953px 1850px #FFF, 408px 1360px #FFF, 569px 1704px #FFF, 1367px 729px #FFF, 807px 1845px #FFF, 572px 825px #FFF, 523px 1089px #FFF, 1405px 1622px #FFF, 1352px 423px #FFF, 1247px 1758px #FFF, 1117px 920px #FFF, 983px 352px #FFF, 175px 1351px #FFF, 962px 260px #FFF, 918px 1530px #FFF, 1184px 935px #FFF, 201px 473px #FFF, 90px 1759px #FFF, 211px 933px #FFF, 1887px 171px #FFF, 177px 101px #FFF, 844px 346px #FFF, 283px 1821px #FFF, 1236px 1225px #FFF, 173px 1875px #FFF, 24px 1776px #FFF, 277px 804px #FFF, 1714px 715px #FFF, 937px 1882px #FFF, 1708px 1405px #FFF, 173px 1847px #FFF, 1902px 1160px #FFF, 1725px 1270px #FFF, 36px 18px #FFF, 1612px 1467px #FFF, 1390px 1733px #FFF, 927px 1315px #FFF, 1907px 1337px #FFF, 1855px 1454px #FFF, 1033px 1425px #FFF, 1450px 1359px #FFF, 1422px 771px #FFF, 256px 343px #FFF, 1581px 340px #FFF, 1180px 247px #FFF, 191px 882px #FFF, 372px 1171px #FFF, 1509px 937px #FFF, 1018px 1829px #FFF, 121px 152px #FFF, 327px 767px #FFF, 1438px 1421px #FFF, 321px 905px #FFF, 616px 245px #FFF, 1957px 1520px #FFF, 1811px 1924px #FFF, 1454px 1778px #FFF, 1507px 822px #FFF, 649px 218px #FFF, 362px 1567px #FFF, 1637px 145px #FFF, 115px 466px #FFF, 345px 935px #FFF, 112px 1019px #FFF, 1440px 1910px #FFF, 1280px 1367px #FFF, 1505px 890px #FFF, 788px 927px #FFF, 753px 1273px #FFF, 1924px 1714px #FFF, 495px 1149px #FFF, 267px 1851px #FFF, 1293px 1431px #FFF, 1159px 433px #FFF, 1725px 1170px #FFF, 1067px 296px #FFF, 746px 463px #FFF, 412px 349px #FFF, 1193px 1421px #FFF, 564px 455px #FFF, 1675px 589px #FFF;
    animation: animStar 150s linear infinite;
  }

  #stars3:after {
    content: " ";
    position: absolute;
    top: 200px;
    width: 3px;
    height: 3px;
    background: transparent;
    box-shadow: 1703px 1208px #FFF, 381px 207px #FFF, 1516px 1990px #FFF, 1960px 982px #FFF, 922px 1278px #FFF, 689px 1766px #FFF, 538px 554px #FFF, 802px 1529px #FFF, 871px 1805px #FFF, 228px 1830px #FFF, 353px 186px #FFF, 1873px 1154px #FFF, 494px 1244px #FFF, 1415px 261px #FFF, 1020px 1990px #FFF, 1924px 1169px #FFF, 1205px 464px #FFF, 596px 830px #FFF, 1319px 504px #FFF, 953px 1850px #FFF, 408px 1360px #FFF, 569px 1704px #FFF, 1367px 729px #FFF, 807px 1845px #FFF, 572px 825px #FFF, 523px 1089px #FFF, 1405px 1622px #FFF, 1352px 423px #FFF, 1247px 1758px #FFF, 1117px 920px #FFF, 983px 352px #FFF, 175px 1351px #FFF, 962px 260px #FFF, 918px 1530px #FFF, 1184px 935px #FFF, 201px 473px #FFF, 90px 1759px #FFF, 211px 933px #FFF, 1887px 171px #FFF, 177px 101px #FFF, 844px 346px #FFF, 283px 1821px #FFF, 1236px 1225px #FFF, 173px 1875px #FFF, 24px 1776px #FFF, 277px 804px #FFF, 1714px 715px #FFF, 937px 1882px #FFF, 1708px 1405px #FFF, 173px 1847px #FFF, 1902px 1160px #FFF, 1725px 1270px #FFF, 36px 18px #FFF, 1612px 1467px #FFF, 1390px 1733px #FFF, 927px 1315px #FFF, 1907px 1337px #FFF, 1855px 1454px #FFF, 1033px 1425px #FFF, 1450px 1359px #FFF, 1422px 771px #FFF, 256px 343px #FFF, 1581px 340px #FFF, 1180px 247px #FFF, 191px 882px #FFF, 372px 1171px #FFF, 1509px 937px #FFF, 1018px 1829px #FFF, 121px 152px #FFF, 327px 767px #FFF, 1438px 1421px #FFF, 321px 905px #FFF, 616px 245px #FFF, 1957px 1520px #FFF, 1811px 1924px #FFF, 1454px 1778px #FFF, 1507px 822px #FFF, 649px 218px #FFF, 362px 1567px #FFF, 1637px 145px #FFF, 115px 466px #FFF, 345px 935px #FFF, 112px 1019px #FFF, 1440px 1910px #FFF, 1280px 1367px #FFF, 1505px 890px #FFF, 788px 927px #FFF, 753px 1273px #FFF, 1924px 1714px #FFF, 495px 1149px #FFF, 267px 1851px #FFF, 1293px 1431px #FFF, 1159px 433px #FFF, 1725px 1170px #FFF, 1067px 296px #FFF, 746px 463px #FFF, 412px 349px #FFF, 1193px 1421px #FFF, 564px 455px #FFF, 1675px 589px #FFF;
  }

  @keyframes animStar {
    from {
      transform: translateY(0px);
    }

    to {
      transform: translateY(-2000px);
    }
  }



/* ............................................................................................ */
  body>.grid {
    height: 100%;
  }

  body{
    background:#000;
  }

  .loginBG{
    height:100vh;
    width:100%;
    background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
  }

  .login-box {
    background: #FFFBF5;
    height: 570px;
    width: 500px;
    padding: 15px;
    position: absolute;
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* border: 4px solid #000; */
    border-radius: 20px;
    display: flex;
  }

  .login-bg {
    background-image: url('../pic/pyh.jpg');
    background-size: cover;
    background-position: center;
    width: 50%;
    border-radius: 16px;
    border: 4px solid #000;
  }

  .login-form {
    width: 100%;
    position:relative;
  }

  .login-text {
    width: 90%;
    margin: auto;
    padding: 20px 0px;
  }

  .login-text .forgot-password {
    float: right;
    font-weight: 900;
  }

  .login-text .forgot-password:hover {
    text-decoration: underline;
  }

  .login-text .signup {
    font-weight: 900;
  }

  .login-text .signup:hover {
    text-decoration: underline;
  }

  .logo {
    text-align: center;
  }

  .logo p {
    font-size: 30px;
    font-weight: 900;
    letter-spacing: 10px;
  }

  .lboxcss {
    width: 100%;
    position: relative;
    height: 60px;
    overflow: hidden;
    margin: 10px 0px;
  }

  .lboxcss .lbox-input {
    width: 100%;
    height: 100%;
    color: #000;
    padding-top: 20px;
    border: none;
    background: transparent;
  }

  .lboxcss .lbox-input-wrapper {
    display: flex;
  }

  .lboxcss .lbox-input-wrapper .icon {
    margin-left: 5px;
  }

  .lboxcss label {
    position: absolute;
    bottom: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    pointer-events: none;
    border-bottom: 1px solid grey;
  }

  .lboxcss label::after {
    content: "";
    position: absolute;
    bottom: -1px;
    left: 0px;
    width: 100%;
    height: 100%;
    border-bottom: 3px solid #000;
    transform: translateX(-100%);
    transition: all 0.3s ease;
  }

  .content-name {
    position: absolute;
    bottom: 0px;
    left: 0px;
    padding-bottom: 5px;
    transition: all 0.3s ease;
  }

  .lboxcss .lbox-input:focus {
    outline: none;
  }

  .lboxcss .lbox-input:focus+.label-name .content-name,
  .lboxcss .lbox-input:valid+.label-name .content-name {
    transform: translateY(-150%);
    font-size: 14px;
    left: 0px;
    color: #000;
  }

  .lboxcss .lbox-input:focus+.label-name::after,
  .lboxcss .lbox-input:valid+.label-name::after {
    transform: translateX(0%);
  }

  #showPasswordButton {
    background: transparent;
    border: 0px;
    margin: 35px 0px;
    position: absolute;
    right: 0;
    cursor: pointer;
  }


  .lboxcss .lbox-input-wrapper .lbox-input {
    flex: 1;
    /* Take up remaining space */
  }

  .lboxcss .lbox-input-wrapper .icon {
    margin-left: 5px;
  }


  .login-function {
    position: absolute;
    width: calc(100% - 40px);
    text-align: center;
    /* margin: 180px 0px; */
    bottom:10px;
  }

  .login-function #login-button {
    width: 100%;
    transition: 0.4s;
    margin: 4px 0px;
  }
</style>

<script src="../Fomantic-ui/dist/semantic.min.js"></script>
<!-- <script src="assets/library/jquery.min.js"></script> -->
<script src="../Fomantic-ui/dist/components/form.js"></script>
<script src="../Fomantic-ui/dist/components/transition.js"></script>