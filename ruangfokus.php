<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Ruang Fokus - StudyHub</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
        <style>
            body{
                font-family: 'Inknut Antiqua';
                background-color: #fff9e8;
                color: #CB6040;
                font-size: 1rem;
            }
            .desk-header{
                display: flex;
                align-items: end;
                padding:1rem;
                height: 200px;
                text-shadow: 1px 1px 1px rgba(0,0,0,0.7);
            }
            img {
                border-radius: 10px;
            }

            .container img {
                margin-bottom: 1rem;
            }

            .booking-btn{
                font-size: 1.5rem;
                color:#CB6040;
                font-weight: bold;
            }.booking-section{
                font-weight: bold;
                font-size: 2rem;
            }
            .btn-reservasi {
                background-color: #CB6040;
                color: white;
            }
            .btn-reservasi:hover {
                background-color: #b15438;
            }

            .facilities li{
                margin-bottom: 0.5rem;
            }
            galery-section{
                padding: 1rem;
            }
            .text{
                color: #CB6040;
            }
        </style>
    </head>

    <body>
        <?php include"layout/header.html"?>

        <div class="desk-header">
            <div class="container">
                <h1>Ruang Fokus</h1>
            </div>
        </div>

        <div class="container my-4">
            <div class="row">
                <div class="col-md-6">
                    <img src="assets/ruangfokus.jpg" class="img-fluid rounded" alt="Galeri 1">
                </div>

                <div class="col-md-6">
                    <img src="assets/ruangfokus.jpg" class="img-fluid rounded" alt="Galeri 1">
                </div>
            </div>
        </div>

        <div class="container py-5">
            <div class="row">
                <div class="col-md-8">
                <h2 class="text">Desc</h2>
                <p>
                    Ruang Fokus 4 - 5 Orang dirancang untuk mendukung kerja tim kecil atau belajar kelompok dengan kenyamanan maksimal. Terletak di area strategis, ruangan ini menghadirkan suasana tenang dan privat, cocok untuk berdiskusi, menyelesaikan proyek, atau belajar intensif bersama.
                </p>
                <h4 class="mt-4">Fasilitas</h4>
                <ul>
                    <li>Meja besar dan kursi ergonomis untuk 4 - 5 orang</li>
                    <li>WiFi cepat tanpa batas</li>
                    <li>AC dan ventilasi alami</li>
                    <li>Stop kontak tersedia di setiap posisi duduk</li>
                    <li>Pencahayaan yang optimal untuk fokus kerja</li>
                    <li>Whiteboard kecil untuk keperluan brainstorming</li>
                </ul>
                </div>

                <div class="col-md-4">
                <div class="bg-white shadow-sm p-4 rounded" style="background-color: #FFEEDB; color:#CB6040">
                    <h4 class="text">Booking Sekarang!</h4>
                    <p>Kapasitas 4 - 5 Orang</p>
                    <a href="#" class="btn btn-reservasi w-100 mt-2" onclick="window.location.href='reservasi/pilih_layanan.php'">Reservasi</a>
                </div>
                </div>
            </div>
        </div>
        
        <?php include"layout/footer.html"?>
    </body>
</html>