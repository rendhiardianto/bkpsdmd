<?php
include "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM users WHERE id=$id LIMIT 1");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $role = $conn->real_escape_string($_POST['role']);
    $conn->query("UPDATE users SET fullname='$fullname', role='$role' WHERE id=$id");
    echo "<script>alert('User updated!'); window.location='admin_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit User</title></head>
<body>
  <h2>Edit User</h2>
  <form method="POST">
    <label>Full Name:</label><br>
    <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required><br><br>

    <label>Role:</label><br>
    <select name="role">
      <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>User</option>
      <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
    </select><br><br>

    <button type="submit">Save Changes</button>
  </form>
  <a href="admin_dashboard.php">⬅ Back</a>
</body>
</html>
