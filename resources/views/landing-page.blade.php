<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SMK </title>
  <!-- External buat background -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

  <!-- Conect CSS bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

  <!-- Connect CSS -->
  <link rel="stylesheet" href="{{ asset('css/landing-page.css') }}">

  <!-- Import Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
  <!-- Wrapper -->
  <div class="container1">
    <div class="background"></div>

    <div class="main-content">
      <div class="container">
        <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo Sekolah PGRI" id="logo-pgri">
        <a href="{{ route('cari') }}"><button>Cari Info Siswa</button></a>
        <a href="{{ route('login-siswa') }}"><button>Masuk ke akun Siswa</button></a>
        <a href="{{ route('login-walikelas') }}"><button>Masuk ke akun Wali Kelas</button></a>
        <a href="{{ route('login-gurumapel') }}"><button>Masuk ke akun Guru</button></a>
      </div>
    </div>
  </div>

  <!-- Connect Bootsrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <!-- Connect Custom JS -->
  <script src="{{ asset('js/darryl.js') }}"></script>

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