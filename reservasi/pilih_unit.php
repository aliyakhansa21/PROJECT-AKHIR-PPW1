<?php
session_start();
include '../config.php';

// if (!isset($_POST['id_layanan'])) {
//     header("Location: pilih_layanan.php");
//     exit;
// }

$id_layanan = $_SESSION['id_layanan'];

$layanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM layanan WHERE ID_LAYANAN = '$id_layanan'"));
$tipe = $layanan['TIPE'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_kursi'])) {
        $_SESSION['id_kursi'] = $_POST['id_kursi'];
        $_SESSION['id_ruangan'] = null;
    } elseif (isset($_POST['id_ruangan'])) {
        $_SESSION['id_ruangan'] = $_POST['id_ruangan'];
        $_SESSION['id_kursi'] = null;
    }
    header('Location: pilih_unit.php');
    exit;
}



// $_SESSION['id_layanan'] = $_POST['id_layanan'];



?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Pilih <?= htmlspecialchars($tipe) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            .card-title {
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
            .option-card {
                border: 2px solid #CB6040;
                border-radius: 10px;
                padding: 15px;
                margin-bottom: 15px;
                background-color: #fff;
            }
            .form-check-input:checked {
                background-color: #CB6040;
                border-color: #CB6040;
            }
        </style>
    </head>

    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <!-- Progress Step -->
                    <div class="mb-4">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 66%;" aria-valuenow="2" aria-valuemin="0" aria-valuemax="3">Langkah 2 dari 3</div>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Pilih <?= $tipe == 'KURSI' ? 'Kursi' : 'Ruangan' ?></h2>
                            <form action="pilih_sesi.php" method="post">
                                <div class="row">
                                    <?php
                                    if ($tipe == 'KURSI') {
                                        $data = mysqli_query($conn, "SELECT * FROM kursi WHERE STATUS_KURSI = 'TERSEDIA'");
                                        while ($row = mysqli_fetch_assoc($data)) {
                                            echo "
                                            <div class='col-md-6'>
                                                <div class='option-card'>
                                                    <div class='form-check'>
                                                        <input class='form-check-input' type='radio' name='id_kursi' id='kursi{$row['ID_KURSI']}' value='{$row['ID_KURSI']}' required>
                                                        <label class='form-check-label fw-bold' for='kursi{$row['ID_KURSI']}'>
                                                            Kursi {$row['NOMOR_KURSI']}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>";
                                        }
                                    } else {
                                        $data = mysqli_query($conn, "SELECT * FROM ruangan");
                                        while ($row = mysqli_fetch_assoc($data)) {
                                            echo "
                                            <div class='col-md-6'>
                                                <div class='option-card'>
                                                    <div class='form-check'>
                                                        <input class='form-check-input' type='radio' name='id_ruangan' id='ruangan{$row['ID_RUANGAN']}' value='{$row['ID_RUANGAN']}' required>
                                                        <label class='form-check-label fw-bold' for='ruangan{$row['ID_RUANGAN']}'>
                                                            Ruangan {$row['NAMA_RUANGAN']}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>";
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn">Lanjut</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
