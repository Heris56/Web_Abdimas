<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Presensi</title>
  <link rel="stylesheet" href="{{ asset('css/dashboard-wali-kelas.css') }}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

  <!-- Conect CSS bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

  <!-- Connect CSS -->
  <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">


  <!-- Import Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
  <x-navbar></x-navbar>
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
          <button>Input Presensi</button>
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
              <td>-</td>
              <td>Hadir</td>
              <td>Hadir</td>
              <td>-</td>
            </tr>
            <tr>
              <td>Alvira</td>
              <td>Izin</td>
              <td>Hadir</td>
              <td>Hadir</td>
              <td>Hadir</td>
              <td>Hadir</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>