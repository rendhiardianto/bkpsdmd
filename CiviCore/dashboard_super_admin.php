<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include "db.php";
include "auth.php";
include "datetime_helper.php";
include "verification/jafung/jf_counthelper.php";

requireRole('super_admin');
$role = 'super_admin';

// define filter default so it's always available
$filter = $_GET['filter'] ?? 'all';
$counts = getSubmissionCounts($conn);
?>

<?php
$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT nip, fullname, jabatan, organisasi, profile_pic FROM users WHERE id=$userId");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Dashboard</title>
  <link href="dashboard_style.css" rel="stylesheet" type="text/css">
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="/icon/button/logo2.png">

</head>
<body>

  <div class="header">

    <div class="logo">
      <a href="../index.php" target="_blank"><img src="../icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
    </div>

    <div class="roleHeader">
      <h1>👑 Super Admin Dashboard 👑</h1>
    </div>

    <div class="startlogoDD">
      <div class="namaProfil">Halo, <?php echo $user['fullname']; ?></div>
      <button onclick="toggleStartMenu()" class="startbtn"> <?php if (!empty($user['profile_pic'])): ?>
        <img src="uploads/profile_pics/<?php echo $user['profile_pic']; ?>" class="profile-pic">
        <?php else: ?>
          <img src="uploads/profile_pics/default.png" alt="Default" class="profile-pic">
        <?php endif; ?>
      </button>
      
      <div id="myStart" class="start-content">
        <a href="edit_profile/edit_profile.php"><img src="/icon/edit_profile.png" width="20px"> Edit Profile</a>
        <a href="logout.php" class="logout" style="text-decoration: none;"><img src="/icon/log_out.png" width="20px">Logout</a>
      </div>
    </div>
  </div>
 

<div class="liveClock">
  <?php echo renderLiveClock(); ?>
</div>

<div class="content">
  
  <div class="leftSide">
    <div class="greetings">
          <?php
            date_default_timezone_set('Asia/Jakarta');
            $a = date ("H");

            if (($a >=4) && ($a<=10))
            {
              echo "Selamat Pagi ";
              echo "&#9728;";
            }
            elseif (($a>=11) && ($a<=15))
            {
              echo "Selamat Siang ";
              echo "&#9788;";
            }
            elseif (($a>=16) && ($a<=17))
            {
              echo "Selamat Sore ";
              echo "&#9729;";
            }
            else
            {
              echo "Selamat Malam ";
              echo "&#9790;";
            }
          ?>
        </div>
        
    <div class="userBio">
      <div class="fotoProfil">
        <?php if ($user['profile_pic']): ?>
        <img src="uploads/profile_pics/<?php echo $user['profile_pic']; ?>" alt="Profile Picture">
        <?php else: ?>
        <img src="uploads/profile_pics/default.png" alt="Default Profile">
        <?php endif; ?>
      </div>

      <div class="namaProfil">
        <br><?php echo $user['fullname']; ?>
      </div>

      <div class="nipProfil">
          <?php echo $user['nip']; ?>
        </div>
        
      <div class="jabatanProfil">
          <br><?php echo $user['jabatan']; ?>
      </div>

      <div class="organisasiProfil">
        <b><?php echo $user['organisasi']; ?></b>
      </div>
      <br><hr>
    </div>

    <div class="tab">
      <button class="tablinks" onclick="openCity(event, 'tab2')">Command Center</button>
      <button class="tablinks" onclick="openCity(event, 'tab3')" id="defaultOpen">Services Verification</button>
      <button class="tablinks" onclick="openCity(event, 'tab4')" >Website CMS</button>
      <button class="tablinks" onclick="openCity(event, 'tab1')">User Authorithy</button>
    </div>

  </div><!--leftSide-->

  <div class="rightSide">
    <div id="tab1" class="tabcontent">
      <div class="flex-item-main">
        <p><a href="authority/add_user.php?role=<?php echo urlencode($role); ?>">
        <img src="../icon/button/add_user.png" alt="Add User"> </a><br>ADD CIVICORE USER</p>
      </div>
      
      <div class="flex-item-main">
        <p><a href="authority/dashboard_admin_list.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/profil.png" ></a><br>CIVICORE USER LIST</p>
      </div>

      <div class="flex-item-main">
        <p><a href="authority/add_asn_merangin.php?role=<?php echo urlencode($role); ?>">
        <img src="../icon/button/add_asn.png" alt="Add User"> </a><br>ADD ASN MERANGIN</p>
      </div>

    </div>

    <div id="tab2" class="tabcontent">
      
    </div>

    <div id="tab3" class="tabcontent">
      <!--<div class="filter-bar">
        <?php
          $filters = ['all' => 'All']; 
          foreach ($filters as $key => $label) {
            $count = $counts[$key] ?? 0;
            $active = ($filter === $key) ? 'active' : '';
          }
        ?>
      </div>-->
      <div class="flex-item-main">
        <span class="badge"><?= $counts['all'] ?? 0; ?></span>
        <p><a href="verification/jafung/verify_documents.php?role=<?= urlencode($role); ?>">
          <img src="../icon/button/fungsional.png"></a><br>VERIFIKASI JABATAN FUNGSIONAL</p>
      </div>
    </div>

    <div id="tab4" class="tabcontent">
      
      <div class="flex-item-main">
        <p><a href="pengumuman/dashboard_pengumuman.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/announcement.png" ></a><br>PENGUMUMAN</p>
      </div>

      <div class="flex-item-main">
        <p><a href="berita/admin_news.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/news.png" ></a><br>BERITA</p>
      </div>

      <div class="flex-item-main">
        <p><a href="blog/admin_blog.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/blog.png" ></a><br>BLOG</p>
      </div>

      <div class="flex-item-main">
        <p><a href="infoGrafis/admin_infoGrafis.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/graphics.png" ></a><br>INFOGRAFIS</p>
      </div>

      <div class="flex-item-main">
        <p><a href="transparansi/dashboard_transparansi.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/transparansi.png"></a><br>TRANSPARANSI</p>
      </div>

      <div class="flex-item-main">
        <p><a href="pojokjafung/dashboard_jf.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/fungsional.png"></a><br>POJOK FUNGSIONAL</p>
      </div>

      <div class="flex-item-main">
        <p><a href="rekap_asn_merangin/dashboard_input_rekap_asn.php?role=<?php echo urlencode($role); ?>">
          <img src="../icon/button/rekap_asn.png"></a><br>INPUT REKAP ASN</p>
      </div>
    </div><!--tab4-->
  </div><!--rightSide-->

</div><!--content-->

  <div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
  </div>

<script src="/JavaScript/script.js"></script>

<script>
  // JavaScript code to handle tab switching
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "flex";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

</script>

</body>
</html>