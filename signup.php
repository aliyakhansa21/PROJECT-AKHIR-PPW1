<?php
include_once("config.php");

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong";
    }

    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }

    $check_query = "SELECT * FROM user WHERE username_user = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result === false) {
        $errors[] = "Kesalahan saat memeriksa username/email" .mysqli_error($conn);
        $errors[] = "Query: " .$check_query;
    } 

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO user (username_user, email, password) VALUES ('$username', '$email', '$hashed_password')";

        if (mysqli_query($conn, $insert_query)) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $errors[] = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #CB6040;
            font-family: "Inknut Antiqua", serif;
        }

        .signup-card {
            background-color: #FFF9E8;
            border-radius: 10px;
            padding: 2rem;
            max-width: 600px;
            margin: 5rem auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-control {
            background-color: #FFF9E8;
            border: 2px solid #CB6040;
            color: #CB6040;
            text-align: center;
        }

        .form-control::placeholder {
            color: #CB6040;
            text-align: center;
        }

        .btn-custom {
            background-color: #CB6040;
            color: #FFF9E8;
            border-radius: 20px;
            width: 60%;
        }

        .btn-custom:hover {
            background-color: #a94c2f;
            color: white;
        }

        .login-link {
            font-size: 0.9rem;
            text-align: center;
            display: block;
            margin-top: 0.5rem;
            color: #8b4e37;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="signup-card text-center">
        <h2 style="color:#CB6040;">SIGN UP</h2>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0" style="list-style: none; padding-left: 0;">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="E-Mail" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" name="register" class="btn btn-custom">CREATE ACCOUNT</button>
            <a href="login.php" class="login-link">Already have an account?</a>
        </form>
    </div>
</body>
</html>
