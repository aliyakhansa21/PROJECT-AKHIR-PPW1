<?php
include_once("config.php");
requireLogin();

// if(!isset($_GET['id'])){
//     header("Location: index.php");
//     exit();
// }

$id_user = $_SESSION['id_user'];

// Ambil data user dari database
$result = mysqli_query($conn, "SELECT USERNAME_USER, EMAIL FROM user WHERE ID_USER = $id_user");


if (mysqli_num_rows($result) === 0) {
    echo "User tidak ditemukan.";
    exit();
}

$user = mysqli_fetch_assoc($result);
$errors = [];
$success = '';

if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $update = mysqli_query($conn, "UPDATE user SET username_user='$username', email='$email' WHERE id_user=$id_user");

    // Validasi
    if (empty($username)){
        $errors[] = "Username tidak boleh kosong.";
    } 

    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    $cek_email = mysqli_query($conn, "SELECT id_user FROM user WHERE email = '$email' AND id_user != $id_user");
    if (mysqli_num_rows($cek_email) > 0) {
        $errors[] = "Email sudah digunakan oleh pengguna lain.";
    }


    if (empty($errors)) {
        $query = "UPDATE user SET username_user = '$username', email = '$email' WHERE id_user = $id_user";
        if (mysqli_query($conn, $query)) {
            $success = "Profil berhasil diperbarui.";
            $user['username'] = $username;
            $user['email'] = $email;
        } else {
            $errors[] = "Gagal menyimpan perubahan.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #CB6040;
            font-family: "Inknut Antiqua";
        }
        .edit-profile-container {
            max-width: 500px;
            margin: 60px auto;
            background-color: #FFF9E8;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #CB6040;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25);
        }
        .btn-custom{
            background-color: #CB6040;
            color: #FFF9E8;
            border-radius: 20px;
            width: 60%;
        }
        .btn-custom:hover{
            background-color: #CB6040;
            opacity: 90%;
        }
    </style>
</head>
<body>

<div class="container edit-profile-container">
    <h3 class="mb-4 text-center">Edit Profil</h3>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                value="<?= htmlspecialchars($_POST['username'] ?? $user['USERNAME_USER']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                value="<?= htmlspecialchars($_POST['email'] ?? $user['EMAIL']) ?>">
        </div>
        <div class="d-flex justify-content-center">
            <button type="submit" name="update" class="btn btn-custom" text-center>Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>
