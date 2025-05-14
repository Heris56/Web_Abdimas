<div>
    <!-- Navbar -->
    <nav class="navbar fixed-top border">

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
</div>