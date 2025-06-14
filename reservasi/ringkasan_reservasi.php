<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['id_sesi'] = $_POST['id_sesi'];
}

$id_layanan = $_SESSION['id_layanan'] ?? null;
$id_sesi = $_SESSION['id_sesi'] ?? null;
$id_kursi = $_SESSION['id_kursi'] ?? null;
$id_ruangan = $_SESSION['id_ruangan'] ?? null;

if (!$id_layanan || !$id_sesi || (!$id_kursi && !$id_ruangan)) {
    echo "Data reservasi tidak lengkap. <a href='pilih_layanan.php'>Kembali</a>";
    exit;
}

$layanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM layanan WHERE id_layanan = '$id_layanan'"));
$sesi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sesi WHERE id_sesi = '$id_sesi'"));
$unit = null;

// if (!isset($_POST['id_sesi'])){
//     header("Location: pilih_sesi.php");
//     exit;
// }

// $_SESSION['id_sesi'] = $_POST['id_sesi'];
// $id_user = 1;

// $id_layanan = $_SESSION['id_layanan'];
// $id_kursi = $_SESSION['id_kursi'];
// $id_ruangan = $_SESSION['id_ruangan'];
// $id_sesi = $_SESSION['id_sesi'];



// if ($id_kursi){
//     $unit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kursi WHERE id_kursi = '$id_kursi'"));
//     $unit_text = "Kursi" . $unit['nomor_kursi'];
// } else{
//     $unit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM ruangan WHERE id_ruangan = '$id_ruangan'"));
//     $unit_text = $unit['NAMA_RUANGAN'];
// }
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
                            <li class="list-group-item"><strong>Layanan:</strong> <?= htmlspecialchars($layanan['NAMA_LAYANAN']) ?></li>
                            <li class="list-group-item"><strong><?= $unit_text ?></strong></li>
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