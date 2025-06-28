<head>
    <!-- Conect Icons bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- global css --}}
    @vite(['resources/css/app.css'])
</head>

<nav class="navbar container-fluid fixed-top">
    <!-- navigate to home/dashboard by clicking logo/name -->
    <a class="logo" href="{{ route('landing') }}">
        <img src="{{ asset('images/navlogo.png') }}" alt="Logo" width="64" height="64"
            class="logo-img d-inline-block" />
        <span>SMK PGRI 35</span>
    </a>

    @if ($showSearch ?? false)
        <div class="search-container ms-auto">
            <form action="/action_page.php">
                <input type="text" placeholder="Search.." name="search">
                <button type="submit"><i class="bi bi-search"></i></i></button>
            </form>
        </div>
    @endif

    <div class="navbar-button ms-auto">
        @if (session('username'))
            <div class="btn-group">
                <button type="button" class="button btn-foreground" data-bs-toggle="dropdown" data-bs-display="static"
                    aria-expanded="false">
                    <i class="bi bi-person-fill"></i>
                    {{ session('username') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end custom-dropdown-menu">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item destructive-dropdown-item">Logout</button>
                    </form>
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
                    <li><button class="dropdown-item custom-dropdown-item" type="button"
                            onclick="window.location.href='/logingurumapel'">Masuk sebagai Guru Mapel</button></li>
                    <li><button class="dropdown-item custom-dropdown-item" type="button"
                            onclick="window.location.href='/loginwalikelas'">Masuk sebagai Wali Kelas</button></li>
                    <li><button class="dropdown-item custom-dropdown-item" type="button"
                            onclick="window.location.href='/loginsiswa'">Masuk sebagai Siswa</button></li>

                    </li>
                </ul>
            </div>
        @endif
    </div>
</nav>
