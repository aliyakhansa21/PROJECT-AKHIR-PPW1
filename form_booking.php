<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}


include_once("config.php");


// Proses form saat disubmit
$pesan_error = "";
$pesan_sukses = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user  = $_SESSION['id_user'];
    $id_kursi = $_POST['id_kursi'];
    $id_sesi  = $_POST['id_sesi'];
    $WAKTU_BUAT  = $_POST['WAKTU_BUAT'];

    // Cek apakah kursi sudah dibooking di tanggal dan sesi yang sama
    $cek = mysqli_query($conn, "SELECT * FROM RESERVASI 
        WHERE ID_KURSI = '$id_kursi' 
        AND ID_SESI = '$id_sesi' 
        AND WAKTU_BUAT = '$WAKTU_BUAT'");

    if (mysqli_num_rows($cek) > 0) {
        $pesan_error = "Booking gagal! Kursi sudah dibooking di tanggal dan sesi tersebut.";
    } else {
        // Simpan ke database
        $query = mysqli_query($conn, "INSERT INTO reservasi (id_user, id_kursi, id_sesi, WAKTU_BUAT)
                    VALUES ('$id_user', '$id_kursi', '$id_sesi', '$WAKTU_BUAT')");
        if ($query) {
            $pesan_sukses = "Booking berhasil!";
        } else {
            $pesan_error = "Terjadi kesalahan saat menyimpan data.";
        }
    }
}

// Ambil data kursi dan sesi untuk ditampilkan di form
$data_kursi = mysqli_query($conn, "SELECT * FROM kursi");
$data_sesi = mysqli_query($conn, "SELECT * FROM sesi");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Reservasi Kursi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #CB6040;
            font-family: Arial, sans-serif;
        }
        .reservasi-form {
            background-color: #FFF9E8;
            border-radius: 10px;
            padding: 2rem;
            max-width: 600px;
            margin: 3rem auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h3 {
            color: #CB6040;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="reservasi-form">
            <h3 class="text-center mb-4">Form Reservasi Kursi</h3>

            <!-- Tampilkan pesan sukses/gagal -->
            <?php if (!empty($pesan_error)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($pesan_error) ?></div>
            <?php elseif (!empty($pesan_sukses)) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($pesan_sukses) ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="kursi" class="form-label">Pilih Kursi</label>
                    <select class="form-select" name="id_kursi" id="kursi" required>
                        <option value="" selected disabled>-- Pilih Kursi --</option>
                        <?php while ($k = mysqli_fetch_assoc($data_kursi)) : ?>
                            <option value="<?= $k['ID_KURSI'] ?>"><?= htmlspecialchars($k['NOMOR_KURSI']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="sesi" class="form-label">Pilih Sesi</label>
                    <select class="form-select" name="id_sesi" id="sesi" required>
                        <option value="" selected disabled>-- Pilih Sesi --</option>
                        <?php while ($s = mysqli_fetch_assoc($data_sesi)) : ?>
                            <option value="<?= $s['ID_SESI'] ?>"><?= htmlspecialchars($s['NAMA_SESI']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Pilih Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Booking Sekarang</button>
            </form>
        </div>
    </div>
</body>
</html>
