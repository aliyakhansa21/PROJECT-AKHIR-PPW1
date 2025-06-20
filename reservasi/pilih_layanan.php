<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
include __DIR__ . '/../config.php';

if (!$conn) {
    // Jika koneksi gagal, catat error dan berikan pesan ke user
    error_log("Koneksi database gagal di pilih_layanan.php: " . mysqli_connect_error());
    $_SESSION['error_message'] = "Terjadi masalah pada koneksi database. Silakan coba lagi nanti.";
    header('Location: ../index.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_layanan']) && !empty($_POST['id_layanan'])) {
        $_SESSION['id_layanan'] = $_POST['id_layanan'];
        header('Location: pilih_unit.php');
        exit; 
    } else {
        $_SESSION['error_message'] = "Pilihan layanan tidak valid. Silakan pilih kembali.";
        header('Location: pilih_layanan.php'); 
        exit;
    }
}

$query = mysqli_query($conn, "SELECT * FROM layanan");
if (!$query) {
    error_log("Query layanan gagal di pilih_layanan.php: " . mysqli_error($conn));
    $_SESSION['error_message'] = "Tidak dapat memuat daftar layanan. Silakan coba lagi nanti.";
    header('Location: ../index.php'); 
    exit;
}

$jumlah_layanan = mysqli_num_rows($query);

$layanan_list = [];
while ($layanan = mysqli_fetch_assoc($query)) {
    $layanan_list[] = $layanan;
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Pilih Layanan</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .layanan-card img {
                max-height: 150px;
                object-fit: cover;
            }
            *{
                background-color: #FFF9E8;
                color: #CB6040;
                padding: 0;
                margin: 0;
            }
            body{
                font-family: "Inknut Antiqua";
                font-size: 20px;
                color:#CB6040;
                background-color:#FFF9E8;
            }
            .card-body h2{
                color: #0D6172;
            }
            .btn{
                background-color: #CB6040;
                color: #FFF9E8;
            }
            .progress-bar{
                background-color: #CB6040;
                color: #FFF9E8;
            }
        </style>
    </head>

    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="mb-4">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 33%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="3">Langkah 1 dari 3</div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($_SESSION['error_message']) ?>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Pilih Layanan</h2>
                            
                            <?php if ($jumlah_layanan > 0): ?>
                                <form action="pilih_layanan.php" method="post" id="layanan-form">
                                    <div class="row">
                                        <?php foreach ($layanan_list as $layanan): ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="card h-100 layanan-card border <?php if (isset($_POST['id_layanan']) && $_POST['id_layanan'] == $layanan['ID_LAYANAN']) echo 'border-primary'; ?>">
                                                    <?php if (!empty($layanan['GAMBAR'])): ?>
                                                        <img src="<?= htmlspecialchars($layanan['GAMBAR']) ?>" class="card-img-top img-fluid" style="object-fit: contain; height: 200px;" alt="<?= htmlspecialchars($layanan['NAMA_LAYANAN']) ?>">
                                                    <?php else: ?>
                                                        <img src="https://via.placeholder.com/300x150?text=Preview+Layanan" class="card-img-top" alt="Gambar layanan">
                                                    <?php endif; ?>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="id_layanan" id="layanan<?= $layanan['ID_LAYANAN'] ?>" value="<?= $layanan['ID_LAYANAN'] ?>" required>
                                                            <label class="form-check-label fw-bold" for="layanan<?= $layanan['ID_LAYANAN'] ?>">
                                                                <?= htmlspecialchars($layanan['NAMA_LAYANAN']) ?>
                                                            </label>
                                                        </div>
                                                        <p class="text-muted mb-1"><small><?= htmlspecialchars($layanan['TIPE']) ?></small></p>
                                                        <?php if (!empty($layanan['DESKRIPSI'])): ?>
                                                            <p class="small"><?= htmlspecialchars($layanan['DESKRIPSI']) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <input type="submit" value="Lanjut" class="btn"
                                        style="background-color: #CB6040; color: #FFF9E8; border: none; border-radius: 20px; padding: 10px 20px; font-size: 20px; width: 100%;">
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning text-center" role="alert">
                                    <h5>Tidak Ada Layanan Tersedia</h5>
                                    <p>Saat ini tidak ada layanan yang tersedia. Silakan hubungi administrator.</p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('layanan-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const selectedLayanan = document.querySelector('input[name="id_layanan"]:checked');
                        if (!selectedLayanan) {
                            e.preventDefault();
                            alert('Silakan pilih salah satu layanan terlebih dahulu!');
                        }
                    });
                }
            });
        </script>
    </body>
</html>