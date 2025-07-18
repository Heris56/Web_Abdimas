<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk ke SMK PGRI 35</title>
    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    {{-- global css --}}
    @vite(['resources/css/app.css'])

    <!-- global js -->
    @vite(['resources/js/app.js'])

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">

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
    <!-- Navbar -->
    <x-navbar :showSearch="false" />
    <!-- Wrapper untuk form masuk -->
    <div class="container1">
        <div class="form-box">
            <h1 class="head">Masuk sebagai Admin Sekolah SMK PGRI 35</h1>
            <h2 class="subhead">Halo bang Admin</h2>
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('auth.staff') }}" method="post">
                @csrf
                <div class="mt-3">
                    <label class="form-label" for="NIP">NIP Staff</label>
                    <input class="form-control" type="NIP" id="NIP" name="staffNIP" placeholder="Enter your NIP">
                </div>

                <div class="mt-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" type="email" id="email" name="staffemail"
                        placeholder="Enter your email">
                    {{-- <p class="form-hint">Email Salah</p> --}}
                </div>

                <div class="mt-3 mb-3">
                    <label class="form-label" for="password">Password</label>
                    <div class="position-relative">
                        <input class="form-control" type="password" id="password" name="staffpassword"
                        placeholder="Enter your password">
                    {{-- <p class="form-hint">Password Salah</p> --}}
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;"></i>
                    </div>

                </div>
                <button class="btn btn-primary" type="submit">Masuk</button>
            </form>
        </div>
    </div>

    <!-- Connect Bootsrap bundle-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <script>
        window.addEventListener("DOMContentLoaded", function () {
        const togglePassword = document.getElementById("togglePassword");
        const password = document.getElementById("password");

        togglePassword.addEventListener("click", function (e) {
            const type =
            password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("bi-eye");
        });
    });
    </script>
</body>

</html>
