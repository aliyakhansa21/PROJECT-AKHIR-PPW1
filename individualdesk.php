<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Individual Desk - StudyHub</title>
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
                <h1>Individual Desk</h1>
            </div>
        </div>

        <div class="container my-4">
            <div class="row">
                <div class="col-md-6">
                    <img src="individualdesk.jpeg" class="img-fluid rounded" alt="Galeri 1">
                </div>

                <div class="col-md-6">
                    <img src="individualdesk.jpeg" class="img-fluid rounded" alt="Galeri 1">
                </div>
            </div>
        </div>

        <div class="container py-5">
            <div class="row">
                <div class="col-md-8">
                <h2 class="text">Desc</h2>
                <p>
                    Individual Desk A dirancang untuk kenyamanan belajar atau bekerja secara individu. Lokasi strategis dengan akses Wi-Fi cepat, meja ergonomis, dan suasana tenang untuk meningkatkan produktivitas Anda.
                </p>
                <h4 class="mt-4">Fasilitas</h4>
                <ul>
                    <li>Meja dan kursi ergonomis</li>
                    <li>WiFi cepat tanpa batas</li>
                    <li>AC dan ventilasi alami</li>
                    <li>Stop kontak di setiap meja</li>
                </ul>
                </div>

                <div class="col-md-4">
                <div class="bg-white shadow-sm p-4 rounded" style="background-color: #FFEEDB; color:#CB6040">
                    <h4 class="text">Booking Sekarang!</h4>
                    <p>Kapasitas 1 Orang</p>
                    <a href="#" class="btn btn-reservasi w-100 mt-2">Reservasi</a>
                </div>
                </div>
            </div>
        </div>
        


        <?php include"layout/footer.html"?>

        
    </body>
</html>