<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['id_sesi'] = $_POST['id_sesi'];
}


$id_layanan = $_SESSION['id_layanan'] ?? null;
$id_sesi = $_SESSION['id_sesi'] ?? null;
$tanggal_reservasi = $_SESSION['tanggal_reservasi'] ?? null; // Ambil tanggal reservasi
$id_kursi = $_SESSION['id_kursi'] ?? null;
$id_ruangan = $_SESSION['id_ruangan'] ?? null;

if (!$id_layanan || !$id_sesi || (!$id_kursi && !$id_ruangan) || !$tanggal_reservasi) {
    echo "Data reservasi tidak lengkap. <a href='pilih_layanan.php'>Kembali</a>";
    exit;
}

$layanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT NAMA_LAYANAN, TIPE FROM layanan WHERE ID_LAYANAN = '$id_layanan'"));
$sesi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT NAMA_SESI, JAM_MULAI, JAM_SELESAI FROM sesi WHERE ID_SESI = '$id_sesi'"));


$unit_text = ""; // Variabel untuk teks unit yang dipilih
if ($id_kursi){
    // Query dengan JOIN untuk ambil nama ruangan juga
    $stmt_kursi = $conn->prepare("SELECT k.NOMOR_KURSI, r.NAMA_RUANGAN
                                FROM kursi k
                                JOIN ruangan r ON k.ID_RUANGAN = r.ID_RUANGAN
                                WHERE k.ID_KURSI = ?");
    $stmt_kursi->bind_param("i", $id_kursi);
    $stmt_kursi->execute();
    $result_kursi = $stmt_kursi->get_result();
    $kursi = $result_kursi->fetch_assoc();
    $stmt_kursi->close();
    if ($kursi) {
        $unit_text = "Kursi " . htmlspecialchars($kursi['NOMOR_KURSI']) . " (Ruangan " . htmlspecialchars($kursi['NAMA_RUANGAN']) . ")";
    }
} elseif ($id_ruangan){
    $stmt_ruangan = $conn->prepare("SELECT NAMA_RUANGAN FROM ruangan WHERE ID_RUANGAN = ?");
    $stmt_ruangan->bind_param("i", $id_ruangan);
    $stmt_ruangan->execute();
    $result_ruangan = $stmt_ruangan->get_result();
    $ruangan = $result_ruangan->fetch_assoc();
    $stmt_ruangan->close();
    if ($ruangan) {
        $unit_text = "Ruangan " . htmlspecialchars($ruangan['NAMA_RUANGAN']);
    }
}
?> 

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Reservasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styling -->
    <style>
        * {
            background-color: #FFF9E8;
            color: #CB6040;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "Inknut Antiqua", serif;
            font-size: 20px;
            background-color: #FFF9E8;
        }
        .card-title, .list-group-item {
            color: #0D6172;
        }
        .btn {
            background-color: #CB6040;
            color: #FFF9E8;
        }
        .progress-bar {
            background-color: #CB6040;
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
                        <h2 class="card-title text-center mb-4">Ringkasan Reservasi</h2>
                        <ul class="list-group mb-4">
                            <li class="list-group-item"><strong>Unit:</strong> <?= htmlspecialchars($unit_text) ?></li>
                            <li class="list-group-item"><strong>Tanggal:</strong> <?= htmlspecialchars($tanggal_reservasi) ?></li>
                            <li class="list-group-item">
                                <strong>Sesi:</strong> <?= htmlspecialchars($sesi['NAMA_SESI']) ?> (<?= $sesi['JAM_MULAI'] ?> - <?= $sesi['JAM_SELESAI'] ?>)
                            </li>
                        </ul>

                        <form action="proses_reservasi.php" method="post" class="d-grid">
                            <button type="submit" class="btn btn-lg">Reservasi Sekarang</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Ambil pesan dari session (yang sudah di-escape di PHP)
        const successMessage = <?php echo isset($_SESSION['success_message']) ? json_encode($_SESSION['success_message']) : 'null'; ?>;
        const errorMessage = <?php echo isset($_SESSION['error_message']) ? json_encode($_SESSION['error_message']) : 'null'; ?>;

        // Hapus pesan dari session setelah diambil oleh JavaScript
        <?php
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        ?>

        // Tampilkan alert jika ada pesan
        if (successMessage) {
            alert(successMessage);
            // Opsional: Redirect ke halaman lain setelah alert, misalnya home atau riwayat reservasi
            // window.location.href = '../index.php'; // Contoh: redirect ke home
            // window.location.href = 'riwayat_reservasi.php'; // Contoh: redirect ke riwayat
        } else if (errorMessage) {
            alert(errorMessage);
        }
    </script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>