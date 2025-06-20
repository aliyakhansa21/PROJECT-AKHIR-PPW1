<?php
session_start();
include '../config.php';

$id_user = $_SESSION['user_id'] ?? null;
if (!$id_user) {
    $_SESSION['error_message'] = "Anda harus login terlebih dahulu.";
    header("Location: ../login.php");
    exit();
}

$id_layanan = $_SESSION['id_layanan'] ?? null;
$id_kursi = $_SESSION['id_kursi'] ?? null;
$id_ruangan = $_SESSION['id_ruangan'] ?? null;
$id_sesi = $_SESSION['id_sesi'] ?? null;
$tanggal_reservasi = $_SESSION['tanggal_reservasi'] ?? null;

if (!$id_layanan || !$id_sesi || (!$id_kursi && !$id_ruangan) || !$tanggal_reservasi) {
    $_SESSION['error_message'] = "Data reservasi tidak lengkap. Silakan coba lagi dari awal.";
    header("Location: ringkasan_reservasi.php");
    exit();
}

$stmt_sesi = $conn->prepare("SELECT JAM_MULAI, JAM_SELESAI FROM sesi WHERE ID_SESI = ?");
$stmt_sesi->bind_param("i", $id_sesi);
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
$status_reservasi = 'DIKONFIRMASI';

$stmt_cek_user_aktif = $conn->prepare("SELECT ID_RESERVASI FROM reservasi WHERE ID_USER = ? AND STATUS IN ('PENDING', 'DIKONFIRMASI') AND TANGGAL_RESERVASI >= CURDATE()");
$stmt_cek_user_aktif->bind_param("i", $id_user);
$stmt_cek_user_aktif->execute();
$stmt_cek_user_aktif->store_result();

if ($stmt_cek_user_aktif->num_rows > 0) {
    $_SESSION['error_message'] = "Anda sudah memiliki reservasi aktif yang belum selesai. Mohon selesaikan reservasi Anda yang sebelumnya.";
    header("Location: ringkasan_reservasi.php");
    $stmt_cek_user_aktif->close();
    exit();
}
$stmt_cek_user_aktif->close();

$unit_id_to_check = $id_kursi ?? $id_ruangan;
$unit_column_name = $id_kursi ? 'ID_KURSI' : 'ID_RUANGAN';

$stmt_cek_overlap = $conn->prepare("SELECT ID_RESERVASI FROM reservasi WHERE " . $unit_column_name . " = ? AND TANGGAL_RESERVASI = ? AND ID_SESI = ? AND STATUS IN ('PENDING', 'DIKONFIRMASI')");
$stmt_cek_overlap->bind_param("isi", $unit_id_to_check, $tanggal_reservasi, $id_sesi);
$stmt_cek_overlap->execute();
$stmt_cek_overlap->store_result();

if ($stmt_cek_overlap->num_rows > 0) {
    $_SESSION['error_message'] = "Unit ini sudah dipesan pada waktu yang Anda pilih. Mohon pilih waktu atau unit lain.";
    header("Location: pilih_sesi.php");
    $stmt_cek_overlap->close();
    exit();
}
$stmt_cek_overlap->close();



try {
    $sql_insert = "INSERT INTO reservasi (ID_USER, ID_RUANGAN, ID_KURSI, ID_LAYANAN, ID_SESI, TANGGAL_RESERVASI, JAM_MULAI, JAM_SELESAI, STATUS, WAKTU_BUAT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    $stmt_insert->bind_param("iiiiisssss", 
        $id_user,
        $id_ruangan,
        $id_kursi,
        $id_layanan,
        $id_sesi,
        $tanggal_reservasi,
        $jam_mulai,
        $jam_selesai,
        $status_reservasi,
        $waktu_buat
    );
    
    if (!$stmt_insert->execute()) {
        throw new Exception("Gagal menyimpan reservasi: " . $stmt_insert->error);
    }
    $stmt_insert->close();

    if ($id_kursi) {
        $stmt_update_kursi = $conn->prepare("UPDATE kursi SET STATUS_KURSI = 'TERPESAN' WHERE ID_KURSI = ?");
        $stmt_update_kursi->bind_param("i", $id_kursi);
        if (!$stmt_update_kursi->execute()) {
            throw new Exception("Gagal update status kursi");
        }
        $stmt_update_kursi->close();
    } elseif ($id_ruangan) {
        $stmt_update_ruangan = $conn->prepare("UPDATE ruangan SET STATUS_RUANGAN = 'TERPESAN' WHERE ID_RUANGAN = ?");
        $stmt_update_ruangan->bind_param("i", $id_ruangan);
        if (!$stmt_update_ruangan->execute()) {
            throw new Exception("Gagal update status ruangan");
        }
        $stmt_update_ruangan->close();
    }

    $conn->commit();

    unset(
        $_SESSION['id_layanan'],
        $_SESSION['id_kursi'],
        $_SESSION['id_ruangan'],
        $_SESSION['id_sesi'],
        $_SESSION['tanggal_reservasi']
    );

    $_SESSION['success_message'] = "Reservasi Anda berhasil dibuat!";
    header("Location: ../index.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "Terjadi kesalahan saat menyimpan reservasi: " . $e->getMessage();
    header("Location: ringkasan_reservasi.php");
    exit();
}

$conn->close();
?>