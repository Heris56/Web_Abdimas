<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=1, user-scalable=yes" />
    <title>Masuk ke SMK Telkom</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <link rel="stylesheet" href="{{ asset('css/info-siswa.css') }}">

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
    <x-navbar />

    <div class="content-wrapper container-fluid">
        <div class="Tabs">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    {{-- Adjust links based on whether it's a guest or logged-in user --}}
                    <a class="nav-link"
                        href="{{ $isGuest ? route('guest.info.siswa', ['inputNISN' => $siswa->nisn]) : route('info.presensi') }}">Presensi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page"
                        href="{{ $isGuest ? route('guest.info.siswa', ['inputNISN' => $siswa->nisn, 'tab' => 'nilai']) : route('info.nilai') }}">Nilai</a>
                </li>
            </ul>
        </div>

        <div class="Contents">

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="filterTahunAjaran" class="form-label">Filter Tahun Ajaran:</label>
                    <select class="form-select" id="filterTahunAjaran" onchange="filterByTahunAjaran()">
                        <option value="all"
                            {{ request('tahun_ajaran') == 'all' || !request()->has('tahun_ajaran') ? 'selected' : '' }}>
                            Semua Tahun Ajaran
                        </option>
                        @if (isset($semesterList))
                            @foreach ($semesterList as $tahun)
                                <option value="{{ $tahun }}"
                                    {{ request('tahun_ajaran') == $tahun ? 'selected' : '' }}>
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
                        @foreach ($nilaiByMapel as $mapel => $data)
                            <option value="{{ Str::slug($mapel, '-') }}">{{ $mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if (request('tahun_ajaran') && request('tahun_ajaran') !== 'all')
                @php
                    [$tahunAjaran, $semester] = explode(' - ', request('tahun_ajaran'));
                    $semesterText = $semester === '1' ? 'Ganjil' : 'Genap';
                @endphp
            @endif


            @if (isset($nilaiByMapel) && count($nilaiByMapel) > 0)
                @foreach ($nilaiByMapel as $mapel => $data)
                    @php
                        // Kelompokkan grades berdasarkan Tahun Ajaran + Semester
                        $groupedByTahunSemester = $data['grades']->groupBy(function ($item) {
                            $semesterAngka = $item->semester === 'Ganjil' ? '1' : '2';
                            return $item->tahun_pelajaran . ' - ' . $semesterAngka;
                        });
                    @endphp

                    @foreach ($groupedByTahunSemester as $tahunSemester => $gradesGroup)
                        <div class="subject-section" data-mapel="{{ Str::slug($mapel, '-') }}">
                            <span class="head">{{ $mapel }} ({{ $tahunSemester }})</span>
                            @php
                                $guruPengampu = $gradesGroup->first()->nama_guru ?? 'Tidak diketahui';
                            @endphp
                            <div class="subject-stats">
                                Guru Pengampu: {{ $guruPengampu }}
                            </div>
                        </div>

                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Nilai</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($gradesGroup as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ strtoupper($item->kegiatan) }}</td>
                                        <td>{{ $item->nilai }}</td>
                                        <td>{{ $item->tanggal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
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
                                'nilai' => [],
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
                    <div class="avatar-wrapper">
                        <div class="avatar">
                            <div class="avatar-inner">
                                <img src="{{ asset('images/userprofile.png') }}" alt="Profile Picture"
                                    class="avatar-img">
                            </div>
                            <div class="avatar-border"></div>
                        </div>
                    </div>

                    <div class="profile-info">
                        <h2 class="name">{{ $siswa->nama_siswa ?? 'Nama Siswa' }}</h2>
                        <p class="title">{{ $siswa->nisn ?? 'NISN' }}</p>

                        <div class="stats">
                            <div class="stat">
                                <span
                                    class="stat-value">{{ $siswa->id_kelas ?? ($siswa->nama_kelas ?? 'Kelas') }}</span>
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
                    @if (!$isGuest)
                        {{-- Only show Ganti Password for logged-in users --}}
                        <div class="text-center mt-3">
                            <a href="{{ route('siswa.formGantiPassword') }}"
                                class="btn custom-ganti-password-btn w-100">
                                Ganti Password
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

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
                const selectedSections = document.querySelectorAll(`.subject-section[data-mapel="${selected}"]`);
                selectedSections.forEach(section => {
                    section.style.display = '';

                    // Find the table that comes right after this section
                    let nextElement = section.nextElementSibling;
                    while (nextElement) {
                        if (nextElement.tagName === 'TABLE') {
                            nextElement.style.display = '';
                            break;
                        }
                        nextElement = nextElement.nextElementSibling;
                    }
                });
            }
        }

        function filterByTahunAjaran() {
            const selectedTahun = document.getElementById('filterTahunAjaran').value;
            const allSections = document.querySelectorAll('.subject-section');
            const allTables = document.querySelectorAll('table.table-bordered');

            if (selectedTahun === 'all') {
                // Show all sections and tables without filtering tahun ajaran
                allSections.forEach(section => section.style.display = '');
                allTables.forEach(table => table.style.display = '');
            } else {
                // Filter berdasarkan tahun ajaran (yang ada di dalam teks heading)
                allSections.forEach(section => {
                    // Contoh text: "Matematika (2023/2024 - 1)"
                    const headingText = section.querySelector('.head').textContent || '';
                    if (headingText.includes(selectedTahun)) {
                        section.style.display = '';

                        // Tampilkan table setelahnya juga
                        let nextElement = section.nextElementSibling;
                        while (nextElement) {
                            if (nextElement.tagName === 'TABLE') {
                                nextElement.style.display = '';
                                break;
                            }
                            nextElement = nextElement.nextElementSibling;
                        }
                    } else {
                        section.style.display = 'none';
                        // Sembunyikan juga table setelahnya
                        let nextElement = section.nextElementSibling;
                        while (nextElement) {
                            if (nextElement.tagName === 'TABLE') {
                                nextElement.style.display = 'none';
                                break;
                            }
                            nextElement = nextElement.nextElementSibling;
                        }
                    }
                });
            }
        }

        // Untuk gabungkan filter keduanya tanpa reload
        function applyFilters() {
            const selectedTahun = document.getElementById('filterTahunAjaran').value;
            const selectedMapel = document.getElementById('filterMapel').value;
            const allSections = document.querySelectorAll('.subject-section');
            const allTables = document.querySelectorAll('table.table-bordered');

            allSections.forEach(section => {
                const headingText = section.querySelector('.head').textContent || '';
                const mapelSlug = section.getAttribute('data-mapel');

                const matchTahun = (selectedTahun === 'all' || headingText.includes(selectedTahun));
                const matchMapel = (selectedMapel === 'all' || mapelSlug === selectedMapel);

                if (matchTahun && matchMapel) {
                    section.style.display = '';
                    // Show the corresponding table
                    let nextElement = section.nextElementSibling;
                    while (nextElement) {
                        if (nextElement.tagName === 'TABLE') {
                            nextElement.style.display = '';
                            break;
                        }
                        nextElement = nextElement.nextElementSibling;
                    }
                } else {
                    section.style.display = 'none';
                    // Hide the corresponding table
                    let nextElement = section.nextElementSibling;
                    while (nextElement) {
                        if (nextElement.tagName === 'TABLE') {
                            nextElement.style.display = 'none';
                            break;
                        }
                        nextElement = nextElement.nextElementSibling;
                    }
                }
            });
        }

        // Override event handler supaya pakai gabungan filter tanpa reload
        document.getElementById('filterTahunAjaran').onchange = applyFilters;
        document.getElementById('filterMapel').onchange = applyFilters;

        // Jalankan filter sekali saat halaman load
        document.addEventListener('DOMContentLoaded', () => {
            applyFilters();
        });
    </script>


</body>

</html>
