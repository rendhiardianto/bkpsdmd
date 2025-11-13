<!doctype html>
<html>
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-65T4XSDM2Q');
</script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<link rel="stylesheet" href="/fontFamily.css">
<link href="/headerFooter.css" rel="stylesheet" type="text/css">
<link href="index.css" rel="stylesheet" type="text/css">

<title>Dashboard Live Reporting - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="/icon/IconWeb.png">
</head>

<body>
	
<div class="topnav" id="mynavBtn">
	<div id="startButton"></div>
	<script>
	fetch("/startButton.html")
		.then(response => response.text())
		.then(data => {
			document.getElementById("startButton").innerHTML = data;
		});
	</script>
	<div class="navLogo">
		<a href="/index.php"><img src="/icon/BKPLogo3.png" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>	
	</div>
	
</div>
	
<!------------------- CONTENT ----------------------------------->
<h2>Dashboard Live Reporting</h2>
<div class="content">

    <!--<div class="flex-item-main">
      <h4>CUTI ASN</h4>
      <a href="download_sk_jafung.php"><img src="/icon/PSDM/sakit.png"></a>
    </div>-->

</div>
<!------------------- FOOTER ----------------------------------->	
<div class="gotoTop" onclick="topFunction()" id="myBtn" title="Go to top"> <img src="/icon/go_to_top.png"></div>

<div id="footer"></div>

<script>
fetch("/footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
	
<script src="/JavaScript/script.js"></script>
	
</body>
</html>
