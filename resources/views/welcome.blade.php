<!-- resources/views/landing.blade.php -->
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Remind | Pengingat Digital Pintar</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { scroll-behavior: smooth; }
        .hero {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 40px;
            color: #0d6efd;
        }
        .cta {
            background: #f8f9fa;
            padding: 80px 0;
        }
        footer {
            background: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">E-Remind</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                <li class="nav-item"><a class="nav-link" href="#manfaat">Manfaat</a></li>
                <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                <li class="nav-item"><a class="btn btn-light ms-2" href="{{ route('login') }}">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero text-center" id="home">
    <div class="container">
        <h1 class="display-5 fw-bold">Aplikasi E-Remind</h1>
        <p class="lead mt-3">Solusi pengingat digital untuk jadwal kuliah,tugas, dan aktivitas harian penting Anda</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg mt-4">Mulai Sekarang</a>
    </div>
</section>

<!-- FITUR -->
<section class="py-5" id="fitur">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Fitur Unggulan</h2>
            <p class="text-muted">Kemudahan mengelola pengingat dalam satu aplikasi web</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <i class="fa-solid fa-bell feature-icon mb-3"></i>
                <h5 class="fw-bold">Notifikasi Otomatis</h5>
                <p>Pengingat real-time agar tidak melewatkan jadwal kuliah dan tugas penting.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fa-solid fa-calendar-check feature-icon mb-3"></i>
                <h5 class="fw-bold">Manajemen Jadwal</h5>
                <p>Atur jadwal harian, mingguan, dan bulanan dengan mudah.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fa-solid fa-user-shield feature-icon mb-3"></i>
                <h5 class="fw-bold">Aman & Terpercaya</h5>
                <p>Data pengguna terlindungi dengan sistem keamanan modern.</p>
            </div>
        </div>
    </div>
</section>

<!-- MANFAAT -->
<section class="bg-light py-5" id="manfaat">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold">Mengapa Memilih E-Remind?</h2>
                <ul class="mt-3">
                    <li>Meningkatkan produktivitas</li>
                    <li>Mengurangi lupa jadwal kuliah dan aktivitas penting</li>
                    <li>Menyederhanakan manajemen tugas</li>
                    <li>Cocok untuk mahasiswa, dosen, dan profesional</li>
                </ul>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://cdn-icons-png.flaticon.com/512/942/942799.png" class="img-fluid" style="max-height:280px;" alt="Reminder">
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta text-center">
    <div class="container">
        <h2 class="fw-bold">Siap Mengelola Waktu Lebih Baik?</h2>
        <p class="mt-3">Daftar sekarang dan rasakan kemudahan E-Remind</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Gratis</a>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-4" id="kontak">
    <div class="container text-center">
        <p class="mb-1">&copy; {{ date('Y') }} E-Remind. All rights reserved.</p>
        <small>Email: support@gmail.com.com | Telp: 085768733830</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>