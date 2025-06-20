<?php
// --- Poin 1: Pindahkan session_start() ke sini (jika ini file pertama yang di-include) ---
// Ini adalah tempat terbaik untuk session_start() karena file ini akan di-include di hampir semua file lain.
// Ini memastikan session dimulai SEBELUM ada output ke browser.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi database
$host = "localhost";
$username = "root";
$password = ""; // Sesuaikan dengan password MySQL Anda
$database = "studyhub";

// --- Poin 2: Coba koneksi dan handle error dengan lebih baik ---
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi: Jika gagal, catat error ke log dan set pesan error di session
if (!$conn) {
    error_log("Koneksi database gagal: " . mysqli_connect_error(), 0); // 0 = log ke SAPI log (php_error_log)
    // Jangan gunakan die() di file config.php jika ingin redirect atau menampilkan pesan user-friendly.
    // Set pesan error di session, lalu file yang meng-include config.php akan menanganinya.
    $_SESSION['error_message'] = "Terjadi masalah pada koneksi database. Mohon coba lagi nanti.";
    // Tidak ada header() atau exit() di sini, karena config.php di-include.
    // File yang memanggil include ini harus menangani redirect.
}

// --- Poin 3: Perbaikan fungsi isLoggedIn() dan requireLogin() ---
// Anda menggunakan $_SESSION['user_id'] di isLoggedIn() tapi $_SESSION['id_user'] di requireLogin().
// Pastikan konsisten, umumnya pakai user_id.
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) { // Ubah id_user menjadi user_id
        header("Location: login.php"); // Pastikan path ke login.php benar
        exit();
    }
}

// Fungsi untuk upload file (tetap sama, di sini tidak ada perubahan terkait masalah utama)
function uploadFile($file, $target_dir = "uploads/mahasiswa/") {
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file adalah gambar
    // Perbaikan: gunakan $_FILES, bukan $_POST["submit"] untuk cek ini
    if (!isset($file["tmp_name"]) || empty($file["tmp_name"])) {
        return array('success' => false, 'message' => 'File tidak ditemukan atau kosong.');
    }
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return array('success' => false, 'message' => 'File bukan gambar.');
    }

    // Cek ukuran file (max 5MB)
    if ($file["size"] > 5000000) {
        return array('success' => false, 'message' => 'File terlalu besar. Maksimal 5MB.');
    }

    // Hanya allow format tertentu
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return array('success' => false, 'message' => 'Hanya format JPG, JPEG, PNG & GIF yang diizinkan.');
    }

    // Generate nama file unik
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'filename' => $new_filename);
    } else {
        return array('success' => false, 'message' => 'Error saat upload file. Kode error: ' . $file['error']); // Tambah detail error
    }
}

// Fungsi untuk hapus file (tetap sama)
function deleteFile($filename, $dir = "uploads/mahasiswa/") {
    if ($filename && file_exists($dir . $filename)) {
        unlink($dir . $filename);
    }
}