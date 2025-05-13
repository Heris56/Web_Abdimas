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
  <nav class="navbar fixed-top">

    <div class="container-fluid">

      <!-- navigate to home/dashboard by clicking logo/name -->
      <a class="navbar-brand brand-name" href="{{ route('landing') }}">
        <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo" width="64" height="64" class="d-inline-block" />
        SMK PGRI
      </a>

      <!-- Button login/register -->
      <div class="ms-auto" id="navbar_button">
        <a href="{{ route('landing') }}">
          Cari data Siswa
        </a>
      </div>
    </div>
  </nav>

  <!-- Wrapper untuk form masuk -->
  <div class="container1">
    <div class="container" id="form-box">
      @if (session('error'))
      <div class="alert alert-danger">
        {{session('error')}}
      </div>
      @endif
      <!-- Insert bacotan formalitas -->
      <div class="fs-2 fw-bold text-center">Masuk</div>
      <div class="fs-4 fw-medium mb-4 text-center">Masuk ke Website Sekolah</div>
      <form action="{{ route('login') }}" method="POST" onsubmit="">
        @csrf

        <!-- Isi NIP -->
        <div class="mb-2">
          <label for="inputNIP" class="form-label">NIP</label>
          <input type="text" class="form-control" id="inputNIP" name="inputNIP" required>
        </div>

        <!-- Isi kata sandi -->
        <div class="mb-2">
          <label for="inputPassword" class="form-label">Kata sandi</label>
          <input type="password" class="form-control" id="inputPassword" name="inputPassword" required>
        </div>

        <div class="mb-1">
          <input type="checkbox" id="togglePassword"> Tunjukan sandi
        </div>
        <div class="mb-4">
          <input type="checkbox" id="RememberMe" name="RememberMe"> Ingat Saya
        </div>

        <!-- Button masuk -->
        <div class="d-flex flex-column justify-content-center">
          <button type="submit" class="btn">
            Masuk
          </button>
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