<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form>
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal</label>
      <input type="date" class="form-control" id="tanggal" name="tanggal">
    </div>

    <div class="mb-3">
      <label for="nama" class="form-label">Nama Siswa</label>
      <input type="text" class="form-control" id="nama" name="nama_siswa">
    </div>

    <div class="mb-3">
      <label class="form-label">Keterangan</label>
      <div class="d-flex flex-wrap gap-3">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="hadir" value="Hadir">
          <label class="form-check-label" for="hadir">
            Hadir
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="dispensasi" value="Dispensasi">
          <label class="form-check-label" for="dispensasi">
            Dispensasi
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="sakit" value="Sakit">
          <label class="form-check-label" for="sakit">
            Sakit
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="keterangan" id="alpha" value="Alpha">
          <label class="form-check-label" for="alpha">
            Alpha
          </label>
        </div>
      </div>
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