<?php
session_start();

// session_start();
include_once("config.php"); 

$error = ""; // inisialisasi

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // Login sukses
                $_SESSION['id_user'] = $user['ID_USER'];
                $_SESSION['username'] = $user['USERNAME_USER'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
    } else {
        $error = "Mohon isi semua data.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Log In</title>
    <style>
        body{
            background-color: #CB6040;
            font-family: "Inknut Antiqua";
        }
        .signup-card{
            background-color: #FFF9E8;
            border-radius: 10px;
            padding: 2rem;
            max-width: 600px;
            margin: 5rem auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-control{
            background-color: #FFF9E8;
            color: white;
            text-align: center;
        }
        .form-control::placeholder{
            color: #CB6040;
            text-align: center;
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
        .login-link{
            font-size: 0.8rem;
            text-align: center;
            display: block;
            margin-top: 0.5rem;
            color: #8b4e37;
        }
        .error-msg {
            color: red;
            margin-top: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="signup-card text-center">
        <h2 class="mb-4" style="color:#CB6040;">LOG IN</h2>
        
        <?php if (!empty($error)) : ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="E-Mail" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-custom">LOGIN</button>
            <a href="signup.php" class="login-link">Don't have an account?</a>
        </form>
    </div>
</body>
</html>
