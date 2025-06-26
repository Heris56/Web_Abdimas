<head>
    <!-- Conect Icons bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- global css --}}
    @vite(['resources/css/app.css'])
</head>

<nav class="navbar container-fluid fixed-top">
    <!-- navigate to home/dashboard by clicking logo/name -->
    <a class="logo" href="{{ route('landing') }}">
        <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo" width="64" height="64"
            class="logo-img d-inline-block" />
        SMK PGRI 35
    </a>

    <div class="search-container ms-auto">
        <form action="/action_page.php">
            <input type="text" placeholder="Search.." name="search">
            <button type="submit"><i class="bi bi-search"></i></i></button>
        </form>
    </div>

    <div class="navbar-button ms-auto">
        @if (session('username'))
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-display="static" aria-expanded="false">
                    <i class="bi bi-person-fill"></i>
                    {{ session('username') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                    <li><button class="dropdown-item" type="button">Action</button></li>
                    <li><button class="dropdown-item" type="button">Another action</button></li>
                    <li><button class="dropdown-item" type="button">Something else here</button></li>
                </ul>
            </div>
        @else
            <div class="btn-group">
                <button type="button" class="button btn-foreground" data-bs-toggle="dropdown" data-bs-display="static"
                    aria-expanded="false">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="dropdown-text">Masuk ke akun SMK PGRI 35</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end custom-dropdown-menu">
                    <li><button class="dropdown-item custom-dropdown-item" type="button">Masuk sebagai Guru
                            Mapel</button></li>
                    <li><button class="dropdown-item custom-dropdown-item" type="button">Masuk sebagai Wali
                            Kelas</button></li>
                    <li><button class="dropdown-item custom-dropdown-item" type="button">Masuk sebagai Siswa</button>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</nav>
