<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_ruangan'])) {
    unset($_SESSION['id_ruangan']);
    // Opsional: bersihkan juga id_kursi jika ada agar state selalu bersih
    unset($_SESSION['id_kursi']);
    echo "Session id_ruangan cleared.";
    exit;
}
?>