<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>	

<style>
/* --- Global --- */
* {
  box-sizing: border-box;
}
body {
  margin: 0;
}

#myBtn {
  display: none;
  position: fixed;
  bottom: 20px;
  right: 30px;
  z-index: 99;
  border: none;
  outline: none;
  cursor: pointer;
  border-radius: 50%;
  background-color: rgb(0, 153, 255);
  color: white;
  
  
}
#myBtn img{
  align-items: center;
  margin: auto;
  display: block;
  width: 40px;
}
.copyright {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: #0077b6;
  color: #ffffff;
  text-align: center;
  font-size: 17px;
  font-family: Roboto-Light;
  padding: 20px;
  z-index: 1000; /* keeps it above other content */
}
@media screen and (max-width: 600px){
	.copyright{
		font-size: 15px;
	}
}
</style>

</head>
<body>

<!------------------- FOOTER ----------------------------------->	

<div class="copyright">
	Copyright © 2025 - <?php echo date("Y"); ?>. TIM PUSDATIN | BKPSDMD Kabupaten Merangin. All Rights Reserved.
</div>

<!--<script> <h3 id="visitor-count">Loading...</h3>
  fetch("counter.php")
    .then(res => res.text())
    .then(count => {
      document.getElementById("visitor-count").innerText = count;
    });
</script>-->

</body>
</html>