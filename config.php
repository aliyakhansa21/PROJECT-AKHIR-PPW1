<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi database
$host = "localhost";
$username = "root";
$password = ""; 
$database = "studyhub";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    error_log("Koneksi database gagal: " . mysqli_connect_error(), 0); 
    $_SESSION['error_message'] = "Terjadi masalah pada koneksi database. Mohon coba lagi nanti.";
}


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) { 
        header("Location: login.php"); 
        exit();
    }
}

