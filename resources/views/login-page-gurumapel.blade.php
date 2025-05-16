<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk ke SMK Telkom</title>
    <!-- External buat background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">


    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar container-fluid fixed-top">

        <!-- navigate to home/dashboard by clicking logo/name -->
        <a class="logo" href="{{ route('landing') }}">
            <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo" width="64" height="64" class="logo-img d-inline-block" />
            SMK PGRI 35
        </a>

        <!-- Button login/register -->
        <div class="navbar-button ms-auto">
            <a href="{{ route('cari') }}">
                Cari data Siswa
            </a>
        </div>
    </nav>

    <!-- Wrapper untuk form masuk -->
    <div class="background container1">
        <div class="form-box container border">
            @if (session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
            @elseif(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
            @endif
            <!-- Insert bacotan formalitas -->
            <div class="head text-center">Masuk Sebagai Guru Mata Pelajaran</div>
            <div class="subhead text-center">Selamat datang di website SMK PGRI 35</div>
            <form action="{{ route('login.gurumapel') }}" method="POST" onsubmit="">
                @csrf

                <!-- Isi Username -->
                <div class="mb-2">
                    <label for="inputUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="inputUsername" name="inputUsername" required>
                </div>

                <!-- Isi kata sandi -->
                <div class="mb-4">
                    <label for="inputPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="inputPassword" name="inputPassword" required>
                </div>

                <!-- Button masuk -->
                <div class="d-flex flex-column justify-content-center">
                    <button type="submit" class="btn border btn-primary">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Connect Bootsrap bundle-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Connect Custom JS -->
    <script src="{{ asset('js/darryl.js') }}"></script>
</body>

</html>