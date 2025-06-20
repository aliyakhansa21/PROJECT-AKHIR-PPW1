<?php
ini_set('display_errors', 0); // Nonaktifkan tampilan error di browser
ini_set('log_errors', 1);    // Aktifkan logging error ke php_error_log
error_reporting(E_ALL);      // Laporkan semua jenis error
session_start();
include '../config.php';

// Pastikan layanan dan unit sudah ada di session
if (!isset($_SESSION['id_layanan']) || (!isset($_SESSION['id_kursi']) && !isset($_SESSION['id_ruangan']))) {
    header('Location: pilih_unit.php'); // Kembali jika data unit belum lengkap
    exit;
}

$id_layanan = $_SESSION['id_layanan'];
$id_kursi = $_SESSION['id_kursi'] ?? null;
$id_ruangan = $_SESSION['id_ruangan'] ?? null;

// Mengidentifikasi unit yang dipilih untuk filter ketersediaan
$unit_id_selected = null;
$unit_column_name = null;

if ($id_kursi) {
    $unit_id_selected = $id_kursi;
    $unit_column_name = 'ID_KURSI';
} elseif ($id_ruangan) {
    $unit_id_selected = $id_ruangan;
    $unit_column_name = 'ID_RUANGAN';
} else {
    // Seharusnya tidak terjadi karena sudah divalidasi di atas
    $_SESSION['error_message'] = "Unit reservasi tidak ditemukan. Silakan pilih kembali.";
    header('Location: pilih_unit.php');
    exit;
}

// Simpan pilihan sesi dan tanggal ke session saat POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_sesi']) && isset($_POST['tanggal_reservasi'])) {
        $_SESSION['id_sesi'] = $_POST['id_sesi'];
        $_SESSION['tanggal_reservasi'] = $_POST['tanggal_reservasi'];
        header('Location: ringkasan_reservasi.php');
        exit;
    }
}

$tanggal_pilihan = isset($_POST['tanggal_reservasi']) ? $_POST['tanggal_reservasi'] : (isset($_SESSION['tanggal_reservasi']) ? $_SESSION['tanggal_reservasi'] : date('Y-m-d'));

// Query untuk mendapatkan sesi yang masih tersedia untuk unit dan tanggal yang dipilih
// QUERY KOMPLEKS (JOIN)
$sql = "SELECT s.*
        FROM sesi s
        LEFT JOIN reservasi r ON s.ID_SESI = r.ID_SESI
            AND r.TANGGAL_RESERVASI = ?
            AND r." . $unit_column_name . " = ?
            AND r.STATUS IN ('APPROVE', 'REJECT')
        WHERE r.ID_RESERVASI IS NULL
        ORDER BY s.JAM_MULAI ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $tanggal_pilihan, $unit_id_selected);
$stmt->execute();
$query_sesi_tersedia = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Pilih Sesi</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Custom Styling -->
        <style>
            * {
                background-color: #FFF9E8;
                color: #CB6040;
                padding: 0;
                margin: 0;
            }
            body {
                font-family: "Inknut Antiqua", serif;
                font-size: 20px;
                background-color: #FFF9E8;
            }
            .card-body h2 {
                color: #0D6172;
            }
            .btn {
                background-color: #CB6040;
                color: #FFF9E8;
            }
            .progress-bar {
                background-color: #CB6040;
                color: #FFF9E8;
            }
        </style>
    </head>

    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 100%;" aria-valuenow="3" aria-valuemin="0" aria-valuemax="3">Langkah 3 dari 3</div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Pilih Tanggal & Sesi</h2>
                            <form action="pilih_sesi.php" method="post" id="dateForm">
                                <div class="mb-3">
                                    <label for="tanggalReservasi" class="form-label">Pilih Tanggal Reservasi:</label>
                                    <input type="date" class="form-control" id="tanggalReservasi" name="tanggal_reservasi"
                                        value="<?= htmlspecialchars($tanggal_pilihan) ?>"
                                        min="<?= date('Y-m-d'); ?>" required
                                        onchange="document.getElementById('dateForm').submit();"> <input type="hidden" name="id_layanan" value="<?= htmlspecialchars($id_layanan) ?>">
                                        <?php if ($id_kursi) { ?>
                                            <input type="hidden" name="id_kursi" value="<?= htmlspecialchars($id_kursi) ?>">
                                        <?php } elseif ($id_ruangan) { ?>
                                            <input type="hidden" name="id_ruangan" value="<?= htmlspecialchars($id_ruangan) ?>">
                                        <?php } ?>
                                </div>
                            </form>

                            <form action="pilih_sesi.php" method="post">
                                <input type="hidden" name="id_layanan" value="<?= htmlspecialchars($id_layanan) ?>">
                                <?php if ($id_kursi) { ?>
                                    <input type="hidden" name="id_kursi" value="<?= htmlspecialchars($id_kursi) ?>">
                                <?php } elseif ($id_ruangan) { ?>
                                    <input type="hidden" name="id_ruangan" value="<?= htmlspecialchars($id_ruangan) ?>">
                                <?php } ?>
                                <input type="hidden" name="tanggal_reservasi" value="<?= htmlspecialchars($tanggal_pilihan) ?>">


                                <div class="list-group mb-4">
                                    <?php
                                    if (mysqli_num_rows($query_sesi_tersedia) > 0) {
                                        while ($sesi = mysqli_fetch_assoc($query_sesi_tersedia)) {
                                    ?>
                                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <input class="form-check-input me-2" type="radio" name="id_sesi" value="<?= $sesi['ID_SESI'] ?>"
                                                        <?= (isset($_SESSION['id_sesi']) && $_SESSION['id_sesi'] == $sesi['ID_SESI']) ? 'checked' : '' ?> required>
                                                    <span class="fw-bold"><?= htmlspecialchars($sesi['NAMA_SESI']) ?></span>
                                                    <small class="d-block text-muted"><?= $sesi['JAM_MULAI'] ?> - <?= $sesi['JAM_SELESAI'] ?></small>
                                                </div>
                                            </label>
                                    <?php
                                        }
                                    } else {
                                        echo "<div class='alert alert-info text-center' role='alert'>Tidak ada sesi tersedia untuk unit dan tanggal ini.</div>";
                                    }
                                    ?>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="pilih_unit.php" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn" <?= (mysqli_num_rows($query_sesi_tersedia) == 0) ? 'disabled' : '' ?>>Lanjut</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>