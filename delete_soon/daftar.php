<?php
	error_reporting( ~E_NOTICE ); // avoid notice
	require_once 'config.php';
	if(isset($_POST['daftar']))
	{
		$imgFile = $_FILES['userPic']['name'];
		$tmp_dir = $_FILES['userPic']['tmp_name'];
		$imgSize = $_FILES['userPic']['size'];
		
		// filter data yang diinputkan
		$userNama_depan = filter_input(INPUT_POST, 'userNama_depan');
		$userNama_belakang = filter_input(INPUT_POST, 'userNama_belakang');
		$userNIP = filter_input(INPUT_POST, 'userNIP');
		
		// enkripsi password
		$userEmail = filter_input(INPUT_POST, 'userEmail', FILTER_VALIDATE_EMAIL);
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		if(empty($imgFile))
		{
			$errMSG = "Masukkan Foto Terlebih Dahulu!";
		}
		else if(empty($userNama_depan))
		{
			$errMSG = "Masukkan Nama Depan Anda!";
		}
		else if(empty($userNIP))
		{
			$errMSG = "Masukkan NIP Anda!";
		}
		else if(empty($userEmail))
		{
			$errMSG = "Masukkan Email Anda!";
		}
		else if(empty($password))
		{
			$errMSG = "Passwordnya Jangan Lupa.";
		}
		else
		{
			$upload_dir = 'user_image/'; // upload directory
	
			$imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension
		
			// valid image extensions
			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
		
			// rename uploading image
			$images = rand(1000,1000000).".".$imgExt;
				
			// allow valid image file formats
			if(in_array($imgExt, $valid_extensions))
			{			
				// Check file size
				if($imgSize < 500000)
				{
					move_uploaded_file($tmp_dir,$upload_dir.$images);
				}
				else
				{
					$errMSG = "Maaf, ukuran file foto Anda terlalu besar, maksimal 500KB.";
				}
			}
			else
			{
				$errMSG = "Maaf, hanya file bertipe JPG, JPEG, PNG & GIF saja yang diperbolehkan.";		
			}
		}
		
		// if no error occured, continue ....
		if(!isset($errMSG))
		{
			$stmt = $db->prepare('INSERT INTO usercms (userPic, userNIP, userNama_depan, userNama_belakang, userEmail, password)
			VALUES(:userPic, :userNIP, :userNama_depan, :userNama_belakang, :userEmail, :email, :password)');
			
			$stmt->bindParam(':userPic',$userPic);
			$stmt->bindParam(':userNIP',$userNIP);
			$stmt->bindParam(':userNama_depan',$userNama_depan);
			$stmt->bindParam(':userNama_belakang',$userNama_belakang);
			$stmt->bindParam(':userEmail',$userEmail);
			$stmt->bindParam(':password',$password);

			if($stmt->execute())
			{
				$successMSG = "";
				?>
				<script>
				alert('Selamat, data Anda sudah terdaftar.');
				window.location.href='login.php';
				</script>
                <?php
				//header("refresh:5;login.php"); // redirects image view page after 5 seconds.
			}
			else
			{
				$errMSG = "Upps, gagal menyimpan data!";
			}
		}
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Daftar Akun - eHome Automation</title>
<link href="daftar.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="images/button/logo2.png">
</head>
<body>    
	<section class="wrapper">    
    	<header>
            <logo>
            	<a href="index.php"><img src="images/button/logo.png" alt="smarthome" height="35"/> eHome Automation</a>
            </logo>
            <tombol>
            	Sudah punya akun?
                <a href="login.php"><img src="images/button/masuk.png" height="20"/>Masuk</a>
            </tombol>
        </header>
        
        <main>
        <center><notif>
            <?php
            if(isset($errMSG))
            {
                ?>
                <?php echo $errMSG;?>
                <?php
            }
            else if(isset($successMSG))
            {
                ?>
                <?php echo $successMSG;?>
                <?php
            }
            ?>
        </notif></center>
        <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
        	<h2>
            	Upload Foto Profil<br>
            	<input class="input-group" type="file" name="userPic" accept="image/*"/>
            </h2>
            	<table width="800">
                    <tr>
                    	<th><b>Data Diri</b></th>
                    </tr>
                 </table>
                 <table width="800">
                	<tr>
                    	<td>Nama Lengkap</td>
                        <td><input type="text" placeholder="Nama Depan" name="userNama_depan"></td>
                        <td><input type="text" placeholder="Nama Belakang (optional)" name="userNama_belakang"></td>
                    </tr>
                    <tr>
                    	<td>NIP</td>
                        <td><input type="text" placeholder="NIP" name="userNIP"></td>
                    </tr>
                    <tr>
                    	<td>Email</td>
                        <td><input type="email" placeholder="Email Anda" name="userEmail"></td>
                    </tr>
                    <tr>
                    	<td>Buat Sandi</td>
                        <td><input type="password" placeholder="Buat Sandi" name="password"></td>
                    </tr>
                </table>
                <table width="800">
                    <tr>
                    	<td>*pastikan semua data sudah terisi dengan benar</td>
					</tr>
                </table>
            <table width="800">
                <tr>
					<th><input type="submit" class="btn btn-success btn-block" name="daftar" value="Daftar"/></th>
                </tr>
            </table>
			<tombol>
				
			</tombol>
       </form>
       </main>
       
    	<footer>
        	<center><table>
            	<tr>
                	<td>Copyright &copy;2018. Electrical Engineering - Jambi University</td>
                </tr>
            </table></center>
        </footer>
        
    </section>
</body>
</html>
