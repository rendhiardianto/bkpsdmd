<?php
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id=$user_id LIMIT 1");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f6fa;
      padding: 50px;
    }
    .container {
      max-width: 500px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 20px; }
    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      background: #4cafef;
      color: #fff;
    }
    button:hover { background: #2196f3; }
    .logout {
      display: block;
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>User Profile</h2>

    <!-- Update Name -->
    <form action="update_profile.php" method="POST">
      <label>Full Name:</label>
      <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required>
      <label>Email (cannot change):</label>
      <input type="email" value="<?php echo $user['email']; ?>" disabled>
      <button type="submit">Update Profile</button>
    </form>

    <hr>

    <!-- Change Password -->
    <form action="change_password.php" method="POST">
      <label>Old Password:</label>
      <input type="password" name="old_password" required>
      <label>New Password:</label>
      <input type="password" name="new_password" required>
      <button type="submit">Change Password</button>
    </form>

    <a href="user_dashboard.php" class="logout">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
