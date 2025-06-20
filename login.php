<?php
include_once(__DIR__ . "/config.php"); // Gunakan __DIR__ untuk path absolut yang aman

// --- Poin 2: Redirect jika sudah login ---
// Jika user sudah login, langsung redirect ke index.php
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = ""; // inisialisasi pesan error

// --- Poin 3: Periksa pesan error dari config.php (jika koneksi DB gagal) ---
if (isset($_SESSION['error_message_db'])) {
    $error = $_SESSION['error_message_db'];
    unset($_SESSION['error_message_db']); // Hapus pesan setelah ditampilkan
}

// Pastikan koneksi database aktif sebelum melakukan query
if ($conn === null) {
    // Error sudah ditangani di atas, tidak perlu proses form
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // --- Poin 4: Gunakan Prepared Statements untuk keamanan ---
        $stmt = $conn->prepare("SELECT ID_USER, USERNAME_USER, password FROM user WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email); // 's' untuk string (email)
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // --- Poin 5: Verifikasi password hashed ---
                if (password_verify($password, $user['password'])) {
                    // Login sukses
                    $_SESSION['user_id'] = $user['ID_USER']; // Konsisten menggunakan 'user_id'
                    $_SESSION['username'] = $user['USERNAME_USER']; // Simpan username juga
                    // $_SESSION['role'] = $user['ROLE']; // Jika ada kolom role di tabel user

                    header("Location: index.php"); // Redirect ke halaman utama
                    exit(); // Penting: Hentikan eksekusi skrip
                } else {
                    $error = "Email atau password salah."; // Pesan umum untuk keamanan
                }
            } else {
                $error = "Email atau password salah."; // Pesan umum untuk keamanan
            }
            $stmt->close(); // Tutup prepared statement
        } else {
            $error = "Terjadi kesalahan sistem saat memproses login. Mohon coba lagi.";
            error_log("Prepare statement failed in login.php: " . $conn->error);
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
            color: #CB6040; /* Ubah warna teks input agar terlihat */
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