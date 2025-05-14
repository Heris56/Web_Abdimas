<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Presensi</title>
  <link rel="stylesheet" href="{{ asset('css/dashboard-wali-kelas.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  
</head>
<body>
  @include('components.navbar')
  @include('components.input-absen')
  <div class="container" id="content-wrapper">
    <div class="main-content">
        <div id="wrapper-info-wali-kelas">
            <div class="column" id="nama-wali-kelas">
                <H3>
                Hi, Dafa Raimi Suandi👋
                </H3>
            </div>
            <div class="column" id="kelas">
                <H5>
                Wali Kelas: XI-A
                </H5>
            </div>
            <div class="column" id="nip">
                <H5>
                NIP: 999999999999
                </H5>
            </div>
        </div>
        <div id="wrapper-top-bar-and-table">
        <div class="top-bar">
        <input type="text" placeholder="Cari Tanggal Presensi" />
        <button type="button" class="btn" id="exampleModalLabel" data-bs-toggle="modal" data-bs-target="exampleModalLabel">Input Presensi</button>
        <button style="background-color: orange; color: white;">Cetak Presensi</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>Nama Siswa</th>
            <th>16 Januari 2023</th>
            <th>20 Januari 2023</th>
            <th>21 Januari 2023</th>
            <th>22 Januari 2023</th>
            <th>23 Januari 2023</th>
            <th>24 Januari 2023</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Tasha Ariana</td>
            <td>Hadir</td>
            <td>Hadir</td>
            <td>Sakit</td>
            <td>Hadir</td>
            <td>Hadir</td>
            <td>Dispen</td>
          </tr>
          <tr>
            <td>Alvira</td>
            <td>Izin</td>
            <td>Hadir</td>
            <td>Hadir</td>
            <td>Hadir</td>
            <td>Hadir</td>
            <td>Alpha</td>
          </tr>
        </tbody>
      </table>
        </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoZ5zYG3F1Vg+dG8aq9KrYs1B+kV0j8am41juGosG4H+gAN"
        crossorigin="anonymous"></script>
  <script src="{{ asset('js/dashboard-wali-kelas.js') }}"></script>
</body>
</html>
