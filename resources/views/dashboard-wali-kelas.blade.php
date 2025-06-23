<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Presensi</title>
    <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-wali-kelas.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-edit-kehadiran" content="{{ route('dashboard.walikelas.edit-kehadiran') }}">
</head>

<body>
    @php
        $wali = $data_wali_kelas->first();
        $data_absen = $data_absen;
    @endphp
    <x-navbar></x-navbar>
    @include('components.input-absen')
    <div class="container" id="content-wrapper">
        <div class="main-content">
            <div id="wrapper-info-wali-kelas">
                @if($wali)
                    <div class="column" id="nama-wali-kelas">
                        <H3>
                            Hi, {{ $wali->nama }}👋
                        </H3>
                    </div>
                    <div class="column" id="kelas">
                        <H5>
                            Wali Kelas: {{ $wali->id_kelas }}
                        </H5>
                    </div>
                    <div class="column" id="nip">
                        <H5>
                            NIP: {{ $wali->nip_wali_kelas }}
                        </H5>
                    </div>
                    <div id="button-ganti-password">
                        <a href="{{ route('dashboard.walikelas.ganti-password') }}">
                        <button type="button" class="btn btn-warning">
                            Ganti Password
                        </button>
                        </a>
                    </div>
            </div>
            @endif

            <div id="wrapper-top-bar-and-table">
                <div class="top-bar">
                    <input type="text" placeholder="Cari Nama Siswa" class="form-control w-50" id="search_siswa"/>
                    <div id="button-wrapper" class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#modal-input" id="button-input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-calendar-plus" viewBox="0 0 16 16">
                                        <path
                                            d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7" />
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                        </button>
                        <button type="button" class="btn btn-warning" id="button-cetak" onclick="exportExcel()">Cetak
                        Presensi</button>
                    </div>
                </div>
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
                @endif
                @include('components.table-presensi')
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
        <script src="{{ asset('js/dashboard_wali_kelas.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    </body>
</html>