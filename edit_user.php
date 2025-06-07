

<?php
include_once("config.php");

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $result = mysqli_query($conn, "SELECT * FROM user WHERE id_user = $id");

  if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
  } else {
    echo "User tidak ditemukan!";
    exit;
  }
} else {
  echo "ID tidak valid!";
  exit;
}

if (isset($_POST['update'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];

  $update = mysqli_query($conn, "UPDATE user SET username_user='$username', email='$email' WHERE id_user=$id");

  if ($update) {
    header("Location: admin_users.php");
    exit;
  } else {
    echo "Gagal update data!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <style>
    body {
      background-color: #fff7e6;
      font-family: Georgia, serif;
      text-align: center;
    }

    form {
      background-color: #fff;
      border: 1px solid #c25c32;
      padding: 20px;
      display: inline-block;
      margin-top: 50px;
    }

    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 8px;
      margin: 5px 0;
    }

    input[type="submit"] {
      background-color: #c25c32;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      margin-top: 10px;
    }

    input[type="submit"]:hover {
      background-color: #a94426;
    }
  </style>
</head>
<body>

  <h1>Edit Data User</h1>

  <form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?= isset($user['username_user']) ? htmlspecialchars($user['username_user']) : ''; ?>" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required><br>

    <input type="submit" name="update" value="Update">
  </form>

</body>
</html>
