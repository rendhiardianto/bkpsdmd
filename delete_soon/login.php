<?php
require_once("config.php");
if(isset($_POST['login'])){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $sql = "SELECT * FROM register WHERE username=:username OR email=:email";
    
	$stmt = $db->prepare($sql);
	
    // bind parameter ke query
    $params = array(
        ":username" => $username,
        ":email" => $username
    );
	
	ini_set('memory_limit', '-1');
	
    $stmt->execute($params);
	
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
	
    // jika user terdaftar
    if($user)
	{
		// verifikasi password
        if(password_verify($password, $user["password"]))
		{
            // buat Session
            session_start();
            $_SESSION["user"] = $user;
            // login sukses, alihkan ke halaman timeline
            header("Location: home.php");
        }
    }
	else
	{
		echo "<script>alert('Opps, data yang Anda masukkan salah!'); window.history.back()</script>";
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Masuk - eHome Automation</title>
<link rel="shortcut icon" href="images/button/logo2.png">
<link href="login.css" rel="stylesheet" type="text/css">
</head>
<body>    
	<section class="wrapper">
        <header>
            <logo>
            	<a href="index.php"><img src="images/button/logo.png" alt="smarthome" height="35"/> eHome Automation</a>
            </logo>
            <tombol>
            	Belum punya akun?
                <a href="daftar.php"><img src="images/button/daftar.png" height="20"/>Daftar</a>
            </tombol>
        </header>
        <main>
        <table>
        	<tr>
            	<td width="75%" align="center">
                	<b>eHome Automation<br></b>
                	<span>a last project developed by <a href="https://www.instagram.com/rendhiardianto/" target="_blank"/>goodpeople</span></a>
                </td>
                <td bgcolor="#1E1E1E">
                	<form action="" method="POST" name="login">
                    	<c>Masuk</c>
                        <p><label>Nama Pengguna atau Email</label>
                		<br><input type="text" placeholder="Nama Pengguna/Email" name="username"></p>
                        <p><label>Kata Sandi</label>
                		<br><input type="password" placeholder="Kata Sandi" name="password" id="input"></p>
                		<input type="checkbox" onclick="myFunction()">Lihat Kata Sandi
                		
                        <input type="submit" class="btn btn-success btn-block" name="login" value="Masuk"/>
            		</form>                
                </td>
            </tr>
        </table>      	
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
<script>
function myFunction()
{
    var x = document.getElementById("input");
    if (x.type === "password")
	{
        x.type = "text";
    }
	else 
	{
        x.type = "password";
    }
}
</script> 
</html>