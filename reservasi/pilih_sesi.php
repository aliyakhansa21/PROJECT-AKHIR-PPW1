<?php
session_start();
include '../config.php';

// Simpan unit yang dipilih
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_kursi'])) {
        $_SESSION['id_kursi'] = $_POST['id_kursi'];
        $_SESSION['id_ruangan'] = null;
    } elseif (isset($_POST['id_ruangan'])) {
        $_SESSION['id_ruangan'] = $_POST['id_ruangan'];
        $_SESSION['id_kursi'] = null;
    }
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $_SESSION['id_sesi'] = $_POST['id_sesi'];
//     header('Location: ringkasan_reservasi.php');
//     exit;
// }


$query = mysqli_query($conn, "SELECT * FROM sesi");
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
                            <h2 class="card-title text-center mb-4">Pilih Sesi</h2>
                            <form action="ringkasan_reservasi.php" method="post">
                                <div class="list-group mb-4">
                                    <?php while ($sesi = mysqli_fetch_assoc($query)) { ?>
                                        <label class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <input class="form-check-input me-2" type="radio" name="id_sesi" value="<?= $sesi['ID_SESI'] ?>" required>
                                                <span class="fw-bold"><?= htmlspecialchars($sesi['NAMA_SESI']) ?></span>
                                                <small class="d-block text-muted"><?= $sesi['JAM_MULAI'] ?> - <?= $sesi['JAM_SELESAI'] ?></small>
                                            </div>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn">Lanjut</button>
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