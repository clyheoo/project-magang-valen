<!-- Surat Masuk Section -->
<section id="surat-masuk" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Surat Masuk</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="input-group me-2" style="width: 300px;">
                <input type="text" class="form-control" id="searchSuratMasuk" placeholder="Cari surat berdasarkan judul laporan, perihal atau pengirim...">
                <button class="btn btn-outline-secondary" type="button" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
            <select class="form-select me-2" id="filterDivisi" style="width: auto;">
                <option value="">Semua Divisi</option>
                <option value="umum">Umum & Kepegawaian</option>
                <option value="keuangan">Keuangan</option>
                <option value="perencanaan">Perencanaan</option>
                <option value="hukum">Hukum & Kerjasama</option>
                <option value="ti">Teknologi Informasi</option>
            </select>
            <select class="form-select me-2" id="filterStatus" style="width: auto;">
                <option value="">Semua Status</option>
                <option value="baru">Baru</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
            </select>
            <button class="btn btn-success me-2" id="btnSuratBaruSection">Surat Baru</button>

            <!-- Modal Surat Baru -->
            <div class="modal fade" id="modalSuratBaruSection" tabindex="-1" aria-labelledby="modalSuratBaruSectionLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalSuratBaruSectionLabel">Tambah Surat Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formSuratBaruSection" class="form-surat-baru" action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="suratBaruNomor" class="form-label">Nomor Surat</label>
                                    <input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" id="suratBaruNomor" name="nomor_surat" value="{{ old('nomor_surat') }}" required>
                                    @error('nomor_surat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruJudul" class="form-label">Judul Laporan</label>
                                    <input type="text" class="form-control @error('judul_laporan') is-invalid @enderror" id="suratBaruJudul" name="judul_laporan" value="{{ old('judul_laporan') }}" required>
                                    @error('judul_laporan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruDivisi" class="form-label">Divisi</label>
                                    <select class="form-select @error('divisi') is-invalid @enderror" id="suratBaruDivisi" name="divisi" required>
                                        <option value="">Pilih Divisi</option>
                                        <option value="umum" {{ old('divisi') == 'umum' ? 'selected' : '' }}>Umum & Kepegawaian</option>
                                        <option value="sekretariat" {{ old('divisi') == 'sekretariat' ? 'selected' : '' }}>Sekretariat</option>
                                        <option value="keuangan" {{ old('divisi') == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                        <option value="akademik" {{ old('divisi') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                        <option value="kemahasiswaan" {{ old('divisi') == 'kemahasiswaan' ? 'selected' : '' }}>Kemahasiswaan</option>
                                        <option value="sarpras" {{ old('divisi') == 'sarpras' ? 'selected' : '' }}>Sarana & Prasarana</option>
                                        <option value="humas" {{ old('divisi') == 'humas' ? 'selected' : '' }}>Humas</option>
                                        <option value="it" {{ old('divisi') == 'it' ? 'selected' : '' }}>IT</option>
                                    </select>
                                    @error('divisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruTanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="suratBaruTanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruPerihal" class="form-label">Perihal</label>
                                    <textarea class="form-control @error('perihal') is-invalid @enderror" id="suratBaruPerihal" name="perihal" rows="3">{{ old('perihal') }}</textarea>
                                    @error('perihal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruPengirim" class="form-label">Pengirim</label>
                                    <input type="text" class="form-control @error('pengirim') is-invalid @enderror" id="suratBaruPengirim" name="pengirim" value="{{ old('pengirim') }}" required>
                                    @error('pengirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruFormat" class="form-label">Format File</label>
                                    <select class="form-select @error('format_file_id') is-invalid @enderror" id="suratBaruFormat" name="format_file_id" required>
                                        <option value="">Pilih Format</option>
                                        <option value="1" {{ old('format_file_id') == 1 ? 'selected' : '' }}>PDF</option>
                                        <option value="2" {{ old('format_file_id') == 2 ? 'selected' : '' }}>DOC</option>
                                        <option value="3" {{ old('format_file_id') == 3 ? 'selected' : '' }}>DOCX</option>
                                        <option value="4" {{ old('format_file_id') == 4 ? 'selected' : '' }}>XLS</option>
                                        <option value="5" {{ old('format_file_id') == 5 ? 'selected' : '' }}>XLSX</option>
                                    </select>
                                    @error('format_file_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="suratBaruFile" class="form-label">File Surat (PDF, DOC, DOCX, XLS, XLSX - max 10MB)</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="suratBaruFile" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" />
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Surat Masuk -->
            <div class="modal fade" id="modalEditSuratMasuk" tabindex="-1" aria-labelledby="modalEditSuratMasukLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditSuratMasukLabel">Edit Surat Masuk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formEditSuratMasuk" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editSuratId" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editSuratNomor" class="form-label">Nomor Surat</label>
                                    <input type="text" class="form-control" id="editSuratNomor" name="nomor_surat" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratJudul" class="form-label">Judul Laporan</label>
                                    <input type="text" class="form-control" id="editSuratJudul" name="judul_laporan" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratDivisi" class="form-label">Divisi</label>
                                    <select class="form-select" id="editSuratDivisi" name="divisi" required>
                                        <option value="">Pilih Divisi</option>
                                        <option value="umum">Umum & Kepegawaian</option>
                                        <option value="keuangan">Keuangan</option>
                                        <option value="perencanaan">Perencanaan</option>
                                        <option value="hukum">Hukum & Kerjasama</option>
                                        <option value="ti">Teknologi Informasi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratTanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="editSuratTanggal" name="tanggal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratPerihal" class="form-label">Perihal</label>
                                    <textarea class="form-control" id="editSuratPerihal" name="perihal" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratPengirim" class="form-label">Pengirim</label>
                                    <input type="text" class="form-control" id="editSuratPengirim" name="pengirim" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratFormat" class="form-label">Format File</label>
                                    <select class="form-select" id="editSuratFormat" name="format_file_id" required>
                                        <option value="">Pilih Format</option>
                                        <option value="1">PDF</option>
                                        <option value="2">DOC</option>
                                        <option value="3">DOCX</option>
                                        <option value="4">XLS</option>
                                        <option value="5">XLSX</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editSuratFile" class="form-label">File Surat (PDF, DOC, DOCX, XLS, XLSX - max 10MB)</label>
                                    <input type="file" class="form-control" id="editSuratFile" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
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
                            <th scope="col">Judul Laporan</th>
                            <th scope="col">Perihal</th>
                            <th scope="col">Divisi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="suratMasukTableBody">
                        @foreach($suratMasuk as $index => $item)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->pengirim ?? '-' }}</td>
                            <td>{{ $item->nomor_surat ?? '-' }}</td>
                            <td>{{ $item->instruksi_disposisi ?? '-' }}</td>
                            <td>{{ $item->divisi->nama_divisi ?? ($item->nama_divisi ?? '-') }}</td>
                            <td><span class="badge bg-success">{{ ucfirst($item->status ?? 'baru') }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('surat-masuk.show', $item->id) }}" class="btn btn-outline-primary" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-secondary" onclick="editSuratMasuk({{ $item->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="hapusSuratMasuk({{ $item->id }}, event)" title="Hapus">
                                        <i class="fas fa-trash"></i>
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
</section>
