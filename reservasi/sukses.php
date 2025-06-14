<?php
session_start(); // Pastikan session dimulai

// Ambil detail reservasi terakhir dari session
// Asumsikan data ini sudah disimpan di $_SESSION['last_reservation_details']
// setelah INSERT berhasil di proses_reservasi.php
$reservation_details = $_SESSION['last_reservation_details'] ?? null;

// Ambil dan hapus pesan sukses atau error dari session
$success_message = $_SESSION['success_message'] ?? 'Reservasi Anda berhasil dibuat!';
unset($_SESSION['success_message']);

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Berhasil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: "Inknut Antiqua", serif;
            font-size: 20px;
            background-color: #FFF9E8; /* Warna background dari ringkasan_reservasi.php */
            color: #CB6040; /* Warna teks utama */
        }
        .card-title, .list-group-item {
            color: #0D6172; /* Warna judul card dan list item dari ringkasan_reservasi.php */
            background-color: #FFF9E8; /* Pastikan list item juga mengikuti background body */
        }
        .btn {
            background-color: #CB6040; /* Warna tombol dari ringkasan_reservasi.php */
            color: #FFF9E8;
        }
        .btn:hover {
            background-color: #a84d34; /* Warna hover untuk tombol */
            color: #FFF9E8;
        }
        /* Pastikan container atau card juga punya background yang konsisten */
        .container, .card, .card-body {
            background-color: #FFF9E8;
        }
        .success-box {
            background-color: #e6ffe6; /* Light green for success */
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error-box {
            background-color: #ffebe6; /* Light red for error */
            border: 1px solid #ffcccb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #d9534f;
            text-align: center;
        }
        .detail-item strong {
            display: inline-block;
            width: 120px; /* Lebar untuk label agar rapi */
        }
        .mt-4 { margin-top: 1.5rem; }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if ($error_message): ?>
                            <div class="error-box">
                                <h2 class="card-title mb-3">Reservasi Gagal!</h2>
                                <p><?php echo htmlspecialchars($error_message); ?></p>
                                <a href="../index.php" class="btn mt-4">Kembali ke Beranda</a>
                            </div>
                        <?php else: ?>
                            <div class="success-box">
                                <h1 class="card-title mb-3">🎉 <?php echo htmlspecialchars($success_message); ?></h1>
                                <p class="lead">Terima Kasih Telah Melakukan Reservasi!</p>
                            </div>

                            <?php if ($reservation_details): ?>
                                <h2 class="card-title text-center mb-4">Detail Reservasi Anda</h2>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item detail-item"><strong>Layanan:</strong> <?= htmlspecialchars($reservation_details['nama_layanan'] ?? 'N/A') ?></li>
                                    <li class="list-group-item detail-item"><strong>Unit:</strong> <?= htmlspecialchars($reservation_details['nama_unit'] ?? 'N/A') ?></li>
                                    <li class="list-group-item detail-item">
                                        <strong>Tanggal:</strong> <?= htmlspecialchars(date('d F Y', strtotime($reservation_details['tanggal_reservasi'] ?? date('Y-m-d')))) ?>
                                    </li>
                                    <li class="list-group-item detail-item">
                                        <strong>Waktu:</strong> <?= htmlspecialchars(date('H:i', strtotime($reservation_details['jam_mulai'] ?? '00:00')) . ' - ' . date('H:i', strtotime($reservation_details['jam_selesai'] ?? '00:00'))) ?>
                                    </li>
                                    <li class="list-group-item detail-item">
                                        <strong>Status:</strong> <span style="font-weight: bold; color: green;"><?= htmlspecialchars($reservation_details['status_reservasi'] ?? 'Aktif') ?></span>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <p class="text-center">Detail reservasi tidak dapat ditampilkan. Silakan cek <a href="riwayat_reservasi.php">Riwayat Reservasi Anda</a>.</p>
                            <?php endif; ?>

                            <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
                                <a href="../index.php" class="btn btn-lg">Kembali ke Beranda</a>
                                <a href="riwayat_reservasi.php" class="btn btn-lg" style="background-color: #0D6172;">Lihat Riwayat Reservasi</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>