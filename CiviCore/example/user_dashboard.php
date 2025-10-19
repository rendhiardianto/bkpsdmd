<?php
include "db.php";
include "auth.php";
requireRole('user');
?>


<?php
$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT fullname, nip, profile_pic FROM users WHERE id=" . $_SESSION['user_id']);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f3f3f3;
      padding: 50px;
      text-align: center;
    }
    .box {
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      display: inline-block;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #f44336;
      color: white;
      text-decoration: none;
      border-radius: 8px;
    }
    a:hover {
      background: #e53935;
    }
  </style>
</head>
<body>
  <div class="box">
    <h1>Welcome, <?php echo $_SESSION['fullname']; ?> 🎉</h1>

    <p>NIP: <?php echo $user['nip']; ?> </p>

    <p>You are now logged in to your dashboard.</p>

    <?php if ($user['profile_pic']): ?>
    <img src="uploads/profile_pics/<?php echo $user['profile_pic']; ?>" 
       alt="Profile Picture" style="width:120px; height:120px; border-radius:50%;">
    <?php else: ?>

    <img src="uploads/profile_pics/default.png" 
       alt="Default Profile" style="width:120px; height:120px; border-radius:50%;">
    <?php endif; ?>

    <p><a href="profile.php">👤 View / Edit Profile</a></p>

    <a href="logout.php">Logout</a>
  </div>
</body>
</html>
