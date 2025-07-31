<div class="table1 mb-3" style="overflow-x: auto; white-space: nowrap;" class="mb-3">
    @csrf
    <table class="table table-striped" id="table-presensi" style="min-width: 100%;">
        <thead class="table-warning">
            <tr>
                <th style="width: fit-content;">Nama Siswa</th>
                @php use Carbon\Carbon;
                @endphp
                @foreach($tanggal_list as $tanggals)
                    @php
                        $tanggal = Carbon::parse($tanggals);
                        $hari = $tanggal->translatedFormat('l');
                        $tanggalFormat = $tanggal->format('d-m-Y');
                    @endphp
                    <th style="width: fit-content;">
                        <div class="d-flex flex-column align-items-center justify-content-center text-center">
                            <button type="button" class="btn btn-danger btn-delete-tanggal" data-tanggal="{{ $tanggals }}"
                                style="width: 90px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-calendar-x-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2M6.854 8.146 8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 1 1 .708-.708" />
                                </svg></button>
                            <div><strong>{{ $hari }}, <br> {{ $tanggalFormat }}</strong></div>
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($list_siswa as $siswa)
                <tr>
                    <td style="width: fit-content;">{{ $siswa->nama_siswa }}</td>
                    @foreach($tanggal_list as $tanggals)
                        @php
                            $presensi = $data_absen->firstWhere(
                                fn($item) =>
                                $item->nisn_siswa == $siswa->nisn_siswa &&
                                $item->tanggal == $tanggals
                            );
                            if (!$presensi) {
                                $presensi = (object) [
                                    'nisn_siswa' => $siswa->nisn_siswa,
                                    'tanggal' => $tanggals,
                                    'keterangan_absen' => null,
                                ];
                            }
                        @endphp
                        <td>
                            <select class="form-select" name="absen[{{ $siswa->nisn_siswa }}][{{ $tanggals }}]"
                                data-nisn="{{ $siswa->nisn_siswa }}" data-tanggal="{{ $tanggals }}"
                                data-tahun_ajaran="{{ $wali->tahun_ajaran }}" onchange="updateKehadiran(this)">
                                <option value="" {{ $presensi->keterangan_absen === '-' || $presensi->keterangan_absen === null ? 'selected' : '' }}>-</option>
                                @foreach(['Hadir', 'Sakit', 'Dispensasi', 'Izin', 'Alpha'] as $keterangan_absen)
                                    <option value="{{ $keterangan_absen }}" {{ ($presensi->keterangan_absen ?? '') === $keterangan_absen ? 'selected' : '' }}>
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
</div>