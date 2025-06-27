<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=1, user-scalable=yes" />
    <title>Masuk ke SMK Telkom</title>
    <!-- External buat background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/info-siswa.css') }}">

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
    <nav class="navbar container-fluid fixed-top">

        <!-- navigate to home/dashboard by clicking logo/name -->
        <a class="logo" href="{{ route('landing') }}">
            <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo" width="64" height="64"
                class="logo-img d-inline-block" />
            SMK PGRI 35
        </a>

        <!-- Button login/register -->
        <div class="navbar-button ms-auto">
             @if($isGuest)
                <a href="{{ route('login-siswa') }}">
                    Masuk
                </a>
            @else
            <form action="{{ route('logout') }}", method="post">
                @csrf
                <button class="btn btn-danger" type="submit">Keluar</button>
            </form>
            @endif
        </div>
    </nav>

    <div class="content-wrapper container-fluid">
        <div class="Tabs">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ $isGuest ? route('guest.info.siswa', ['inputNISN' => $siswa->nisn]) : route('info.presensi') }}">Presensi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ $isGuest ? route('guest.info.siswa', ['inputNISN' => $siswa->nisn, 'tab' => 'nilai']) : route('info.nilai') }}">Nilai</a>
                </li>
            </ul>
        </div>
        <div class="Contents">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="filterTahunAjaran" class="form-label">Filter Tahun Ajaran:</label>
                    <select class="form-select" id="filterTahunAjaran" onchange="filterByTahunAjaran()">
                        <option value="all">Semua Tahun Ajaran</option>
                        @if(isset($tahunAjaranList))
                            @foreach($tahunAjaranList as $tahunSemester)
                                <option value="{{ $tahunSemester }}" {{ $tahunAjaranFilter == $tahunSemester ? 'selected' : '' }}>
                                    {{ $tahunSemester }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                </div>
            </div>

            @if(isset($presensi) && count($presensi) > 0)
                <!-- Table Presensi -->
                <div class="subject-section">
                    <span class="head">Riwayat Presensi</span>
                    @if($tahunAjaranFilter !== 'all')
                        <span class="badge bg-primary ms-2">{{ $tahunAjaranFilter }}</span>
                    @endif
                </div>

                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Catatan</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($presensi as $index => $presensiItem)
                            <tr>
                                <td>{{ $index + 1 }}</th>
                                <td>{{ $presensiItem->tanggal }}</td>
                                <td>{{ $presensiItem->keterangan_absen }}</td>
                                <td>
                                    @if($presensiItem->keterangan_absen == 'Hadir')
                                        -
                                    @elseif($presensiItem->keterangan_absen == 'Izin')
                                        Surat izin sakit
                                    @elseif($presensiItem->keterangan_absen == 'Alpha')
                                        Tidak ada keterangan
                                    @else
                                        Terlambat
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info">
                    Belum ada data presensi untuk ditampilkan.
                    @if($tahunAjaranFilter !== 'all')
                        ({{ $tahunAjaranFilter }})
                    @endif
                </div>
            @endif
        </div>
        <div class="Profile">
            <div class="profile-card">
                <div class="card-content">
                    <!-- Avatar Circle -->
                    <div class="avatar-wrapper">
                        <div class="avatar">
                            <div class="avatar-inner">
                                <img src="{{ asset('images/userprofile.png') }}" alt="Profile Picture" class="avatar-img">
                            </div>
                            <div class="avatar-border"></div>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="profile-info">
                        <h2 class="name">{{ $siswa->nama_siswa ?? 'Nama Siswa' }}</h2>
                        <p class="title">{{ $siswa->nisn ?? 'NISN' }}</p>

                        <div class="stats">
                            <div class="stat">
                                <span class="stat-value">{{ $siswa->id_kelas ?? 'Kelas' }}</span>
                                <span class="stat-label">Kelas</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">{{ $siswa->tahun_ajaran ?? 'Tahun' }}</span>
                                <span class="stat-label">Angkatan</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">{{ $siswa->jurusan ?? 'Jurusan' }}</span>
                                <span class="stat-label">Jurusan</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('siswa.formGantiPassword') }}" class="btn custom-ganti-password-btn w-100">
                            Ganti Password
                        </a>
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

        <script>
            function filterByTahunAjaran() {
                const selectedTahun = document.getElementById('filterTahunAjaran').value;

                if (selectedTahun === 'all') {
                    window.location.href = "{{ route('info.presensi') }}";
                    return;
                }

                window.location.href = "{{ route('info.presensi') }}?tahun_ajaran=" + selectedTahun;
            }
        </script>
</body>

</html>