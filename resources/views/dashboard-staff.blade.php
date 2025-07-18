<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Olah Data SMK PGRI 35</title>

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Conect Icons bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-staff.css') }}">

    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    {{-- XLSX CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- show notifs -->
    @vite(['resources/js/app.js'])

    <!-- Connect Bootsrap bundle-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>

<body>
    {{-- import small notifs --}}
    <x-toast-notification />

    {{-- import navbar --}}
    <x-navbar />
    {{-- ini buat session success atau gagal --}}
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    @if (session('error') || session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    document.querySelectorAll('.alert').forEach(function(alert) {
                        alert.style.transition = 'Opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 500);
                    });
                }, 10000);
            });
        </script>
    @endif
    {{-- end of alert session --}}

    <div class="content-wrapper container-fluid">
        <div class="Tabs d-flex align-items-center">
            <input type="text" class="form-control me-auto" id="cariSiswa" name="cariSiswa" placeholder="Cari Siswa">
            <div class="btns input-nilai me-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#inputNilaiModal">
                    Input Data {{ $buttonText }}
                </a>
            </div>

            <div class="btns cetak-nilai">
                <button id="button-cetak" class="btn btn-success">
                    Cetak {{ $buttonText }}
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
                        <th>Actions</th>
                        @if ($type == 'tahun_ajaran')
                            <th>
                                Tahun Ajaran Terbaru
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @foreach ($columns as $key => $label)
                                <td>
                                    @if ($key == 'is_current')
                                        {{ $item->$key ? 'True' : 'False' }}
                                    @else
                                    {{ $item->$key ?? '-' }}
                                    @endif
                                    
                                </td>
                            @endforeach
                            <td class="text-center">
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#UpdateNilaiModal"
                                    data-id="
                                    @if ($type == 'siswa') {{ $item->nisn }}
                                    @elseif($type == 'kelas'){{ $item->id_kelas }}
                                    @elseif($type == 'guru_mapel'){{ $item->nip_guru_mapel }}
                                    @elseif($type == 'wali_kelas'){{ $item->nip_wali_kelas }}
                                    @elseif($type == 'mapel'){{ $item->id_mapel }}
                                    @elseif($type == 'tahun_ajaran'){{ $item->id_tahun_ajaran }}
                                    @elseif($type == 'paket_mapel'){{ $item->id_paket }}
                                    @else{{ '' }} @endif
                                "
                                    {{-- Loop untuk menambahkan semua data kolom sebagai data-* attributes --}}
                                    @foreach ($columns as $key => $label)
                                    {{-- Menggunakan $item->$key langsung, pastikan nilainya ada --}}
                                    data-{{ $key }}="{{ $item->$key ?? '' }}" @endforeach
                                    {{-- Tambahkan juga foreign key atau primary key yang mungkin tidak langsung di $columns untuk dropdown --}}
                                    @if (!in_array('id_kelas', array_keys($columns))) data-id_kelas="{{ $item->id_kelas ?? '' }}" @endif
                                    @if (!in_array('id_mapel', array_keys($columns))) data-id_mapel="{{ $item->id_mapel ?? '' }}" @endif>
                                    Perbarui
                                </button>
                            </td>
                            @if ($type == 'tahun_ajaran')
                                <td class="text-center">
                                    <button class="btn btn-warning" onclick="setTahunAjaran('{{ $item->id_tahun_ajaran }}', '{{ $item->tahun }}')" data-bs-toggle="modal" data-bs-target="#myInfoModal">
                                        Terapkan
                                    </button>
                                </td>
                            @endif
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
                    <a href="{{ route('data.fetch', 'siswa') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'siswa' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'siswa' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-house-door me-2"></i> Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'guru_mapel') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'guru_mapel' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'guru_mapel' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-person-lines-fill me-2"></i> Guru Mapel
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'wali_kelas') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'wali_kelas' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'wali_kelas' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-person-lines-fill me-2"></i> Wali Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'mapel') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'mapel' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'mapel' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-journal-check me-2"></i> Mata Pelajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'kelas') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'kelas' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'kelas' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-bar-chart-line me-2"></i> Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'tahun_ajaran') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'tahun_ajaran' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'tahun_ajaran' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-bar-chart-line me-2"></i> Tahun Ajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('data.fetch', 'paket_mapel') }}"
                        class="nav-link {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'paket_mapel' ? 'active' : '' }}"
                        {{ Route::is('data.fetch') && Route::current()->parameter('type') == 'paket_mapel' ? 'aria-current=page' : '' }}>
                        <i class="bi bi-bar-chart-line me-2"></i> Paket Mapel
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        {{-- ini bagian untuk Update nilai/data --}}
        <div class="modal fade" id="UpdateNilaiModal" tabindex="-1" aria-labelledby="UpdateNilaiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="UpdateNilaiModalLabel">Update Data {{ $buttonText }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Berikan ID pada form untuk JavaScript --}}
                        <form id="updateForm" method="POST" action="{{ old('item_id') ? route('data.update', ['type' => $type, 'id' => old('item_id')]) : '' }}">
                            @csrf
                            @method('PUT') {{-- Gunakan method PUT atau PATCH untuk update --}}

                            {{-- Input hidden untuk menyimpan ID item yang akan diupdate. Nama 'item_id' akan dibaca oleh controller --}}
                            <input type="hidden" name="item_id" id="updateItemId">
                            <input type="hidden" name="tahun_ajaran_id" id="tahun_ajaran_id">
                            <input type="hidden" name="tahun_ajaran_nilai" id="tahun_ajaran_nilai">


                            @foreach ($columns as $key => $label)
                                <div class="mb-3">
                                    @if ($type == "paket_mapel" && $key =='tahun_ajaran')
                                          
                                    @else
                                    <label for="update_{{ $key }}"
                                            class="form-label">{{ $label }}</label>   
                                    @endif

                                    @if ($type == 'siswa'  && $key == 'id_kelas')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key)? '' : 'selected' }} selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['kelas'] as $item)
                                                <option value="{{  $item->id_kelas}}" {{ old($key) == $item->id_kelas ? 'selected' : '' }}>
                                                    {{  $item->id_kelas}}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada mata pelajaran tersedia</option>
                                            @endforelse
                                        </select>
                                    {{-- Sesuaikan id untuk setiap input --}}
                                    @elseif ($type == 'wali_kelas'  && $key == 'id_kelas')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key)? '' : 'selected' }} selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['kelas'] as $item)
                                                <option value="{{  $item->id_kelas}}" {{ old($key) == $item->id_kelas ? 'selected' : '' }}>
                                                    {{  $item->id_kelas}}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada Kelas tersedia</option>
                                            @endforelse
                                        </select>
                                    @elseif ($type == 'paket_mapel'  && $key == 'id_kelas')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key)? '' : 'selected' }} selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['kelas'] as $item)
                                                <option value="{{  $item->id_kelas}}" {{ old($key) == $item->id_kelas ? 'selected' : '' }}>
                                                    {{  $item->id_kelas}}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada mata pelajaran tersedia</option>
                                            @endforelse
                                        </select>
                                    {{-- Sesuaikan id untuk setiap input --}}
                                    @elseif ($type == 'paket_mapel'  && $key == 'id_mapel')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key)? '' : 'selected' }} selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['mapel'] as $item)
                                                <option value="{{  $item->id_mapel}}" {{ old($key) == $item->id_mapel ? 'selected' : '' }}>
                                                    {{  $item->id_mapel}} - {{ $item->nama_mapel }}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada mata pelajaran tersedia</option>
                                            @endforelse
                                        </select>
                                        <!-- set dropdown untuk pilih mapel dan kelas -->
                                    @elseif ($type == 'paket_mapel'  && $key == 'tahun_ajaran')
                                    @elseif ($type == 'guru_mapel' && $key == 'kode_paket')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key) ? '' : 'selected' }} selected disabled>Pilih {{ $label }}
                                            </option>
                                            @foreach ($dropdowns['paket_mapel'] as $mapelItem)
                                                <option value="{{ $mapelItem->kode_paket }}" {{ old($key) == $mapelItem->kode_paket ? 'selected' : '' }}>
                                                    {{ $mapelItem->kode_paket }}</option>
                                            @endforeach
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif (in_array($key, ['status']))
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key) ? '' : 'selected' }} disabled>Pilih {{ $label }}
                                            </option>
                                            <option value="aktif" {{ old($key) ? 'aktif' : 'selected' }}>Aktif</option>
                                            <option value="nonaktif" {{ old($key) ? 'nonaktif' : 'selected' }}>Nonaktif</option>
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif ($key == 'jurusan')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" selected disabled>Pilih {{ $label }}</option>
                                            {{-- PASTIKAN VALUE SAMA PERSIS DENGAN DATABASE --}}
                                            <option value="Teknik Komputer Jaringan" {{ old($key) == 'Teknik Komputer Jaringan' ? 'selected' : '' }}>Teknik Komputer dan Jaringan</option>
                                            <option value="Rekayasa Perangkat Lunak" {{ old($key) == 'Rekayasa Perangkat Lunak' ? 'selected' : '' }}>Rekayasa Perangkat Lunak</option>
                                            {{-- Jika ada jurusan lain di database, tambahkan opsi di sini --}}
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif ($key == 'is_current')
                                        <input type='number'
                                        class="form-control @error($key) is-invalid @enderror"
                                        id="update_{{ $key }}"
                                        value="{{ old($key) }}"
                                        readonly
                                        name="" {{-- kosongkan name agar tidak dikirim --}}
                                        onkeydown="return false;"
                                        onwheel="this.blur()">
                                    @elseif($type == 'tahun_ajaran' && $key == 'semester')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                        id="update_{{ $key }}" name="{{ $key }}" required>
                                            <option value="" selected disabled>Pilih {{ $label }}</option>
                                            {{-- PASTIKAN VALUE SAMA PERSIS DENGAN DATABASE --}}
                                            <option value="Ganjil" {{ old($key) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="Genap" {{ old($key) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                            {{-- Jika ada jurusan lain di database, tambahkan opsi di sini --}}
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif ($key == 'tahun_ajaran')
                                        <input type='text'
                                        class="form-control @error($key) is-invalid @enderror"
                                        id="{{ $key }}" value= {{ $current_year->tahun }}  disabled >
                                        <input type="hidden" name="tahun_ajaran" value="{{ $current_year->tahun }}">
                                    @else
                                        <input
                                            type="text"
                                            class="form-control @error($key) is-invalid @enderror"
                                            id="update_{{ $key }}" name="{{ $key }}" value="{{ old($key) }}" required>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            @endforeach

                            <div class="mt-4 mb-2 text-end btns simpan-nilai">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- end of update nilai/data --}}

        {{-- ini bagian untuk input nilai/data --}}

        <div class="modal fade" id="inputNilaiModal" tabindex="-1" aria-labelledby="inputNilaiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inputNilaiModalLabel">Input Data {{ $buttonText }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('data.input', ['type' => $type]) }}" method="POST">
                            @csrf
                            @foreach ($columns as $key => $label)
                                <div class="mb-3">
                                    <label for="{{ $key }}" class="form-label">{{ $label }}</label>

                                    <!-- set dropdown untuk kelas pada siswa dan wali kelas -->

                                    @if (($type == 'wali_kelas' || $type == 'siswa')  && $key == 'id_kelas')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="{{ $key }}" name="{{ $key }}" required>
                                            <option selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['kelas'] as $item)
                                                <option value="{{  $item->id_kelas}}">
                                                    {{  $item->id_kelas}}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada kelas tersedia</option>
                                            @endforelse
                                        </select>
                                    @elseif ($type == 'paket_mapel'  && $key == 'id_kelas')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key)? '' : 'selected' }} selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['kelas'] as $item)
                                                <option value="{{  $item->id_kelas}}" {{ old($key) == $item->id_kelas ? 'selected' : '' }}>
                                                    {{  $item->id_kelas}}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada kelas tersedia</option>
                                            @endforelse
                                        </select>
                                    @elseif ($type == 'paket_mapel'  && $key == 'id_mapel')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                                id="{{ $key }}" name="{{ $key }}" required>
                                            <option value="" {{ old($key)? '' : 'selected' }} selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['mapel'] as $item)
                                                <option value="{{  $item->id_mapel}}" {{ old($key) == $item->id_mapel ? 'selected' : '' }}>
                                                    {{  $item->id_mapel}} - {{ $item->nama_mapel }}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada mata pelajaran tersedia</option>
                                            @endforelse
                                        </select>
                                        <!-- set dropdown untuk pilih mapel dan kelas -->
                                    @elseif ($type == 'guru_mapel' && $key == 'kode_paket')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}" required>
                                            <option selected disabled>Pilih {{ $label }}</option>
                                            @forelse ($dropdowns['paket_mapel'] as $item)
                                                <option value="{{ $item->kode_paket }}">{{ $item->kode_paket }} - {{ $item->tahun_ajaran }}
                                                </option>
                                            @empty
                                                <option disabled>Tidak ada Paket tersedia</option>
                                            @endforelse
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- set dropdown untuk pilih status dan status tahun ajaran -->
                                    @elseif (in_array($key, ['status']))
                                        <select class="form-select @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}" required>
                                            <option {{ old($key) ? '' : 'selected' }} disabled>Pilih {{ $label }}</option>
                                            <option value="aktif" {{ old($key) == 'aktif' ? 'selected' : '' }}>aktif</option>
                                            <option value="nonaktif" {{ old($key) == 'nonaktif' ? 'selected' : '' }}>nonaktif</option>
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @elseif ($key == 'jurusan')
                                            <select class="form-select @error($key) is-invalid @enderror"
                                                id="{{ $key }}" name="{{ $key }}" required>
                                                <option selected disabled>Pilih {{ $label }}</option>
                                                <option value="Teknik Komputer Jaringan">Teknik Komputer Jaringan</option>
                                                <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                            </select>
                                            @error($key)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror 

                                        <!-- input fields yang lainnya -->
                                         <!-- block bagian tahun ajaran dan is curent biar gak bisa diubah -->
                                    @elseif ($key == 'is_current' )
                                        <input type='number'
                                        class="form-control @error($key) is-invalid @enderror"
                                        id="{{ $key }}" value= 0 disabled >
                                    @elseif($type == 'tahun_ajaran' && $key == 'semester')
                                        <select class="form-select @error($key) is-invalid @enderror"
                                        id="{{ $key }}" name="{{ $key }}" required>
                                            <option value="" selected disabled>Pilih {{ $label }}</option>
                                            {{-- PASTIKAN VALUE SAMA PERSIS DENGAN DATABASE --}}
                                            <option value="Ganjil" {{ old($key) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="Genap" {{ old($key) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                            {{-- Jika ada jurusan lain di database, tambahkan opsi di sini --}}
                                        </select>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif ($key == 'tahun_ajaran')
                                        <input type='text'
                                        class="form-control @error($key) is-invalid @enderror"
                                        id="{{ $key }}" value= {{ $current_year->tahun }}  disabled >
                                        <input type="hidden" name="tahun_ajaran" value="{{ $current_year->tahun }}">
                                    @else
                                        <input
                                            type="text"
                                            class="form-control @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}" value="{{ old($key) }}" required>
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

        {{-- ini modal konfirmasi tahun ajaran --}}
        <x-confirm-tahun-ajaran id="myInfoModal" title="Konfirmasi Perubahan Tahun Ajaran">
            <div style="color:black;">
                <div>
                    Apakah anda yakin ingin mengubah tahun ajaran yang berjalan?
                </div>
                <div>Semua data tahun ajaran pada user siswa, guru mapel, dan wali kelas akan diubah menjadi tahun <span id="preview_nilai_tahun_ajaran" style="color: red;"></span></div>
            </div>
            
            <x-slot:footer>
                <div class="d-flex justify-content-end w-25 gap-2 ">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#confirmModal">
                        <span style="color: red;">Konfirmasi</span>
                    </button>
                </div>
            </x-slot:footer>
        </x-confirm-tahun-ajaran>

        <x-confirm-tahun-ajaran id="confirmModal" title="Konfirmasi Password">
            <form action="{{ route('data.confirmpassword') }}" method="post">
                @csrf
                <input type="hidden" name="id_tahun_ajaran" id="id_tahun_ajaran_input">
                <input type="hidden" name="nilai_tahun_ajaran" id="nilai_tahun_ajaran_input">

                <div class="mb-3">
                    <label for="" class="form-label">Masukan Password Admin anda</label>
                    <input id="password_admin" name="password_admin" type="password" class="form-control @error('password_admin') is-invalid @enderror" placeholder="Password">
                    @error('password_admin')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="modal-footer"> {{-- Add Bootstrap's modal-footer class here manually --}}
                    <div class="d-flex">
                        <button type="submit" class="btn btn-danger">Konfirmasi</button>
                    </div>
                </div>
            </form>

            
        </x-confirm-tahun-ajaran>

        {{-- end of modal --}}

        <script>
            function setTahunAjaran(id, tahun) {
                document.getElementById('tahun_ajaran_id').value = id; // untuk update form
                document.getElementById('id_tahun_ajaran_input').value = id; // untuk modal password
                document.getElementById('tahun_ajaran_nilai').value = tahun; // untuk update form
                document.getElementById('nilai_tahun_ajaran_input').value = tahun; // untuk info di password
                document.getElementById('preview_nilai_tahun_ajaran').innerText = tahun; // <--- update preview di modal
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const updateModal = document.getElementById('UpdateNilaiModal');

                updateModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    // Ambil ID dari data-id attribute tombol (ini sudah diperbaiki di Blade)
                    const itemId = button.getAttribute('data-id');

                    const updateForm = document.getElementById('updateForm');
                    const updateItemIdInput = document.getElementById('updateItemId');

                    updateItemIdInput.value = itemId;

                    const type = "{{ $type }}"; // Ambil $type dari Blade
                    updateForm.action =
                        `/dashboard/staff/data/update/${type}/${itemId}`; // Sesuai dengan route Anda

                    // Ambil array $columns dari PHP (ini hanya untuk tahu kolom apa saja yang ada)
                    const columnsFromPhp =
                        @json(array_keys($columns)); // Kita hanya perlu nama key (nama kolom)

                    // Loop melalui semua kunci kolom yang ada di tampilan
                    columnsFromPhp.forEach(key => {
                        let actualInputKey =
                            key; // Asumsi default: nama kolom di DB sama dengan nama input

                        // Khusus untuk kolom yang ditampilkan (hasil join) tapi inputnya adalah ID
                        if (key === 'nama_kelas') {
                            actualInputKey = 'id_kelas';
                        }

                        // Ambil nilai dari data-attribute yang sesuai dengan actualInputKey (misal data-id_kelas)
                        // Jika data-attribute untuk key asli tidak ada, coba ambil dari actualInputKey
                        let value = button.getAttribute('data-' + actualInputKey);
                        // Fallback jika value masih null/undefined (misal data-nama_kelas tapi mau id_kelas)
                        if (value === null && key !== actualInputKey) {
                            value = button.getAttribute('data-' +
                                key); // Coba ambil dari data-nama_kelas jika ada
                        }


                        const inputElement = document.getElementById('update_' + actualInputKey);

                        if (inputElement) {
                            // Untuk elemen <select>
                            if (inputElement.tagName === 'SELECT') {
                                // Set nilai select langsung
                                inputElement.value = value;
                                // Opsional: Jika nilai tidak ditemukan di opsi (misal data kotor),
                                // pastikan placeholder terpilih jika value tidak ada
                                if (inputElement.value !== value && value !== null && value !== '') {
                                    inputElement.value = ''; // Set ke placeholder
                                }
                            } else {
                                // Untuk elemen <input>
                                inputElement.value = value;
                            }
                        }
                    });

                    // Penanganan khusus untuk dropdown status/status_tahun_ajaran
                    // Ini tetap perlu karena loop di atas mungkin tidak selalu bekerja sempurna
                    // untuk semua kasus select, atau untuk klarifikasi.
                    const selectStatus = document.getElementById('update_status');
                    if (selectStatus && button.hasAttribute('data-status')) {
                        selectStatus.value = button.getAttribute('data-status');
                    }

                    const selectStatusTA = document.getElementById('update_status_tahun_ajaran');
                    if (selectStatusTA && button.hasAttribute('data-status_tahun_ajaran')) {
                        selectStatusTA.value = button.getAttribute('data-status_tahun_ajaran');
                    }

                    // Penanganan khusus untuk dropdown jurusan
                    const selectJurusan = document.getElementById('update_jurusan');
                    if (selectJurusan && button.hasAttribute('data-jurusan')) {
                        selectJurusan.value = button.getAttribute('data-jurusan');
                    }
                });
            });
        </script>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cek apakah form input sedang digunakan atau form update
            @if ($errors->has('password_admin'))
                const confirmPasswordModal = new bootstrap.Modal(document.getElementById('confirmModal')); // ID modal konfirmasi password Anda
                confirmPasswordModal.show();
            @elseif (old('item_id'))
                // Jika item_id ada, artinya dari modal update
                const updateModal = new bootstrap.Modal(document.getElementById('UpdateNilaiModal'));
                updateModal.show();
            @else
                // Jika tidak, berarti dari modal input
                const inputModal = new bootstrap.Modal(document.getElementById('inputNilaiModal'));
                inputModal.show();
            @endif
        });
    </script>
    @endif

        <script>
            document.getElementById('button-cetak').addEventListener('click', function() {
                showToast('Mencetak {{ $buttonText }}...', 'text-bg-primary');
                exportExcel('{{ $buttonText }}', '{{ str_replace(' ', '_', $buttonText) }}_{{ date('YmdHis') }}');
            });
        </script>
</body>

</html>
