<div class="modal fade" id="modal-input" tabindex="-1" aria-labelledby="modal-input-label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modal-input-label">Input Absen</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form>
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal</label>
      <input type="date" class="form-control" id="tanggal" name="tanggal">
    </div>

    <div class="mb-3">
      <label for="nama" class="form-label fw-bold">Nama Siswa/Siswi</label>
      <table class="table mt-3">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Tasha Ariana</td>
        <td><div class="d-flex flex-wrap gap-3">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="hadir" value="Hadir">
          <label class="form-check-label" for="hadir">Hadir</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="dispensasi" value="Dispensasi">
          <label class="form-check-label" for="dispensasi">Dispensasi</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="sakit" value="Sakit">
          <label class="form-check-label" for="sakit">Sakit</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="alpha" value="Alpha">
          <label class="form-check-label" for="alpha">Alpha</label>
        </div>
      </div></td>
      </tr>
      <tr>
        <td>Alvira</td>
        <td><div class="d-flex flex-wrap gap-3">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="hadir" value="Hadir">
          <label class="form-check-label" for="hadir">Hadir</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="dispensasi" value="Dispensasi">
          <label class="form-check-label" for="dispensasi">Dispensasi</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="sakit" value="Sakit">
          <label class="form-check-label" for="sakit">Sakit</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="alpha" value="Alpha">
          <label class="form-check-label" for="alpha">Alpha</label>
        </div>
      </div></td>
      </tr>
    </tbody>
  </table>
    </div>
  </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>