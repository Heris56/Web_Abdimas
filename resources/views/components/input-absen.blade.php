<div class="modal fade" id="modal-input" tabindex="-1" aria-labelledby="modal-input-label" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('dashboard.walikelas.add-tanggal') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal-input-label">Input Absen</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambahkan Jadwal Absen</button>
                </div>
            </div>
        </form>
    </div>
</div>
