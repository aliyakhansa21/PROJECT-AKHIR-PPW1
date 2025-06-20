<?php
session_start();

//fungsi clearRuanganSessionAndGoBack() di pilih_unit.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_ruangan'])) {
    unset($_SESSION['id_ruangan']);
    echo "success";
}
?>