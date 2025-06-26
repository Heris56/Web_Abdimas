<!DOCTYPE html>
<html lang="en">

<head>
    <title>Olah Nilai</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- CSRF Token biar bisa dipake di AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Connect jQuery, DOM Manipulation, AJAX -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-guru-mapel.css') }}">

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
    <x-navbar></x-navbar>

    <div class="content-wrapper container-fluid">
        <div class="Tabs d-flex align-items-center">
            <input type="text" class="form-control me-auto" id="cariSiswa" name="cariSiswa" placeholder="Cari Siswa">
            <div class="btns input-nilai me-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#inputNilaiModal">
                    Input Nilai
                </a>
            </div>

            <div class="btns cetak-nilai">
                <a href="{{ route('login-siswa') }}">
                    Cetak Nilai
                </a>
            </div>
        </div>

        <div class="Contents">
            <!-- Filter -->
            <div class="row">
                <div class="col-md-4">
                    <label for="mapelFilter" class="form-label">Filter by Mapel:</label>
                    <select id="mapelFilter" class="form-select">
                        <option value="">All Mapel</option>
                        @foreach ($mapelList as $mapel)
                            <option value="{{ $mapel }}">{{ $mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tahunFilter" class="form-label">Filter by Tahun Ajaran:</label>
                    <select id="tahunFilter" class="form-select">
                        <option value="">All Tahun Ajaran</option>
                        @foreach ($tahunPelajaranList as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="kelasFilter" class="form-label">Filter by Kelas:</label>
                    <select id="kelasFilter" class="form-select">
                        <option value="">All Kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas }}">{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div id="tableContainer">
                <div class="header mb-2 mt-2"><span class="head">Semua Mapel</span></div>

                <table class="table table-bordered" id="nilaiTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tahun Ajaran</th>
                            @foreach ($kegiatanList as $kegiatan)
                                <th>{{ $kegiatan }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_nilai as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->nisn }}</td>
                                <td>{{ $row->nama_siswa }}</td>
                                <td>{{ $row->id_kelas }}</td>
                                <td>
                                    @if ($row->semester === 'Ganjil')
                                        {{ $row->tahun_pelajaran }}-1
                                    @elseif ($row->semester === 'Genap')
                                        {{ $row->tahun_pelajaran }}-2
                                    @else
                                        {{ $row->tahun_pelajaran }}
                                    @endif
                                </td>
                                @foreach ($kegiatanList as $kegiatan)
                                    @php
                                        $alias = str_replace(' ', '_', strtolower($kegiatan));
                                    @endphp
                                    <td class="editable" data-nisn="{{ $row->nisn }}"
                                        data-field="{{ $kegiatan }}">{{ $row->$alias ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="inputNilaiModal" tabindex="-1" aria-labelledby="inputNilaiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inputNilaiModalLabel">Input Nilai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="inputNilaiForm">
                            <div class="mb-3">
                                <label for="mapelSelect" class="form-label">Mata Pelajaran</label>
                                <select id="mapelSelect" class="form-select" required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach ($mapelList as $mapel)
                                        <option value="{{ $mapel }}">{{ $mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tahunSelect" class="form-label">Tahun Ajaran</label>
                                <select id="tahunSelect" class="form-select" required>
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach ($tahunPelajaranList as $tahun)
                                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nisnSelect" class="form-label">Siswa</label>
                                <select id="nisnSelect" class="form-select" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach ($data_nilai as $row)
                                        <option value="{{ $row->nisn }}">{{ $row->nama_siswa }}
                                            ({{ $row->nisn }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kegiatanSelect" class="form-label">Kegiatan</label>
                                <select id="kegiatanSelect" class="form-select" required>
                                    <option value="">Pilih Kegiatan</option>
                                    @foreach ($kegiatanList as $kegiatan)
                                        <option value="{{ $kegiatan }}">{{ $kegiatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nilaiInput" class="form-label">Nilai</label>
                                <input type="number" id="nilaiInput" class="form-control" required min="0"
                                    max="100">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buat notif kecil nyimpen error/success -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Connect Bootsrap bundle-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

        <!-- Connect Custom JS -->
        <script src="{{ asset('js/dashboard-guru-mapel.js') }}"></script>
</body>

</html>
