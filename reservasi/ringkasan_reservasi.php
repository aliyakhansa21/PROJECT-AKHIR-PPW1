<?php
session_start();
include '../config.php';

$id_layanan = $_SESSION['id_layanan'] ?? null;
$id_sesi = $_SESSION['id_sesi'] ?? null;
$tanggal_reservasi = $_SESSION['tanggal_reservasi'] ?? null;
$id_kursi = $_SESSION['id_kursi'] ?? null;
$id_ruangan = $_SESSION['id_ruangan'] ?? null;

if (!$id_layanan || !$id_sesi || (!$id_kursi && !$id_ruangan) || !$tanggal_reservasi) {
    $_SESSION['error_message'] = "Data reservasi tidak lengkap. Silakan mulai dari awal.";
    header("Location: pilih_layanan.php");
    exit;
}

$stmt_layanan = $conn->prepare("SELECT NAMA_LAYANAN, TIPE FROM layanan WHERE ID_LAYANAN = ?");
$stmt_layanan->bind_param("i", $id_layanan);
$stmt_layanan->execute();
$result_layanan = $stmt_layanan->get_result();
$layanan = $result_layanan->fetch_assoc();
$stmt_layanan->close();

if (!$layanan) {
    $_SESSION['error_message'] = "Layanan tidak valid.";
    header("Location: pilih_layanan.php");
    exit;
}

$stmt_sesi = $conn->prepare("SELECT NAMA_SESI, JAM_MULAI, JAM_SELESAI FROM sesi WHERE ID_SESI = ?");
$stmt_sesi->bind_param("i", $id_sesi);
$stmt_sesi->execute();
$result_sesi = $stmt_sesi->get_result();
$sesi = $result_sesi->fetch_assoc();
$stmt_sesi->close();

if (!$sesi) {
    $_SESSION['error_message'] = "Sesi tidak valid.";
    header("Location: pilih_sesi.php");
    exit;
}

$unit_text = "";
if ($id_kursi) {
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
} elseif ($id_ruangan) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <div class="mb-4">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 100%;" aria-valuenow="3" aria-valuemin="0" aria-valuemax="3">Langkah 3 dari 3</div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Ringkasan Reservasi</h2>
                        <ul class="list-group mb-4">
                            <li class="list-group-item"><strong>Layanan:</strong> <?= htmlspecialchars($layanan['NAMA_LAYANAN']) ?></li>
                            <li class="list-group-item"><strong>Unit:</strong> <?= htmlspecialchars($unit_text) ?></li>
                            <li class="list-group-item"><strong>Tanggal:</strong> <?= htmlspecialchars(date('d-m-Y', strtotime($tanggal_reservasi))) ?></li>
                            <li class="list-group-item">
                                <strong>Sesi:</strong> <?= htmlspecialchars($sesi['NAMA_SESI']) ?> (<?= $sesi['JAM_MULAI'] ?> - <?= $sesi['JAM_SELESAI'] ?>)
                            </li>
                        </ul>

                        <div class="d-flex justify-content-between">
                            <a href="pilih_sesi.php" class="btn btn-secondary">Kembali</a>
                            <form action="proses_reservasi.php" method="post" class="d-inline">
                                <button type="submit" class="btn btn-lg">Reservasi Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const successMessage = <?php echo isset($_SESSION['success_message']) ? json_encode($_SESSION['success_message']) : 'null'; ?>;
        const errorMessage = <?php echo isset($_SESSION['error_message']) ? json_encode($_SESSION['error_message']) : 'null'; ?>;

        <?php
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        ?>

        if (successMessage) {
            alert(successMessage);
        } else if (errorMessage) {
            alert(errorMessage);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>