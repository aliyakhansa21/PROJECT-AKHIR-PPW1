<?php
session_start();
include '../config.php';

$id_user = 1;
$id_layanan = $_SESSION['id_layanan'];
$id_kursi = $_SESSION['id_kursi'];
$id_ruangan = $_SESSION['id_ruangan'];
$id_sesi = $_SESSION['id_sesi'];

$waktu_buat = date('Y-m-d H:i:s');

$query = "INSERT INTO reservasi (id_user, id_ruangan, id_kursi, id_layanan, id_sesi, status, waktu_buat)
            VALUES('$id_user',
            " . ($id_ruangan ? "'$id_ruangan'" : "NULL") . ",
            " . ($id_kursi ? "'$id_kursi'" : "NULL") . ",
            '$id_layanan', '$id_sesi', 'approve', '$waktu_buat')";

mysqli_query($conn, $query);

if ($id_kursi){
    mysqli_query($conn, "UPDATE kursi SET status_kursi = 'TERPESAN' WHERE id_kursi='$id_kursi'");
}

unset($_SESSION['id_layanan'], $_SESSION['id_kursi'], $_SESSION['id_ruangan'], $_SESSION['id_sesi']);


// Jika berhasil
$_SESSION['success_message'] = "Reservasi Anda berhasil dibuat!";
header("Location: index.php");
exit();

// Jika gagal
$_SESSION['error_message'] = "Terjadi kesalahan saat menyimpan reservasi: " . $stmt_insert->error;
header("Location: ringkasan_reservasi.php");
exit();

// Jika validasi user punya reservasi aktif gagal
$_SESSION['error_message'] = "Anda sudah memiliki reservasi yang disetujui dan belum selesai. Mohon selesaikan reservasi Anda yang sebelumnya.";
header("Location: ringkasan_reservasi.php");
exit();

// Jika validasi unit overlap gagal
$_SESSION['error_message'] = "Unit ini sudah dipesan pada waktu yang Anda pilih. Mohon pilih waktu atau unit lain.";
header("Location: sukses.php");
exit();

// session_unset();
// session_destroy();

header("Location: sukses.php");
exit;
?>