<div class="modal fade" id="modal-delete" tabindex="-1" aria-labelledby="modal-delete-label" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('dashboard.walikelas.add-tanggal') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal-delete-label">Konfirmasi Delete</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Apakah anda yakin untuk menghapus Tanggal ini?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
                    <button type="submit" class="btn btn-secondary">Tambahkan Jadwal Absen</button>
                </div>
            </div>
        </form>
    </div>
</div>
