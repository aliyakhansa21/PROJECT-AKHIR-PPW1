<?php
include_once("config.php");

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $delete = mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");

  if ($delete) {
    header("Location: admin_users.php");
    exit;
  } else {
    echo "Gagal menghapus user!";
  }
} else {
  echo "ID tidak valid!";
}
?>
