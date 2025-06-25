<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - Wali Kelas</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/info-siswa.css') }}">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar container-fluid fixed-top">
        <!-- navigate to home/dashboard by clicking logo/name -->
        <a href="{{ route('dashboard-wali-kelas') }}" class="custom-back-button">
            Kembali
        </a>
    </nav>


    <div class="ganti-password-wrapper">

        <h2>Ganti Password Wali Kelas</h2>

        @if (session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger text-center">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{route('gantiPassword.walikelas')}}" class="mx-auto" style="max-width: 500px;">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required
                    minlength="6">
            </div>

            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="new_password_confirmation"
                    name="new_password_confirmation" required minlength="6">
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="showPasswordToggle">
                <label class="form-check-label" for="showPasswordToggle">Tampilkan Password</label>
            </div>
            <button type="submit" class="btn custom-ganti-password-btn w-100">Perbarui Password</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('showPasswordToggle').addEventListener('change', function () {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('new_password_confirmation');
            const type = this.checked ? 'text' : 'password';
            newPassword.type = type;
            confirmPassword.type = type;
        });
    </script>

</body>

</html>