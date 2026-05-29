<!-- Belum Ditindak Section -->
<section id="belum-ditindak" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Surat Belum Ditindak</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="input-group me-2" style="width: 300px;">
                <input type="text" class="form-control" id="searchBelumDitindak" placeholder="Cari surat berdasarkan perihal atau pengirim...">
                <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
            </div>
            <select class="form-select me-2" id="filterDivisiBelum" style="width: auto;">
                <option value="">Semua Divisi</option>
                <option value="umum">Umum & Kepegawaian</option>
                <option value="keuangan">Keuangan</option>
                <option value="perencanaan">Perencanaan</option>
                <option value="hukum">Hukum & Kerjasama</option>
                <option value="ti">Teknologi Informasi</option>
            </select>
            <select class="form-select me-2" id="filterUrgensi" style="width: auto;">
                <option value="">Semua Urgensi</option>
                <option value="tinggi">Tinggi</option>
                <option value="sedang">Sedang</option>
                <option value="rendah">Rendah</option>
            </select>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">Ekspor</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Belum Ditindak</h6>
                            <h3 class="card-text" id="belumDitindakCount">{{ $belumDitindakCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Lebih dari 7 Hari</h6>
                            <h3 class="card-text" id="overdueCount">{{ $overdueCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Rata-rata Waktu</h6>
                            <h3 class="card-text" id="avgTimeCount">{{ $avgTimeCount 
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tanggal Terima</th>
                            <th scope="col">Pengirim</th>
                            <th scope="col">Perihal</th>
                            <th scope="col">Divisi</th>
                            <th scope="col">Lama Hari</th>
                            <th scope="col">Urgensi</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="belumDitindakTableBody">
                        @php
                            $belumDitindak = \App\Models\SuratMasuk::with(['divisi', 'riwayatStatus' => function($query) {
                                $query->orderBy('tanggal', 'desc');
                            }])->where('status', 'baru')->latest()->get()->map(function ($item) {
                                $tanggalTerima = \Carbon\Carbon::parse($item->tanggal);
                                $hariIni = \Carbon\Carbon::now();
                                $lamaHari = $tanggalTerima->diffInDays($hariIni);

                                if ($lamaHari >= 7) {
                                    $urgensi = 'tinggi';
                                } elseif ($lamaHari >= 3) {
                                    $urgensi = 'sedang';
                                } else {
                                    $urgensi = 'rendah';
                                }

                                return [
                                    'id' => $item->id,
                                    'nomor_surat' => $item->nomor_surat,
                                    'tanggal' => $item->tanggal->format('d/m/Y'),
                                    'pengirim' => $item->pengirim,
                                    'perihal' => $item->perihal,
                                    'divisi_code' => $item->divisi ? $item->divisi->code : null,
                                    'divisi_name' => $item->divisi ? $item->divisi->nama_divisi : 'N/A',
                                    'lama_hari' => $lamaHari,
                                    'urgensi' => $urgensi,
                                    'file_path' => $item->file_path,
                                    'ukuran' => $item->ukuran,
                                    'riwayat_count' => $item->riwayatStatus->count(),
                                ];
                            });
                        @endphp
                        @foreach($belumDitindak as $index => $item)
                        <tr class="{{ $item['lama_hari'] > 7 ? 'table-danger' : ($item['lama_hari'] > 3 ? 'table-warning' : '') }}">
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $item['tanggal'] }}</td>
                            <td>{{ $item['pengirim'] }}</td>
                            <td>{{ $item['perihal'] }}</td>
                            <td><span class="divisi-tag divisi-{{ $item['divisi_code'] }}">{{ $item['divisi_name'] }}</span></td>
                            <td>
                                <span class="badge {{ $item['lama_hari'] > 7 ? 'bg-danger' : ($item['lama_hari'] > 3 ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ $item['lama_hari'] }} hari
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $item['urgensi'] == 'tinggi' ? 'bg-danger' : ($item['urgensi'] == 'sedang' ? 'bg-warning' : 'bg-success') }}">
                                    {{ ucfirst($item['urgensi']) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary" onclick="lihatBelumDitindak({{ $item['id'] }})" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="tindakSurat({{ $item['id'] }})" title="Tindak">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="reminderSurat({{ $item['id'] }})" title="Kirim Reminder">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Belum Ditindak -->
    <div class="modal fade" id="modalLihatBelumDitindak" tabindex="-1" aria-labelledby="modalLihatBelumDitindakLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLihatBelumDitindakLabel">Detail Surat Belum Ditindak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Nomor Surat</strong></td>
                                    <td width="5%">:</td>
                                    <td id="detailBelumNomorSurat"></td>
                                </tr>
                                <tr>
                                    <td><strong>Pengirim</strong></td>
                                    <td>:</td>
                                    <td id="detailBelumPengirim"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Terima</strong></td>
                                    <td>:</td>
                                    <td id="detailBelumTanggal"></td>
                                </tr>
                                <tr>
                                    <td><strong>Perihal</strong></td>
                                    <td>:</td>
                                    <td id="detailBelumPerihal"></td>
                                </tr>
                                <tr>
                                    <td><strong>Divisi</strong></td>
                                    <td>:</td>
                                    <td id="detailBelumDivisi"></td>
                                </tr>
                                <tr>
                                    <td><strong>Lama Hari</strong></td>
                                    <td>:</td>
                                    <td id="detailBelumLamaHari"></td>
                                </tr>
                                <tr>
                                    <td><strong>Urgensi</strong></td>
                                    <td>:</td>
                                    <td id="detailBelumUrgensi"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-pdf fa-5x text-danger"></i>
                                    </div>
                                    <h5 class="card-title">File Surat</h5>
                                    <p class="card-text">Lihat atau unduh file surat</p>
                                    <a href="#" id="detailBelumFileLink" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-eye me-2"></i>Lihat File
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" onclick="tindakSuratModal()">Tindak Surat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tindak Surat -->
    <div class="modal fade" id="modalTindakSurat" tabindex="-1" aria-labelledby="modalTindakSuratLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTindakSuratLabel">Tindak Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTindakSurat">
                    @csrf
                    <input type="hidden" id="tindakSuratId" name="surat_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tindakTanggal" class="form-label">Tanggal Tindakan</label>
                            <input type="date" class="form-control" id="tindakTanggal" name="tanggal_tindakan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tindakDeskripsi" class="form-label">Deskripsi Tindakan</label>
                            <textarea class="form-control" id="tindakDeskripsi" name="deskripsi" rows="3" placeholder="Jelaskan tindakan yang dilakukan..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tindakStatus" class="form-label">Status Akhir</label>
                            <select class="form-select" id="tindakStatus" name="status_akhir" required>
                                <option value="">Pilih Status</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditunda">Ditunda</option>
                                <option value="diteruskan">Diteruskan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tindakCatatan" class="form-label">Catatan Tambahan</label>
                            <textarea class="form-control" id="tindakCatatan" name="catatan" rows="2" placeholder="Catatan tambahan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Tindakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
