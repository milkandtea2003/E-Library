<?php 
include_once('../db.php');

// if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
//   echo  "Please Login First To Access This Content";
//   header("Location: UserLogin.php");
//   exit();
// }

if(isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // 从数据库中获取用户信息
  $UserProfile = "SELECT * FROM `user` WHERE `user_id` = ?";
  $stmt = mysqli_prepare($conn, $UserProfile);
  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);
  $userAvatar = mysqli_stmt_get_result($stmt);

  if (!$userAvatar) {
      die("Error: " . mysqli_error($conn));
  }

  $userImage = mysqli_fetch_assoc($userAvatar);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" type="text/css" href="../Fomantic-ui/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>
  <!-- <link rel="stylesheet" href="Css/Main.css"> -->
  <link rel="stylesheet" href="Sidebar.php">
  <link rel="icon" href="../logo/favicon.ico" type="image/x-icon">
</head>

<body>



  <div class="" id="nav">
    <a class="title" href="UserIndex.php">ONE LIBRARY</a>
    <div class="" id="rightMenu">
      <!-- <div class="item">
          <div class="ui icon input">
            <input class="search-input" type="text" placeholder="Search..." fdprocessedid="brlzed">
              <i class="search link icon"></i>
            </div>
          </div> -->
      <a class="ui item navHref" id="nav-a" href="BookList.php"><i class="book icon"></i>
        Book
      </a>
      <a class="ui item navHref" id="nav-a" href="Favorites.php"><i class="bookmark icon"></i>
        Favorites
      </a>
      <a class="ui item navHref" id="nav-a" href="#"><i class="users icon"></i>
        About Us
      </a>
      <a class="ui item navHref" id="nav-a" href="#"><i class="phone alternate icon"></i>
        Contact Us
      </a>

      <!-- <div class="ui hidden divider"></div> -->
      <div class="ui floating dropdown " style="padding-left:30px;">
  <?php if(isset($_SESSION['user_id'])) { ?>
    <!-- 如果用户已登录，则显示用户信息 -->
    <div style="display:flex; background:#fff;" class="dropDownProfile">
      <img src="<?php echo $userImage['user_profilepicture'] ? $userImage['user_profilepicture'] : '../ProfilePic/tom.jpg'; ?>"
           class="navAvatar" alt="profilepic">
      <div class="text" id="dropDownText"> <?php echo $userImage['user_name']; ?> <i class="dropdown icon"></i></div> 
    </div>
    <div class="menu" id="dropDownList">
      <a class="item" href="UserProfile.php"><i class="user icon"></i> Profile</a>
      <a class="item" href="Logout.php"><i class="log out icon"></i> Logout</a>
    </div>
  <?php } else { ?>
    <!-- 如果用户未登录，则显示登录按钮 -->
    <button onclick="window.location.href='UserLogin.php'" name="login" class="navLoginButton"><i class="user icon"></i> LOGIN</button>
  <?php }?>
</div>

      <!-- <a class="ui item" id="nav-a" href="UserProfile.php">
        Profile
      </a>
      <a class="ui item" id="nav-a" href="Logout.php">
        Logout
      </a> -->
    </div>
  </div>

</body>
</html>
<script src="../Fomantic-ui/dist/semantic.min.js"></script>
<script>
$('.dropdown')
  .dropdown({
    action: 'hide'
  })
; 
</script>
<script>
 var nav = document.getElementById('nav');
var logo = document.querySelector('.title');
var navLinks = document.querySelectorAll('.navHref');

nav.style.background = "transparent";
nav.style.padding = "30px 20px";
nav.style.borderBottom = "0px solid #45474B";

logo.style.color = "#fff";

navLinks.forEach(function(navLink) {
  navLink.style.color = "#fff";
});

window.onscroll = function (event) {
  var scroll = window.pageYOffset;

  if (scroll > 5) {
    nav.style.background = "#FFFBF5";
    nav.style.padding = "20px 20px";
    nav.style.borderBottom = "0px solid #45474B";
    logo.style.color = "#000";
    
    navLinks.forEach(function(navLink) {
      navLink.style.color = "#000";
    });
  } else {
    nav.style.background = "transparent";
    nav.style.padding = "30px 20px";
    nav.style.borderBottom = "0px solid transparent";
    logo.style.color = "#fff";
    
    navLinks.forEach(function(navLink) {
      navLink.style.color = "#fff";
    });
  }
};
</script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@200&display=swap');
  @import url('https://fonts.googleapis.com/css2?family=Jost&display=swap');


  body {
      background: #dddddd !important;
    }
  #nav {
    width: 100%;
    position: fixed;
    /* padding: 30px 20px; */
    transition: 0.4s;
    z-index: 99;
    top: 0;
    /* backdrop-filter: blur(30px); */
    border-radius:0px;
  }

  .title {
    font-size: 20px;
    position: absolute;
    padding: 0px 30px;
    letter-spacing: 10px;
    color: #000;
  }

  .title:hover {
    color: #000;
  }

  #nav-a {
    transition: 0.4s;
  }
  #rightMenu{
    display:flex;
    margin:0px 20px;
    float:right;
    
  }
  #rightMenu #nav-a{
    color:#000;
    padding-left:30px;
  }
  .dropDownProfile{
    border:2px solid #000;
    margin:-11px 5px;
    padding:5px;
    border-radius:20px;
  }
  #dropDownText{
    margin-top:5px;
    padding:0px 7px;
  }
  #dropDownList{
    /* margin-top:13px; */
    margin:16px 30px;
    border-radius:0px;
  }
  /* #dropDownList a{
    border-radius:20px;
  } */
  .navAvatar{
    height:30px;
    width:30px;
    border-radius:50%;
    object-fit: cover;
    border:2px solid #000;
  }
  .navLoginButton{
    background:#fff;
    color:#000;
    border:2px solid #000;
    border-radius:20px;
    padding:10px 20px;
    transition:0.1s;
    margin:-10px 0px;
    cursor: pointer;
  }
  .navLoginButton:hover{
    background:#000;
    color:#fff;
  }
</style>