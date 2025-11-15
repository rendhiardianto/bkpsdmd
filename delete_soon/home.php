<?php require_once("auth.php");?>
<!DOCTYPE html>
<html>
<head>
<title>Home - eHome Automation</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="home.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="shortcut icon" href="images/button/logo2.png">
</head>
<body>
<div class="header">
	<left>
	<?php
    	$tanggal= mktime(date("m"),date("d"),date("Y"));
		echo "Date : ".date("D, d M Y", $tanggal)." ";
		date_default_timezone_set('Asia/Jakarta');
		$jam=date("h:i");
		echo "| Time : ". $jam." "." ";
		$a = date ("H");
		if (($a>=6) && ($a<=11))
		{
			echo "| Good Morning, ";
		}
		else if(($a>11) && ($a<=18))
		{
			echo "| Good Afternoon, ";
		}
		else
		{
			echo "| Good Evening, ";
		}
	?>
	<?php echo $_SESSION["user"]["nama_depan"]?>
	<?php echo $_SESSION["user"]["nama_belakang"]?>
    </left>
    <right>
    	Your ID : <?php echo  $_SESSION["user"]["id"]?>
    </right>
</div>

<div class="sidenav">
	<center><profil>
    <img src="user_images/<?php echo $_SESSION["user"]["images"] ?>" height="150px"/>
    <br><?php echo $_SESSION["user"]["nip"] ?>
    <br><span><?php echo  $_SESSION["user"]["email"]?></span>
    </profil></center>
    <br>
    
    <a href="home.php" class="active"><img src="images/button/home1.png" alt="home1"/><span>Home</span></a>
    <a href="button.php"><img src="images/button/buttonpower1.png" alt="buttonpower1"/><span>Power Button</span></a>
    <a href="setting.php"><img src="images/button/pengaturan1.png" alt="pengaturan1"/><span>Setting</span></a>
    <button class="dropdown-btn">
    <img src="images/button/kontak1.png" alt="pengaturan1"/><span>Help</span><i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
        <a href="help.php"><span>Call Us</span></a>
        <a href="qna.php"><span>Q and A</span></a>
    </div>
    <a href="index.php" onclick="return confirm('Apakah yakin ingin keluar?')"><img src="images/button/keluar1.png" alt="LogOut"/><span>Log Out</span></a>
</div>
<div class="main">
	<div class="sampul">
    	<p><b>Selamat datang  di situs eHome Automation!</b></p>
    </div>
    
    <div id="home1">
    </div>
    
    <timeline>Timeline</timeline>
    
    <div class="content">
    <artikel1>
    	<span>Control Device</span>
        Merupakan perangkat yang bertindak sebagai perangkat kendali, perangkat kendali berupa <i>smartphone</i> dan komputer.
    </artikel1>
    
    <artikel2>
		<table>
			<tr>
                <th>Device eHomeAuto V1.0</th>
            </tr>
        </table>
		<table>
			<tr>
            	<td>Berbasis Arduino UNO dikombinasi dengan NodeMCU ESP8266 yang membuatnya bisa terhubung dengan web server, sehingga <i>prototype</i> ini bisa diakses dimana saja melalui koneksi internet, dan juga alat ini dapat membaca suhu suatu ruangan dan mengirimkannya ke web server.</td>
                <td><img src="images/index_pict/eHomeV0.png" alt="prototype" width="500px"/></td>
            </tr>
		</table>
	</artikel2>
            
	<artikel3>
		<span>Controlled Device</span>
        Peralatan yang dikendalikan berupa lampu dan pendingin ruangan, kendali perangkat hanya sebatas ON/OFF saja.
	</artikel3>
	</div>
</div>

<div class="footer">
	Copyright &copy;2018. Electrical Engineering - Jambi University
</div>

</body>
<script>
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}
</script>
</html> 
