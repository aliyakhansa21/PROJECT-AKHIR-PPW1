<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8">
    <title>Tabel Admin User</title>
    <style>
      body {
        background-color: #fff7e6;
        font-family: Georgia, serif;
        text-align: center;
      }

      h1 {
        color: #c25c32;
        font-weight: bold;
      }

      form{
        margin-bottom: 20px;
      }

      input[type="text"] {
        padding: 8px;
        width: 250px;
        border: 1px solid #c25c32;
        border-radius: 4px;
        font-size: 14px;
      }

      button{
        padding: 8px 16px;
        background-color: #c25c32;
        color:#fff7e6;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }

      button:hover{
        background-color: #a94426;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
        width: 90%;
        max-width: 800px;
        font-size: 16px;
      }

      th, td {
        border: 1px solid #c25c32;
        padding: 12px;
        text-align: center;
      }

      th {
        background-color: #fff3dd;
        color: #c25c32;
      }

      td {
        background-color: #fff;
      }

      a.button {
        padding: 5px 10px;
        text-decoration: none;
        background-color: #c25c32;
        color: #fff;
        border-radius: 5px;
        margin: 0 3px;
        font-size: 14px;
      }

      a.button:hover {
        background-color: #a94426;
      }
    </style>
  </head>
  <body>

    <div class="container">
      <h1>TABEL ADMIN USER</h1>

      <!--Form Pencarian-->
      <form method="get" action="admin_users.php">
        <input type="text" name="cari" placeholder="Cari username atau email..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''?>">
        <button type="submit">Cari</button>
      </form>

      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>ID User</th>
            <th>Username</th>
            <th>Email</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include_once("config.php");

          $cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

          if ($cari != ''){
            $query = "SELECT * FROM user WHERE USERNAME_USER LIKE '%$cari%' OR EMAIL LIKE '%$cari%'";
          } else {
            $query = "SELECT * FROM user";
          }

          $result = mysqli_query($conn, $query);
          if (mysqli_num_rows($result) > 0) {
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {

              echo "<tr>";
              echo "<td>" . $no++ . "</td>";
              echo "<td>" . htmlspecialchars($row['ID_USER']) . "</td>";
              echo "<td>" . htmlspecialchars($row['USERNAME_USER']) . "</td>";
              echo "<td>" . htmlspecialchars($row['EMAIL']) . "</td>";
              echo "<td>
                      <a class='button' href='edit_user.php?id=" . urlencode($row['ID_USER']) . "'>Edit</a>
                      <a class='button' href='delete_user.php?id=" . urlencode($row['ID_USER']) . "' onclick=\"return confirm('Yakin ingin menghapus user ini?');\">Hapus</a>
                    </td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>Tidak ada data user</td></tr>";
          }

          mysqli_close($conn);
          ?>
        </tbody>
      </table>
    </div>

  </body>
</html>
