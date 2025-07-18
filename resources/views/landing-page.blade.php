<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SMK PGRI 35</title>

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    {{-- global css --}}
    @vite(['resources/css/app.css'])

    <!-- global js -->
    {{-- @vite(['resources/js/app.js']) --}}

    <!-- Conect Icons bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing-page.css') }}">

    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <style>
        body {
            padding: 0;
        }
    </style>
    <!-- Wrapper -->
    <div class="container1">
        <div class="background"></div>

        <div class="main-content d-flex flex-column">
            <div class="row-md-auto">
                <span class="hero">SISTEM INFORMASI AKADEMIK</span>
            </div>

            <div class="row-md-auto">
                <span class="hero">SMK PGRI 35</span>
            </div>

            <div class="mt-4 row-md-auto">
                <div class="col container">
                    {{-- <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo Sekolah PGRI" id="logo-pgri"> --}}
                    <a href="{{ route('cari') }}" class="btn">Cari Info Siswa</a>
                    <a href="{{ route('login-siswa') }}" class="btn">Masuk ke akun Siswa</a>
                    <a href="{{ route('login-walikelas') }}" class="btn">Masuk ke akun Wali
                        Kelas</a>
                    <a href="{{ route('login-gurumapel') }}" class="btn">Masuk ke akun Guru</a>
                    <a href="{{ route('login-staff') }}" class="btn">Masuk ke akun Staff</a>
                </div>
            </div>

            <div class="row align-items-center justify-content-start mt-4 mb-2">
                <div><span>Menemukan kesalahan? Hubungi kami!</span></div>
            </div>

            <div class="row align-items-center justify-content-start mb-2">
                <span class="col-auto"><i class="bi bi-whatsapp"></i></span>
                <span class="col-auto">:</span>
                <span class="col text-start"><a href="https://wa.me/+6282119164019">&nbsp;0821-1916-4019</a></span>
            </div>

            <div class="row align-items-center justify-content-start mb-2">
                <span class="col-auto"><i class="bi bi-tiktok"></i></span>
                <span class="col-auto">:</span>
                <span class="col text-start"><a href="https://www.tiktok.com/@smkpgri35soljer">@smkpgri35soljer</a></span>
            </div>

            <div class="row align-items-center justify-content-start mb-2">
                <span class="col-auto"><i class="bi bi-instagram"></i></span>
                <span class="col-auto">:</span>
                <span class="col text-start"><a href="https://www.instagram.com/smkpgri35soljer/">@smkpgri35soljer</a></span>
            </div>

            <div class="row align-items-center justify-content-start mt-4 mb-2">
                <div><span>Alamat: SMK PGRI 35 Solokan Jeruk</span></div>
            </div>
        </div>
    </div>

    <!-- Connect Bootsrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek session flash message dari Laravel dan tampilkan dengan alert()
            @if (session('success'))
                alert("Berhasil: {{ session('success') }}");
            @endif

            @if (session('error'))
                alert("Error: {{ session('error') }}");
            @endif

            @if (session('warning'))
                alert("Peringatan: {{ session('warning') }}");
            @endif
        });
    </script>
</body>

</html>
