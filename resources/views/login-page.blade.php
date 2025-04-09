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
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Merriweather:wght@400;700&display=swap"
    rel="stylesheet">
</head>

<body>
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
      <div class="fs-4 fw-medium mb-4 text-center">Masuk ke akun sekolah SMK Telkom</div>
      <form action="{{ route('login') }}" method="POST" onsubmit="">
        @csrf
        <!-- Isi Email -->
        <div class="mb-2">
          <label for="inputEmail" class="form-label">Alamat email</label>
          <input type="email" class="form-control" id="inputEmail" name="inputEmail" required>
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