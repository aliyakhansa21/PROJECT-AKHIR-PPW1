<?php
session_start();
// echo "Isi session: ";
// print_r($_SESSION);

include_once("config.php");
requireLogin(); // pastikan user sudah login

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

//konfigurasi search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_query = '';
if (!empty($search)){
    $search_query = "WHERE nim LIKE '%$search%' OR nama LIKE '%$search%' OR jurusan LIKE '%$search%' OR email LIKE '%$search%'";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gama StudyHub</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link href="https://cdn.materialdesignicons.com/7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
        <style>
        body {
            font-family: 'Inknut Antiqua', serif;
            background-color: #FFF9E8;
            color: #CB6040;
            font-size: 16px;
        }
        .header{
            background-color: #FFF9E8;
        }
        .company-logo{
            width: 48px;
            height: 48px;
        }
        .hero {
            padding: 100px 30px;
            text-align: center;
        }
        .hero h1 {
            color: #0D6172;
            font-size: 4rem;
        }
        .hero p {
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
        }
        .hero-buttons .btn {
            border-radius: 20px;
            margin: 0 10px;
            background-color: #CB6040;
            color: white;
        }
        .facility-icon {
            width: 48px;
            height: 48px;
        }
        .facility-item i, .facility-item img {
            font-size: 2rem;
            color: #CB6040;
        }
        .footer{
            background-color: #CB6040;
            color: white;
            padding: 40px 0;
        }
        .footer__column h4 {
            margin-bottom: 15px;
        }
        </style>
    </head>

    <body>
        <?php include"layout/header.html"?>

        <section class="hero">
            <h1>STUDYHUB</h1>
            <p>Gama StudyHub adalah adalah ruang belajar bersama khusus mahasiswa UGM yang nyaman, modern, dan mendukung kolaborasi. Dilengkapi dengan fasilitas yang lengkap dan suasana kondusif, Gama StudyHub hadir sebagai solusi untuk kamu yang butuh tempat produktif di lingkungan kampus.</p>
            <div class="hero-buttons mt-4">
                <button class="btn" onclick="window.location.href='reservasi/pilih_layanan.php'">Reservasi</button>
            </div>
        </section>

        <section class="py-5 text-center">
            <h2 class="mb-4">OUR FACILITIES</h2>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-3 mb-4">
                        <div>
                            <i class="mdi mdi-clock-outline"></i>
                            <p>Jam Operasional<br>24 Jam</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div>
                            <i class="mdi mdi-wifi"></i>
                            <p>High Speed<br>WiFi</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div>
                            <img src="air-condisioner.png" alt="AC" class="facility-icon">
                            <p>Jam Operasional<br>24 Jam</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div>
                            <i class="mdi mdi-mosque"></i>
                            <p>Musholla</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 text-center">
            <div class="container">
                <h2 class="mb-4">OUR SPACE</h2>
                <p class="mb-5">Fasilitas meja kursi yang nyaman dan didukung dengan high speed wifi untuk meningkatkan produktivitas</p>
                <div class="row row-cols-1 row-cols-md-2 rol-cols-lg-4 g-4">
                    <div class="col">
                        <div class="card h-100">
                            <img src="assets/individualdesk.jpeg" class="card-img-top" alt="Individual Desk">
                            <div class="card-body">
                                <h5 class="card-title">Individual Desk</h5>
                                <p class="card-text">Cocok untuk kamu yang introvert akut</p>
                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <small>1 Orang</small>
                                <!-- <button class="btn btn-outline-primary btn-sm">→</button> -->
                                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='layanan/individualdesk.php'">→</button>
                                <!-- <button class="btn" onclick="window.location.href='reservasi/pilih_layanan.php'">Reservasi</button> -->

                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card h-100">
                            <img src="assets/ruangfokus.jpg" class="card-img-top" alt="Individual Desk">
                            <div class="card-body">
                                <h5 class="card-title">Ruang Fokus</h5>
                                <p class="card-text">Cocok untuk kamu yang introvert akut</p>
                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <small>4 -5 Orang</small>
                                <!-- <button class="btn btn-outline-primary btn-sm">→</button> -->
                                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='layanan/ruangfokus.php'">→</button>

                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card h-100">
                            <img src="assets/ruangkreatif.jpg" class="card-img-top" alt="Individual Desk">
                            <div class="card-body">
                                <h5 class="card-title">Ruang Kreatif</h5>
                                <p class="card-text">Cocok untuk kamu yang introvert akut</p>
                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <small>6 -8 Orang</small>
                                <!-- <button class="btn btn-outline-primary btn-sm">→</button> -->
                                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='layanan/ruangkreatif.php'">→</button>

                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card h-100">
                            <img src="assets/ruangmeeting.jpg" class="card-img-top" alt="Individual Desk">
                            <div class="card-body">
                                <h5 class="card-title">Ruang Meeting</h5>
                                <p class="card-text">Cocok untuk kamu yang introvert akut</p>
                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <small>9 - 13 Orang</small>
                                <!-- <button class="btn btn-outline-primary btn-sm">→</button> -->
                                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='layanan/ruangmeeting.php'">→</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Frequently Asked Questions</h2>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a1">
                                Q: Bagaimana cara melakukan reservasi ruang?
                            </button>
                        </h2>
                        <div id="a1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A: Anda dapat memilih ruang yang tersedia melalui halaman Cek Ketersediaan, lalu klik tombol "Reservasi".
                            </div>
                        </div>
                    </div>

                    <div class="accordion-items">
                        <h2 class="accordion-header" id="q2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
                                Q: Apakah bisa memesan lebih dari satu slot waktu?
                            </button>
                        </h2>
                        <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A: Ya, Anda bisa memilih lebih dari satu slot waktu selama ruang tersebut masih tersedia.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-items">
                        <h2 class="accordion-header" id="q3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
                                Q: Apakah saya bisa membatalkan atau mengubah jadwal reservasi?
                            </button>
                        </h2>
                        <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A: Tentu, Anda dapat membatalkan atau mengubah jadwal hingga maksimal 1 hari sebelum tanggal reservasi.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-items">
                        <h2 class="accordion-header" id="q4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">
                                Q: Apakah ruang bisa digunakan untuk acara atau rapat besar?
                            </button>
                        </h2>
                        <div id="a4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A: Beberapa ruang cocok untuk workshop atau diskusi kelompok. Hubungi kami untuk kebutuhan khusus.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-items">
                        <h2 class="accordion-header" id="q5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a5">
                                Q: Apakah tersedia paket langganan bulanan?
                            </button>
                        </h2>
                        <div id="a5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A: Kami menyediakan paket mingguan dan bulanan dengan harga khusus. Hubungi kami untuk info lanjut.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container d-flex flex-column flex-md-row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="assets/ruangmeeting.jpg" class="img-fluid rounded" alt="Study space">
                </div>
                <div class="col-md-6">
                    <h4>Come On!</h4>
                    <h2 class="fw-bold">Tingkatkan Produktivitasmu!</h2>
                    <p class="mb-3">Butuh tempat buat rapat, organisasi, atau brainstorming bareng teman? Kami siap jadi ruang produktif kamu di luar kampus.</p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" checked disabled>
                        <label class="form-check-label">Senin - Minggu</label>
                    </div>
                    <a href="#" class="btn btn-light">RESERVASI</a>
                </div>
            </div>
        </section>

        <?php include"layout/footer.html"?>
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>