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
    <link rel="stylesheet" href="{{ asset('css/dashboard-staff.css') }}">

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
            <a href="{{ route('login-siswa') }}">
                Login
            </a>
        </div>
    </nav>

    <div class="content-wrapper container-fluid">
        <div class="Tabs d-flex align-items-center">
            <input type="text" class="form-control me-auto" id="cariSiswa" name="cariSiswa" placeholder="Cari Siswa">
            <div class="btns input-nilai me-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#inputNilaiModal">
                    Input Data Siswa
                </a>
            </div>

            <div class="btns cetak-nilai">
                <a href="{{ route('login-siswa') }}">
                    Cetak List Siswa
                </a>
            </div>
        </div>

        <div class="Contents">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach ($columns as $key => $label)
                        <th>{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        @foreach ($columns as $key => $label)
                        <td>{{ $item->$key ?? '-' }}</td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="Sidebar">
            <div class="head">Data</div>

            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'siswa') }}" class="nav-link" aria-current="page">
                        <i class="bi bi-house-door me-2"></i> Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'guru_mapel') }}" class="nav-link">
                        <i class="bi bi-person-lines-fill me-2"></i> Guru Mapel
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'wali_kelas') }}" class="nav-link">
                        <i class="bi bi-person-lines-fill me-2"></i> Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'mapel') }}" class="nav-link">
                        <i class="bi bi-journal-check me-2"></i> Mata Pelajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'kelas') }}" class="nav-link">
                        <i class="bi bi-bar-chart-line me-2"></i> Kelas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="modal fade" id="inputNilaiModal" tabindex="-1" aria-labelledby="inputNilaiModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="inputNilaiModalLabel">Input Nilai Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <form action="" method="">
                            @csrf

                            <!-- Nama Siswa (Dropdown or Text Input) -->
                            <div class="mb-3">
                                <label for="namaSiswa" class="form-label">Nama Siswa</label>
                                <select class="form-select" id="namaSiswa" name="namaSiswa">
                                    <option selected disabled>Pilih Siswa</option>
                                    <option value="1">Teddy Aditya</option>
                                    <option value="2">John Doe</option>
                                    <option value="3">Jane Smith</option>
                                </select>
                            </div>

                            <!-- Mata Pelajaran -->
                            <div class="mb-3">
                                <label for="namaSiswa" class="form-label">Nama Siswa</label>
                                <select class="form-select" id="namaSiswa" name="namaSiswa">
                                    <option selected disabled>Pilih Mata Pelajaran</option>
                                    <option value="1">Matematika</option>
                                    <option value="2">Bahasa Inggris</option>
                                </select>
                            </div>

                            <!-- Nilai -->
                            <div class="row">
                                <div class="col">
                                    <label for="quiz1" class="form-label">Quiz 1</label>
                                    <input type="number" class="form-control" id="quiz1" name="quiz1">
                                </div>
                                <div class="col">
                                    <label for="quiz2" class="form-label">Quiz 2</label>
                                    <input type="number" class="form-control" id="quiz2" name="quiz2">
                                </div>
                                <div class="col">
                                    <label for="quiz1" class="form-label">Quiz 3</label>
                                    <input type="number" class="form-control" id="quiz1" name="quiz1">
                                </div>
                                <div class="col">
                                    <label for="quiz2" class="form-label">Quiz 4</label>
                                    <input type="number" class="form-control" id="quiz2" name="quiz2">
                                </div>
                                <div class="col">
                                    <label for="uts" class="form-label">UTS</label>
                                    <input type="number" class="form-control" id="uts" name="uts">
                                </div>
                                <div class="col">
                                    <label for="uas" class="form-label">UAS</label>
                                    <input type="number" class="form-control" id="uas" name="uas">
                                </div>
                            </div>

                            <div class="mt-4 mb-2 text-end btns simpan-nilai">
                                <a href="#">
                                    Simpan Nilai
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
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