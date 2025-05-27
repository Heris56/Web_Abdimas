<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Olah Data SMK PGRI 35</title>
    <!-- External buat background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-staff.css') }}">

    {{-- untuk modal notification --}}
    <script src="{{ asset('js/app.js') }}"></script>

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
    {{-- toast notification --}}
    <x-toast-notification />

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
                Login
            </a>
        </div>
    </nav>

    <div class="content-wrapper container-fluid">
        <div class="Tabs d-flex align-items-center">
            <input type="text" class="form-control me-auto" id="cariSiswa" name="cariSiswa" placeholder="Cari Siswa">
            <div class="btns input-nilai me-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#inputNilaiModal">
                    Input Data {{ $buttonText }}
                </a>
            </div>

            <div class="btns cetak-nilai">
                <button id="button-cetak" class="btn btn-success"
                    onclick="showToast('Mencetak {{ $buttonText }}...', 'text-bg-primary'); exportExcel('{{ $buttonText }}', '{{ str_replace(' ', '_', $buttonText) }}_{{ date('YmdHis') }}')">
                    Export {{ $buttonText }} to Excel
                </button>
            </div>
        </div>

        <div class="Contents">
            <table class="table table-bordered" id="table-data">
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

        <div class="modal fade" id="inputNilaiModal" tabindex="-1" aria-labelledby="inputNilaiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inputNilaiModalLabel">Input Data {{ $buttonText }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('data.input', ['type' => $type]) }}" method="POST">
                            @csrf
                            @foreach ($columns as $key => $label)
                                <div class="mb-3">
                                    <label for="{{ $key }}" class="form-label">{{ $label }}</label>

                                    <!-- set dropdown untuk pilih mapel dan kelas -->
                                    @if (in_array($key, ['id_kelas', 'id_mapel']))
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}" required>
                                            <option selected disabled>Pilih {{ $label }}</option>
                                            @if ($key == 'id_kelas')
                                                @forelse ($dropdowns['kelas'] as $item)
                                                    <option value="{{ $item->id_kelas }}">{{ $item->id_kelas }}
                                                        ({{ $item->jurusan }})
                                                    </option>
                                                @empty
                                                    <option disabled>Tidak ada kelas tersedia</option>
                                                @endforelse
                                            @elseif ($key == 'id_mapel')
                                                @forelse ($dropdowns['mapel'] as $item)
                                                    <option value="{{ $item->id_mapel }}">{{ $item->nama_mapel }}
                                                    </option>
                                                @empty
                                                    <option disabled>Tidak ada mata pelajaran tersedia</option>
                                                @endforelse
                                            @endif
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- set dropdown untuk pilih status dan status tahun ajaran -->
                                    @elseif (in_array($key, ['status', 'status_tahun_ajaran']))
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}" required>
                                            <option selected disabled>Pilih {{ $label }}</option>
                                            <option value="aktif">aktif</option>
                                            <option value="nonaktif">nonaktif</option>
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- input fields yang lainnya -->
                                    @else
                                        <input
                                            type="{{ in_array($key, ['nisn', 'nip_guru_mapel', 'nip_wali_kelas']) ? 'number' : 'text' }}"
                                            class="form-control @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}" required>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            @endforeach

                            <div class="mt-4 mb-2 text-end btns simpan-nilai">
                                <button type="submit" class="btn btn-primary">Simpan {{ $buttonText }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('button-cetak').addEventListener('click', function() {
                alert('File presensi akan segera didownload');
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script>
            function exportExcel() {
                const table = document.getElementById("table-data");
                const wb = XLSX.utils.table_to_book(table, {
                    sheet: "{{ $buttonText }}",
                    raw: true
                });
                XLSX.writeFile(wb, "{{ buttonText }}.xlsx");
            }
        </script>

        {{-- connect assets untuk show notif suatu process --}}
        <script src="{{ asset('js/app.js') }}"></script>

        {{-- show notifs --}}
        @vite(['resources/js/app.js'])

        {{-- connect library untuk export ke excel --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.9/xlsx.full.min.js"></script>

        <!-- Connect Bootsrap bundle-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
</body>

</html>
