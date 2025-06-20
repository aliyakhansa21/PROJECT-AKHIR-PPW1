<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
session_start();
include '../config.php';

if (!isset($_SESSION['id_layanan'])) {
    header("Location: pilih_layanan.php");
    exit;
}

$id_layanan = $_SESSION['id_layanan'];
$stmt_layanan = $conn->prepare("SELECT NAMA_LAYANAN, TIPE FROM layanan WHERE ID_LAYANAN = ?");
$stmt_layanan->bind_param("i", $id_layanan);
$stmt_layanan->execute();
$result_layanan = $stmt_layanan->get_result();
$layanan = $result_layanan->fetch_assoc();
$stmt_layanan->close();

if (!$layanan) {
    $_SESSION['error_message'] = "Layanan tidak valid. Silakan pilih layanan kembali.";
    header("Location: pilih_layanan.php");
    exit;
}

$tipe_layanan = $layanan['TIPE'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_kursi'])) {
        $_SESSION['id_kursi'] = $_POST['id_kursi'];
        $_SESSION['id_ruangan'] = null;
        header('Location: pilih_sesi.php');
        exit;
    } elseif (isset($_POST['id_ruangan'])) {
        $_SESSION['id_ruangan'] = $_POST['id_ruangan'];
        $_SESSION['id_kursi'] = null;

        if ($tipe_layanan == 'RUANGAN') {
            header('Location: pilih_sesi.php');
            exit;
        }
    }
}

$query_data = null;
$item_type_label = '';
$input_name_attr = '';

if ($tipe_layanan == 'KURSI') {
    if (!isset($_SESSION['id_ruangan']) || empty($_SESSION['id_ruangan'])) {
        $item_type_label = 'Ruangan untuk ' . htmlspecialchars($layanan['NAMA_LAYANAN']);
        $input_name_attr = 'id_ruangan';
        $sql = "SELECT ID_RUANGAN, NAMA_RUANGAN, KAPASITAS, DESKRIPSI
                FROM ruangan
                WHERE ID_LAYANAN_TERKAIT = ? AND STATUS_RUANGAN = 'TERSEDIA'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_layanan);
        $stmt->execute();
        $query_data = $stmt->get_result();
        $stmt->close();
    } else {
        $id_ruangan_selected = $_SESSION['id_ruangan'];
        $item_type_label = 'Kursi di Ruangan ' . htmlspecialchars($layanan['NAMA_LAYANAN']);
        $input_name_attr = 'id_kursi';
        $sql = "SELECT ID_KURSI, NOMOR_KURSI, STATUS_KURSI
                FROM kursi
                WHERE ID_RUANGAN = ? AND STATUS_KURSI = 'TERSEDIA'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_ruangan_selected);
        $stmt->execute();
        $query_data = $stmt->get_result();
        $stmt->close();
    }
} else {
    $item_type_label = htmlspecialchars($layanan['NAMA_LAYANAN']);
    $input_name_attr = 'id_ruangan';
    $sql = "SELECT ID_RUANGAN, NAMA_RUANGAN, KAPASITAS, DESKRIPSI
            FROM ruangan
            WHERE ID_LAYANAN_TERKAIT = ? AND STATUS_RUANGAN = 'TERSEDIA'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_layanan);
    $stmt->execute();
    $query_data = $stmt->get_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih <?= htmlspecialchars($tipe_layanan) ?></title>
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
        .btn btn-secondary{
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
                <div class="mb-4">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 66%;" aria-valuenow="2" aria-valuemin="0" aria-valuemax="3">Langkah 2 dari 3</div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Pilih <?= $tipe_layanan == 'KURSI' ? 'Kursi' : 'Ruangan' ?></h2>
                        
                        <form action="pilih_unit.php" method="post">
                            <div class="row">
                                <?php
                                if ($query_data && mysqli_num_rows($query_data) > 0) {
                                    while ($row = mysqli_fetch_assoc($query_data)) {
                                        $id_item = ($input_name_attr == 'id_kursi') ? $row['ID_KURSI'] : $row['ID_RUANGAN'];
                                        $nama_item = ($input_name_attr == 'id_kursi') ? $row['NOMOR_KURSI'] : $row['NAMA_RUANGAN'];
                                        $current_session_id_item = ($input_name_attr == 'id_kursi') ? ($_SESSION['id_kursi'] ?? null) : ($_SESSION['id_ruangan'] ?? null);
                                ?>
                                        <div class='col-md-6'>
                                            <div class='option-card'>
                                                <div class='form-check'>
                                                    <input class='form-check-input' type='radio' name='<?= $input_name_attr ?>' id='<?= $input_name_attr ?><?= $id_item ?>' value='<?= $id_item ?>' <?= ($current_session_id_item == $id_item) ? 'checked' : '' ?> required>
                                                    <label class='form-check-label fw-bold' for='<?= $input_name_attr ?><?= $id_item ?>'>
                                                        <?= htmlspecialchars($nama_item) ?>
                                                    </label>
                                                    <?php if ($input_name_attr == 'id_ruangan' && isset($row['KAPASITAS'])) { ?>
                                                        <small class="d-block text-muted">Kapasitas: <?= htmlspecialchars($row['KAPASITAS']) ?> orang</small>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo "<div class='col-12 text-center'><p>Tidak ada " . htmlspecialchars(strtolower($item_type_label)) . " tersedia untuk pilihan ini.</p></div>";
                                }
                                ?>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <?php if ($tipe_layanan == 'KURSI' && isset($_SESSION['id_ruangan']) && !empty($_SESSION['id_ruangan'])) { ?>
                                    <a href="javascript:void(0)" onclick="clearRuanganSessionAndGoBack()" class="btn btn-secondary">Kembali Pilih Ruangan</a>
                                <?php } else { ?>
                                    <a href="pilih_layanan.php" class="btn btn-secondary">Kembali</a>
                                <?php } ?>
                                <button type="submit" class="btn" <?= (mysqli_num_rows($query_data) == 0) ? 'disabled' : '' ?>>Lanjut</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function clearRuanganSessionAndGoBack() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "clear_session.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href = "pilih_unit.php";
                }
            };
            xhr.send("clear_ruangan=true");
        }
    </script>
</body>
</html>