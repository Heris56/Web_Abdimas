<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Presensi</title>
  <link rel="stylesheet" href="{{ asset('css/dashboard-wali-kelas.css') }}">
</head>
<body>
  <header>
    <button class="logo-btn">Logo Sekolah</button>
    <div class="search-bar">
      <input type="text" placeholder="Cari data siswa" />
    </div>
    <button class="logout-btn">Keluar</button>
  </header>

  <div class="container">

    <div class="main-content">
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
</body>
</html>
