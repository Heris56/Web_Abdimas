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
</head>

<body>
    @if(session('error'))
    <script>
        alert('{{ session('error') }}');
    </script>
    @endif

    @if(session('success'))
    <script>
        alert('{{ session('success') }}');
    </script>
    @endif

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
            </div>
            @endif
            <div id="wrapper-top-bar-and-table">
                <div class="top-bar">
                    <input type="text" placeholder="Cari Nama Siswa" class="form-control w-50" id="search_siswa"/>
                    <div id="button-wrapper" class="d-flex justify-content-end">
                        <button type="button" class="btn btn-warning" id="button-cetak" onclick="exportExcel()">Cetak
                            Presensi</button>
                    </div>
                </div>
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