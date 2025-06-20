<?php
session_start(); // Memulai sesi PHP untuk mengakses data sesi
include '../config.php'; // Memasukkan file konfigurasi database

// --- Poin 1: Ambil ID User dari Session (Bukan Hardcode) ---
// Ganti $id_user = 1; dengan mengambil ID pengguna yang sedang login dari session.
// Asumsi ID pengguna disimpan di $_SESSION['user_id'] setelah login.
$id_user = $_SESSION['user_id'] ?? null; // Gunakan null coalescing operator untuk keamanan

// --- Poin 2: Validasi Data Session Lengkap ---
// Pastikan semua data yang dibutuhkan dari session sudah ada sebelum melanjutkan.
$id_layanan = $_SESSION['id_layanan'] ?? null;
$id_kursi = $_SESSION['id_kursi'] ?? null;
$id_ruangan = $_SESSION['id_ruangan'] ?? null;
$id_sesi = $_SESSION['id_sesi'] ?? null;
$tanggal_reservasi = $_SESSION['tanggal_reservasi'] ?? null; // BARU: Ambil tanggal reservasi dari session

// Jika ada data yang kosong, redirect dengan pesan error.
if (!$id_user || !$id_layanan || !$id_sesi || (!$id_kursi && !$id_ruangan) || !$tanggal_reservasi) {
    $_SESSION['error_message'] = "Data reservasi tidak lengkap. Silakan coba lagi dari awal.";
    header("Location: ringkasan_reservasi.php"); // Atau ke halaman awal reservasi
    exit();
}

// --- Poin 3: Ambil Detail Sesi (JAM_MULAI, JAM_SELESAI) ---
// Kita butuh JAM_MULAI dan JAM_SELESAI dari tabel 'sesi' untuk disimpan di tabel 'reservasi'.
$stmt_sesi = $conn->prepare("SELECT JAM_MULAI, JAM_SELESAI FROM sesi WHERE ID_SESI = ?");
$stmt_sesi->bind_param("i", $id_sesi); // 'i' untuk integer
$stmt_sesi->execute();
$result_sesi = $stmt_sesi->get_result();
$sesi = $result_sesi->fetch_assoc();
$stmt_sesi->close();

if (!$sesi) {
    $_SESSION['error_message'] = "Sesi yang dipilih tidak valid.";
    header("Location: pilih_sesi.php");
    exit();
}

$jam_mulai = $sesi['JAM_MULAI'];
$jam_selesai = $sesi['JAM_SELESAI'];
$waktu_buat = date('Y-m-d H:i:s');
$status_reservasi = 'DIKONFIRMASI'; // Atau 'PENDING' jika butuh approval admin

// --- Poin 4: Validasi Ketersediaan (Pencegahan Double Booking) ---
// Ini sangat penting untuk mencegah dua user memesan unit yang sama pada waktu yang sama.
// a. Cek apakah user sudah punya reservasi aktif yang belum selesai.
//    Asumsi: reservasi dianggap aktif jika statusnya 'PENDING' atau 'DIKONFIRMASI'.
$stmt_cek_user_aktif = $conn->prepare("SELECT ID_RESERVASI FROM reservasi WHERE ID_USER = ? AND STATUS IN ('PENDING', 'DIKONFIRMASI')");
$stmt_cek_user_aktif->bind_param("i", $id_user);
$stmt_cek_user_aktif->execute();
$stmt_cek_user_aktif->store_result(); // Simpan hasil query untuk bisa dihitung barisnya

if ($stmt_cek_user_aktif->num_rows > 0) {
    $_SESSION['error_message'] = "Anda sudah memiliki reservasi aktif yang belum selesai. Mohon selesaikan reservasi Anda yang sebelumnya.";
    header("Location: ringkasan_reservasi.php"); // Kembali ke ringkasan atau ke halaman riwayat reservasi
    $stmt_cek_user_aktif->close();
    exit();
}
$stmt_cek_user_aktif->close();

// b. Cek apakah unit (kursi/ruangan) sudah dipesan pada waktu yang sama.
$unit_id_to_check = $id_kursi ?? $id_ruangan; // Ambil ID unit yang dipilih
$unit_column_name = $id_kursi ? 'ID_KURSI' : 'ID_RUANGAN'; // Tentukan kolom mana yang akan dicek

$stmt_cek_overlap = $conn->prepare("SELECT ID_RESERVASI FROM reservasi WHERE
                                    " . $unit_column_name . " = ? AND TANGGAL_RESERVASI = ? AND ID_SESI = ? AND STATUS IN ('PENDING', 'DIKONFIRMASI')");
$stmt_cek_overlap->bind_param("isi", $unit_id_to_check, $tanggal_reservasi, $id_sesi);
$stmt_cek_overlap->execute();
$stmt_cek_overlap->store_result();

if ($stmt_cek_overlap->num_rows > 0) {
    $_SESSION['error_message'] = "Unit ini sudah dipesan pada waktu yang Anda pilih. Mohon pilih waktu atau unit lain.";
    header("Location: pilih_sesi.php"); // Kembali ke halaman pilih sesi
    $stmt_cek_overlap->close();
    exit();
}
$stmt_cek_overlap->close();

// --- Poin 5: INSERT Data Reservasi ke Database (Menggunakan Prepared Statements) ---
// Ini adalah perubahan paling krusial untuk mencegah SQL Injection dan memastikan keamanan.
// Query yang kamu gunakan sebelumnya rentan.
$sql_insert = "INSERT INTO reservasi (ID_USER, ID_RUANGAN, ID_KURSI, ID_LAYANAN, ID_SESI, TANGGAL_RESERVASI, JAM_MULAI, JAM_SELESAI, STATUS, WAKTU_BUAT)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);

// 'i' untuk integer, 's' untuk string.
// Perhatikan: $id_ruangan dan $id_kursi bisa NULL. MySQLi secara otomatis akan menangani NULL
// jika tipe yang diharapkan adalah INT dan nilai variabelnya memang NULL.
$stmt_insert->bind_param("iiisssssss", // Sesuaikan jumlah dan tipe sesuai kolom
    $id_user,
    $id_ruangan,
    $id_kursi,
    $id_layanan,
    $id_sesi,
    $tanggal_reservasi, // BARU
    $jam_mulai,       // BARU
    $jam_selesai,     // BARU
    $status_reservasi,
    $waktu_buat
);

// --- Poin 6: Eksekusi Query dan Penanganan Hasil ---
if ($stmt_insert->execute()) {
    // --- Poin 7: Update Status Kursi/Ruangan ---
    // Update status unit menjadi 'TERPESAN' setelah reservasi berhasil dibuat.
    if ($id_kursi) {
        $stmt_update_kursi = $conn->prepare("UPDATE kursi SET STATUS_KURSI = 'TERPESAN' WHERE ID_KURSI = ?");
        $stmt_update_kursi->bind_param("i", $id_kursi);
        $stmt_update_kursi->execute();
        $stmt_update_kursi->close();
    } elseif ($id_ruangan) {
        // Asumsi ada kolom STATUS_RUANGAN di tabel ruangan
        $stmt_update_ruangan = $conn->prepare("UPDATE ruangan SET STATUS_RUANGAN = 'TERPESAN' WHERE ID_RUANGAN = ?");
        $stmt_update_ruangan->bind_param("i", $id_ruangan);
        $stmt_update_ruangan->execute();
        $stmt_update_ruangan->close();
    }

    // --- Poin 8: Bersihkan Session dan Redirect Sukses ---
    // Penting untuk menghapus data reservasi dari session setelah berhasil.
    unset(
        $_SESSION['id_layanan'],
        $_SESSION['id_kursi'],
        $_SESSION['id_ruangan'],
        $_SESSION['id_sesi'],
        $_SESSION['tanggal_reservasi'] // BARU: Hapus juga tanggal reservasi
    );

    $_SESSION['success_message'] = "Reservasi Anda berhasil dibuat!";
    header("Location: ../index.php"); // Redirect ke halaman utama atau halaman riwayat reservasi
    exit();
} else {
    // Jika eksekusi INSERT gagal
    $_SESSION['error_message'] = "Terjadi kesalahan saat menyimpan reservasi: " . $stmt_insert->error;
    header("Location: ringkasan_reservasi.php"); // Kembali ke halaman ringkasan dengan pesan error
    exit();
}

$stmt_insert->close(); // Tutup prepared statement
$conn->close(); // Tutup koneksi database (opsional, PHP akan menutupnya secara otomatis di akhir skrip)

// Bagian kode di bawah ini tidak akan pernah dieksekusi karena ada exit() di atas.
// Mereka adalah contoh pesan error yang sudah dihandle di bagian if/else di atas.
// Hapus bagian ini dari kode finalmu.
// $_SESSION['error_message'] = "Terjadi kesalahan saat menyimpan reservasi: " . $stmt_insert->error;
// header("Location: ringkasan_reservasi.php");
// exit();
// $_SESSION['error_message'] = "Anda sudah memiliki reservasi yang disetujui dan belum selesai. Mohon selesaikan reservasi Anda yang sebelumnya.";
// header("Location: ringkasan_reservasi.php");
// exit();
// $_SESSION['error_message'] = "Unit ini sudah dipesan pada waktu yang Anda pilih. Mohon pilih waktu atau unit lain.";
// header("Location: sukses.php");
// exit();
// header("Location: sukses.php");
// exit;
?>