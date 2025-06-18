                    @csrf
                    <table class="table table striped" id="table-presensi">
                    <thead class="table-warning">
                        <tr>
                            <th>Nama Siswa</th>
                            @foreach($tanggal_list as $tanggals)
                                <th>
                                <button type="button" class="btn btn-outline-danger btn-delete-tanggal" data-tanggal="{{ $tanggals }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-x-fill" viewBox="0 0 16 16">
  <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2M6.854 8.146 8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 1 1 .708-.708"/>
</svg></button>{{ $tanggals }}</th>
                            @endforeach
                            <th><button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#modal-input" id="button-input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-calendar-plus" viewBox="0 0 16 16">
                                        <path
                                            d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7" />
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                    </svg>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_siswa as $siswa)
                            <tr>
                                <td>{{ $siswa->nama_siswa }}</td>
                                @foreach($tanggal_list as $tanggals)
                                    @php
                                        $presensi = $data_absen->firstWhere(fn($item) =>
                                        $item->nisn_siswa == $siswa->nisn_siswa &&
                                        $item->tanggal == $tanggals
                                        );
                                    @endphp
                                    <td>
                                        <select class="form-select"
                                            name="absen[{{ $siswa->nisn_siswa }}][{{ $tanggals }}]"
                                            data-nisn ="{{ $siswa->nisn_siswa }}"
                                            data-tanggal="{{ $tanggals }}"
                                            onchange ="updateKehadiran(this)">
                                            <option value="">-</option>
                                            @foreach(['Hadir', 'Sakit', 'Dispensasi', 'Izin', 'Alpha'] as $keterangan_absen)
                                                <option value="{{ $keterangan_absen }}"
                                                    {{ ($presensi->keterangan_absen ?? '-') === $keterangan_absen ? 'selected' : '-' }}>
                                                    {{ $keterangan_absen }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                    </tbody>
                </table>