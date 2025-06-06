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

    <!-- Tambahkan style untuk memperbaiki spacing -->
    <style>
        .stats {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 80px;
        }

        .stat-value {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .summary-stats {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .summary-item {
            text-align: center;
            padding: 0.5rem;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .summary-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .subject-stats {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
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
            <a href="{{ route('login-siswa') }}">
                Logout
            </a>
        </div>
    </nav>

    <div class="content-wrapper container-fluid">
        <div class="Tabs">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('info.presensi') }}">Presensi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('info.nilai') }}">Nilai</a>
                </li>
            </ul>
        </div>

        <div class="Contents">

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="filterTahunAjaran" class="form-label">Filter Tahun Ajaran:</label>
                    <select class="form-select" id="filterTahunAjaran" onchange="filterByTahunAjaran()">
                        <option value="all" {{ request('tahun_ajaran') == 'all' || !request()->has('tahun_ajaran') ? 'selected' : '' }}>
                            Semua Tahun Ajaran
                        </option>
                        @if(isset($tahunAjaranList))
                            @foreach($tahunAjaranList as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_ajaran') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filterMapel" class="form-label">Filter Mata Pelajaran:</label>
                    <select class="form-select" id="filterMapel" onchange="filterSubjects()">
                        <option value="all">Semua Mata Pelajaran</option>
                        @foreach($nilaiByMapel as $mapel => $data)
                            <option value="{{ Str::slug($mapel, '-') }}">{{ $mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Table by Subject -->
            @if(isset($nilaiByMapel) && count($nilaiByMapel) > 0)
                @foreach($nilaiByMapel as $mapel => $data)
                    <div class="subject-section" data-mapel="{{ Str::slug($mapel, '-') }}">
                        <span class="head">{{ $mapel }}</span>
                        <div class="subject-stats">
                            Rata-rata: {{ $data['average'] }} |
                            Tertinggi: {{ $data['highest'] }} |
                            Terendah: {{ $data['lowest'] }} |
                            Total Tugas: {{ $data['total_assignments'] }}
                        </div>
                    </div>

                    @php
                        $kegiatan_list = $data['grades']->pluck('kegiatan')->unique()->sort()->values();
                        $grouped_data = [];

                        foreach ($data['grades'] as $item) {
                            $key = $item->id_nilai . '|' . $item->tanggal . '|' . ($item->nama_guru ?? 'Unknown');

                            $grouped_data[$key] = [
                                'tanggal' => $item->tanggal,
                                'guru' => $item->nama_guru ?? 'Unknown',
                                'nilai' => []
                            ];

                            foreach ($kegiatan_list as $kegiatan) {
                                $grouped_data[$key]['nilai'][$kegiatan] = ($item->kegiatan == $kegiatan) ? $item->nilai : '-';
                            }
                        }

                        // Urutkan berdasarkan tanggal terbaru
                        uasort($grouped_data, function ($a, $b) {
                            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
                        });
                    @endphp

                    <table class="table table-bordered mb-4">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Guru</th>
                                @foreach($kegiatan_list as $kegiatan)
                                    <th>{{ strtoupper($kegiatan) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($grouped_data as $row)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $row['tanggal'] }}</td>
                                    <td>{{ $row['guru'] }}</td>
                                    @foreach($kegiatan_list as $kegiatan)
                                        <td>{{ $row['nilai'][$kegiatan] }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @else

                @php
                    // Format lama - menyiapkan struktur data yang dikelompokkan
                    $data_nilai = [];
                    $jenis_kegiatan = [];

                    foreach ($nilai as $item) {
                        $mapel = $item->nama_mapel;
                        $kegiatan = strtoupper($item->kegiatan);

                        if (!isset($data_nilai[$mapel])) {
                            $data_nilai[$mapel] = [
                                'tanggal' => $item->tanggal,
                                'nilai' => []
                            ];
                        }

                        $data_nilai[$mapel]['nilai'][$kegiatan] = $item->nilai;

                        if (!in_array($kegiatan, $jenis_kegiatan)) {
                            $jenis_kegiatan[] = $kegiatan;
                        }
                    }

                    sort($jenis_kegiatan);
                @endphp

                <table class="table table-bordered">

                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($data_nilai as $mapel => $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['tanggal'] }}</td>
                                <td>{{ $mapel }}</td>
                                @foreach ($jenis_kegiatan as $kegiatan)
                                    <td>{{ $data['nilai'][$kegiatan] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="Profile">
            <div class="profile-card">
                <div class="card-content">
                    <!-- Avatar Circle -->
                    <div class="avatar-wrapper">
                        <div class="avatar">
                            <div class="avatar-inner">
                                <img src="{{ asset('images/profile.jpg') }}" alt="Profile Picture" class="avatar-img">
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
                                <span class="stat-value">{{ $siswa->id_kelas ?? $siswa->nama_kelas ?? 'Kelas' }}</span>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Connect Bootstrap bundle-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Connect Custom JS -->
    <script src="{{ asset('js/darryl.js') }}"></script>

    <script>
        function filterSubjects() {
            const selected = document.getElementById('filterMapel').value;
            const allSections = document.querySelectorAll('.subject-section');
            const allTables = document.querySelectorAll('table.table-bordered');

            // First hide everything
            allSections.forEach(section => section.style.display = 'none');
            allTables.forEach(table => table.style.display = 'none');

            if (selected === 'all') {
                // Show all sections and tables
                allSections.forEach(section => section.style.display = '');
                allTables.forEach(table => table.style.display = '');
            } else {
                // Show only selected subject
                const selectedSection = document.querySelector(`.subject-section[data-mapel="${selected}"]`);
                if (selectedSection) {
                    selectedSection.style.display = '';

                    // Find the table that comes right after this section
                    let nextElement = selectedSection.nextElementSibling;
                    while (nextElement) {
                        if (nextElement.tagName === 'TABLE') {
                            nextElement.style.display = '';
                            break;
                        }
                        nextElement = nextElement.nextElementSibling;
                    }
                }
            }
        }
        function filterByTahunAjaran() {
            const selectedTahun = document.getElementById('filterTahunAjaran').value;

            // Jika memilih "Semua Tahun Ajaran", reload halaman tanpa filter
            if (selectedTahun === 'all') {
                window.location.href = "{{ route('info.nilai') }}?tahun_ajaran=all";
                return;
            }

            // Redirect ke URL dengan parameter tahun ajaran
            window.location.href = "{{ route('info.nilai') }}?tahun_ajaran=" + selectedTahun;
        }
    </script>

</body>

</html>