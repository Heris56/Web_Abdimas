<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Presensi</title>
  <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard-wali-kelas.css') }}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<x-navbar></x-navbar>
  @include('components.input-absen')
  <div class="container" id="content-wrapper">
    <div class="main-content">
        <div id="wrapper-info-wali-kelas">
            <div class="column" id="nama-wali-kelas">
                <H3>
                Hi, Devi Daviana Dasyla👋
                </H3>
            </div>
            <div class="column" id="kelas">
                <H5>
                Wali Kelas: XI-A
                </H5>
            </div>
            <div class="column" id="nip">
                <H5>
                NIP: 197806152005011001
                </H5>
            </div>
        </div>
        <div id="wrapper-top-bar-and-table">
        <div class="top-bar">
        <input type="text" placeholder="Cari Tanggal Presensi" class="form-control w-50"/>
        <div id="button-wrapper" class="d-flex justify-content-end">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-input" id="button-input">
        Input Presensi
        </button>
        <button type="button" class="btn btn-warning" id="button-cetak" onclick="exportExcel()">Cetak Presensi</button></div>
      </div>
      <table class="table table striped" id="table-presensi">
        <thead class="table-warning">
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  <script src="{{ asset('js/dashboard-wali-kelas.js') }}"></script>
  <script>
  document.getElementById('button-cetak').addEventListener('click', function () {
    alert('File presensi akan segera didownload');
  });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script>
  function exportExcel() {
  const table = document.getElementById("table-presensi");
  const wb = XLSX.utils.table_to_book(table, {
    sheet: "Presensi",
    raw: true
  });
  XLSX.writeFile(wb, "presensi-siswa.xlsx");
}
</script>
</body>

</html>