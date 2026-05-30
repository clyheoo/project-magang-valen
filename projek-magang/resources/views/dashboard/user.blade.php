@extends('layouts.dashboard')

@section('title', 'Dashboard User')

@section('content')
{{-- ==================== DASHBOARD ==================== --}}
<section id="dashboard" class="dashboard-section active">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3 sidebar-toggle-btn" data-toggle-sidebar><i class="fas fa-bars"></i></button>
            <h1 class="h2 mb-0">Dashboard Overview</h1>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-4">
            <div class="card kpi-card surat-masuk">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Surat Masuk</h6><h3>{{ $suratMasukCount ?? 0 }}</h3></div>
                        <i class="fas fa-envelope fa-2x text-secondary"></i>
                    </div>
                    <span class="badge bg-secondary">Total</span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4">
            <div class="card kpi-card surat-keluar">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Surat Keluar</h6><h3>{{ $suratKeluarCount ?? 0 }}</h3></div>
                        <i class="fas fa-paper-plane fa-2x text-success"></i>
                    </div>
                    <span class="badge bg-success">Total</span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4">
            <div class="card kpi-card belum-ditindak">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Belum Ditindak</h6><h3>{{ $belumDitindakCount ?? 0 }}</h3></div>
                        <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                    </div>
                    <a href="{{ route('belum-ditindak') }}" class="text-decoration-none">
                        <span class="badge bg-warning text-dark">Perlu Perhatian <i class="fas fa-arrow-right ms-1"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Distribusi Surat per Divisi</h5></div>
                <div class="card-body"><div class="chart-container"><canvas id="divisionChartUser"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Status Surat Masuk</h5></div>
                <div class="card-body"><div class="chart-container"><canvas id="statusChartUser"></canvas></div></div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== SURAT MASUK ==================== --}}
<section id="surat-masuk" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3 sidebar-toggle-btn" data-toggle-sidebar><i class="fas fa-bars"></i></button>
            <h1 class="h2">Surat Masuk</h1>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group" style="width:280px">
                <input type="text" class="form-control" id="searchSuratMasuk" placeholder="Cari instruksi..." style="height:38px">
                <button class="btn btn-outline-secondary" id="searchBtnMasuk" style="width:38px"><i class="fas fa-search"></i></button>
            </div>
            <select class="form-select filter-select" id="filterStatus" style="width:150px">
                <option value="">Semua Status</option>
                <option value="baru">Baru</option>
                <option value="ditolak">Ditolak</option>
                <option value="diterima">Diterima</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
            </select>
            <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalSuratBaru">
                <i class="fas fa-plus me-1"></i> Surat Baru
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>#</th><th>Tanggal</th><th>Pengirim</th><th>Instruksi Disposisi</th><th>Instruksi Tambahan</th><th>Divisi</th><th>Format</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody id="suratMasukTableBody">
                        @php $no = 1; @endphp
                        @foreach($suratMasuk as $item)
                        @if($item->created_by == Auth::id() || $item->divisi_id == Auth::user()->divisi_id)
                        <tr data-id="{{ $item->id }}">
                            <th>{{ $no++ }}</th>
                            <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $item->pengirim ?? 'N/A' }}</td>
                            <td class="text-truncate" style="max-width:150px" title="{{ $item->instruksi_disposisi }}">{{ $item->instruksi_disposisi ?: '-' }}</td>
                            <td class="text-truncate" style="max-width:150px" title="{{ $item->instruksi_tambahan }}">{{ $item->instruksi_tambahan ?: '-' }}</td>
                            <td><span class="divisi-tag divisi-{{ $item->divisi_id ?? 0 }}">{{ $item->divisi->nama_divisi ?? $item->nama_divisi ?? 'N/A' }}</span></td>
                            <td><i class="{{ getFileIconClass($item->format_file_id, '', $item->formatFile->nama_format ?? '') }}" title="{{ $item->formatFile->nama_format ?? 'File' }}"></i></td>
                            <td>
                                @switch($item->status)
                                    @case('baru') <span class="badge bg-warning">Baru</span> @break
                                    @case('diterima') <span class="badge bg-success">Diterima</span> @break
                                    @case('ditolak') <span class="badge bg-danger">Ditolak</span> @break
                                    @case('diproses') <span class="badge bg-info">Diproses</span> @break
                                    @case('selesai') <span class="badge bg-primary">Selesai</span> @break
                                    @default <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('surat-masuk.show', $item->id) }}" class="btn btn-outline-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                                    @if($item->created_by == Auth::id())
                                    <button class="btn btn-outline-secondary" onclick="editSuratMasuk({{ $item->id }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- ==================== SURAT KELUAR ==================== --}}
<section id="surat-keluar" class="dashboard-section">
    @include('dashboard.sections.surat-keluar')
</section>
@endsection

@section('modals')
{{-- ===== PROFILE MODAL ===== --}}
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="profile-header text-center">
                <img src="{{ Auth::user()->profile_picture ?: asset('images/default-profile.png') }}" alt="Profile" class="profile-img" id="profileImage">
                <h4 class="mt-2">{{ Auth::user()->name }}</h4>
                <p>{{ ucfirst(Auth::user()->role) }} - {{ Auth::user()->divisi->nama_divisi ?? 'N/A' }}</p>
            </div>
            <div class="modal-body">
                <form id="profileForm">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="fullName" value="{{ Auth::user()->name }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Telepon</label><input type="tel" class="form-control" id="phone" value="{{ Auth::user()->phone }}"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">NIK</label><input type="text" class="form-control" id="sid" value="{{ Auth::user()->sid }}"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" id="department">
                                @foreach($divisi as $d)
                                <option value="{{ $d->id }}" {{ Auth::user()->divisi_id == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Bio</label><textarea class="form-control" id="bio" rows="3">{{ Auth::user()->bio }}</textarea></div>
                    <div class="mb-3"><label class="form-label">Foto Profil</label><input type="file" class="form-control" id="profilePicture" accept="image/*"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveProfile">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== SETTINGS MODAL ===== --}}
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Pengaturan Akun</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="emailNewMail" checked><label class="form-check-label" for="emailNewMail">Terima notifikasi email untuk surat baru</label></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="browserNewMail" checked><label class="form-check-label" for="browserNewMail">Notifikasi browser</label></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveUserSettings">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== SUCCESS MODAL ===== --}}
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:400px">
        <div class="modal-content"><div class="modal-body text-center py-5"><i class="fas fa-check-circle text-success" style="font-size:4rem"></i><h4 class="mt-3" id="successMessage"></h4></div></div>
    </div>
</div>

{{-- ===== ERROR MODAL ===== --}}
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:400px">
        <div class="modal-content"><div class="modal-body text-center py-5"><i class="fas fa-times-circle text-danger" style="font-size:4rem"></i><h4 class="mt-3" id="errorMessage"></h4></div></div>
    </div>
</div>

{{-- ===== SURAT MASUK BARU ===== --}}
<div class="modal fade" id="modalSuratBaru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Surat Masuk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formSuratBaru" action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat" value="{{ old('nomor_surat') }}" required>@error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="mb-3">
                        <label class="form-label">Divisi</label>
                        <select class="form-select @error('divisi_id') is-invalid @enderror" name="divisi_id" required>
                            <option value="" disabled selected>Pilih Divisi</option>
                            @foreach($divisi as $d)
                            <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                            @endforeach
                        </select>
                        @error('divisi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal" value="{{ old('tanggal') }}" required>@error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="mb-3"><label class="form-label">Instruksi Disposisi</label><textarea class="form-control" name="instruksi_disposisi" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Instruksi Tambahan</label><textarea class="form-control" name="instruksi_tambahan" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Pengirim</label><input type="text" class="form-control @error('pengirim') is-invalid @enderror" name="pengirim" value="{{ old('pengirim') }}" required>@error('pengirim')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="mb-3"><label class="form-label">File Surat (PDF, DOCX, XLSX - max 10MB)</label><input type="file" class="form-control @error('file') is-invalid @enderror" name="file" accept=".pdf,.docx,.xlsx">@error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== EDIT SURAT MASUK ===== --}}
<div class="modal fade" id="modalEditSuratMasuk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Surat Masuk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formEditSuratMasuk" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" id="editSuratId" name="id">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" id="editSuratNomor" name="nomor_surat" required></div>
                    <div class="mb-3">
                        <label class="form-label">Divisi</label>
                        <select class="form-select" id="editSuratDivisi" name="divisi_id" required>
                            <option value="" disabled>Pilih Divisi</option>
                            @foreach($divisi as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" class="form-control" id="editSuratTanggal" name="tanggal" required></div>
                    <div class="mb-3"><label class="form-label">Instruksi Disposisi</label><textarea class="form-control" id="edit_instruksi_disposisi" name="instruksi_disposisi" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Instruksi Tambahan</label><textarea class="form-control" id="edit_instruksi_tambahan" name="instruksi_tambahan" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Pengirim</label><input type="text" class="form-control" id="editSuratPengirim" name="pengirim" required></div>
                    <div class="mb-3"><label class="form-label">File Surat</label><input type="file" class="form-control" id="editSuratFile" name="file" accept=".pdf,.docx,.xlsx"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== SURAT KELUAR BARU (LENGKAP) ===== --}}
<div class="modal fade" id="modalSuratKeluarBaru" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Surat Keluar Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSuratKeluarBaru" action="{{ route('surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" name="nomor_surat" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Kirim</label><input type="date" class="form-control" name="tanggal_kirim" required></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" name="divisi_id" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisi as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Penerima</label><input type="text" class="form-control" name="penerima" required></div>
                    <div class="mb-3"><label class="form-label">Judul Laporan</label><input type="text" class="form-control" name="judul_laporan" required></div>
                    <div class="mb-3"><label class="form-label">Perihal</label><textarea class="form-control" name="perihal" rows="3" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Instruksi Disposisi</label><input type="text" class="form-control" name="instruksi_disposisi"></div>
                    <div class="mb-3"><label class="form-label">Instruksi Tambahan</label><input type="text" class="form-control" name="instruksi_tambahan"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="draft">Draft</option>
                                <option value="dikirim" selected>Dikirim</option>
                                <option value="diterima">Diterima</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Lampiran</label><input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" multiple></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== LIHAT SURAT KELUAR ===== --}}
<div class="modal fade" id="modalLihatSuratKeluar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr><td width="30%"><strong>Nomor Surat</strong></td><td width="5%">:</td><td id="detailKeluarNomorSurat"></td></tr>
                            <tr><td><strong>Judul Laporan</strong></td><td>:</td><td id="detailKeluarJudulLaporan"></td></tr>
                            <tr><td><strong>Penerima</strong></td><td>:</td><td id="detailKeluarPenerima"></td></tr>
                            <tr><td><strong>Tanggal Kirim</strong></td><td>:</td><td id="detailKeluarTanggalKirim"></td></tr>
                            <tr><td><strong>Perihal</strong></td><td>:</td><td id="detailKeluarPerihal"></td></tr>
                            <tr><td><strong>Divisi</strong></td><td>:</td><td id="detailKeluarDivisi"></td></tr>
                            <tr><td><strong>Status</strong></td><td>:</td><td id="detailKeluarStatus"></td></tr>
                            <tr><td><strong>Pengirim (User)</strong></td><td>:</td><td id="detailKeluarUser"></td></tr>
                            <tr><td><strong>Format File</strong></td><td>:</td><td id="detailKeluarFormat"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                <div id="detailKeluarFileIcon" class="mb-3"></div>
                                <h5 class="card-title">Lampiran</h5>
                                <a href="#" id="detailKeluarFileLink" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-2" style="display: none;"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== EDIT SURAT KELUAR ===== --}}
<div class="modal fade" id="modalEditSuratKeluar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditSuratKeluar" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" id="editKeluarId" name="id">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" id="editKeluarNomor" name="nomor_surat" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Kirim</label><input type="date" class="form-control" id="editKeluarTanggal" name="tanggal_kirim" required></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" id="editKeluarDivisi" name="divisi_id" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisi as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Penerima</label><input type="text" class="form-control" id="editKeluarPenerima" name="penerima" required></div>
                    <div class="mb-3"><label class="form-label">Judul Laporan</label><input type="text" class="form-control" id="editKeluarJudul" name="judul_laporan" required></div>
                    <div class="mb-3"><label class="form-label">Perihal</label><textarea class="form-control" id="editKeluarPerihal" name="perihal" rows="3" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Instruksi Disposisi</label><input type="text" class="form-control" id="editKeluarInstruksiDisposisi" name="instruksi_disposisi"></div>
                    <div class="mb-3"><label class="form-label">Instruksi Tambahan</label><input type="text" class="form-control" id="editKeluarInstruksiTambahan" name="instruksi_tambahan"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="editKeluarStatus" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diterima">Diterima</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Lampiran (Opsional)</label>
                        <input type="file" class="form-control" id="editKeluarFile" name="file">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah lampiran.</div>
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
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // LIHAT SURAT KELUAR
    // ==========================================
    window.lihatSuratKeluar = function(id) {
        fetch('/surat-keluar/' + id, {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Gagal mengambil data surat.');
            return response.json();
        })
        .then(function(data) {
            if (data.success && data.surat) {
                var surat = data.surat;
                var modal = new bootstrap.Modal(document.getElementById('modalLihatSuratKeluar'));

                document.getElementById('detailKeluarNomorSurat').textContent = surat.nomor_surat || '-';
                document.getElementById('detailKeluarJudulLaporan').textContent = surat.judul_laporan || '-';
                document.getElementById('detailKeluarPenerima').textContent = surat.penerima || '-';
                document.getElementById('detailKeluarTanggalKirim').textContent = surat.tanggal_kirim ? new Date(surat.tanggal_kirim).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
                document.getElementById('detailKeluarPerihal').textContent = surat.perihal || '-';
                document.getElementById('detailKeluarDivisi').textContent = surat.divisi_name || '-';
                document.getElementById('detailKeluarUser').textContent = surat.user_name || '-';
                document.getElementById('detailKeluarFormat').textContent = surat.format_name || '-';

                var statusBadge = document.getElementById('detailKeluarStatus');
                statusBadge.innerHTML = '<span class="badge bg-' + (surat.status_color || 'secondary') + '">' + (surat.status_name || surat.status) + '</span>';

                var fileLink = document.getElementById('detailKeluarFileLink');
                var fileIcon = document.getElementById('detailKeluarFileIcon');
                if (surat.file_path) {
                    fileLink.href = '/storage/' + surat.file_path;
                    fileLink.textContent = 'Lihat ' + (surat.format_name || 'File');
                    fileLink.style.display = 'inline-block';
                    var iconType = 'secondary';
                    var iconName = 'file';
                    if (surat.format_name) {
                        var fn = surat.format_name.toLowerCase();
                        if (fn.includes('pdf')) { iconType = 'danger'; iconName = 'file-pdf'; }
                        else if (fn.includes('word') || fn.includes('doc')) { iconType = 'primary'; iconName = 'file-word'; }
                        else if (fn.includes('excel') || fn.includes('xls')) { iconType = 'success'; iconName = 'file-excel'; }
                    }
                    fileIcon.innerHTML = '<i class="fas fa-' + iconName + ' fa-3x text-' + iconType + '"></i>';
                } else {
                    fileLink.style.display = 'none';
                    fileIcon.innerHTML = '<p class="text-muted">Tidak ada lampiran.</p>';
                }

                modal.show();
            } else {
                alert(data.message || 'Gagal memuat detail surat.');
            }
        })
        .catch(function(error) { console.error('Error:', error); });
    };

    // ==========================================
    // FILTER: SURAT MASUK
    // ==========================================
    function loadSuratMasuk(search, status) {
        var userId = document.querySelector('meta[name="user-id"]').content;
        var userDivisi = document.querySelector('meta[name="user-divisi-id"]').content;
        
        fetch('{{ route("surat-masuk.index") }}?search=' + encodeURIComponent(search || '') + '&status=' + encodeURIComponent(status || ''), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var tbody = document.getElementById('suratMasukTableBody');
            if (!tbody || !data.success) return;
            tbody.innerHTML = '';
            
            var filteredData = data.suratMasuk.filter(function(item) {
                return String(item.created_by) === String(userId) || String(item.divisi_id) === String(userDivisi);
            });
            
            if (filteredData.length) {
                var no = 1;
                filteredData.forEach(function(item) {
                    var tanggal = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';
                    var divisiNama = item.divisi && item.divisi.nama_divisi ? item.divisi.nama_divisi : (item.nama_divisi || 'N/A');
                    var icon = getFileIconClass(item.format_file_id, '', item.format_file && item.format_file.nama_format ? item.format_file.nama_format : '');
                    var statuses = { baru: 'warning', diterima: 'success', ditolak: 'danger', diproses: 'info', selesai: 'primary' };
                    var badge = '<span class="badge bg-' + (statuses[item.status] || 'secondary') + '">' + item.status + '</span>';
                    var canEdit = String(item.created_by) === String(userId);
                    
                    tbody.innerHTML += '<tr data-id="' + item.id + '"><th>' + (no++) + '</th><td>' + tanggal + '</td><td>' + (item.pengirim || 'N/A') + '</td><td class="text-truncate" style="max-width:150px" title="' + (item.instruksi_disposisi || '') + '">' + (item.instruksi_disposisi || '-') + '</td><td class="text-truncate" style="max-width:150px" title="' + (item.instruksi_tambahan || '') + '">' + (item.instruksi_tambahan || '-') + '</td><td><span class="divisi-tag divisi-' + (item.divisi_id || 0) + '">' + divisiNama + '</span></td><td><i class="' + icon + '"></i></td><td>' + badge + '</td><td><div class="btn-group btn-group-sm"><a href="/surat-masuk/' + item.id + '" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>' + (canEdit ? '<button class="btn btn-outline-secondary" onclick="editSuratMasuk(' + item.id + ')"><i class="fas fa-edit"></i></button>' : '') + '</div></td></tr>';
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>';
            }
        })
        .catch(function() {
            var tbody = document.getElementById('suratMasukTableBody');
            if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Gagal memuat data.</td></tr>';
        });
    }

    document.getElementById('searchBtnMasuk').addEventListener('click', function() {
        loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterStatus').value);
    });
    document.getElementById('searchSuratMasuk').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') loadSuratMasuk(e.target.value, document.getElementById('filterStatus').value);
    });
    document.getElementById('filterStatus').addEventListener('change', function() {
        loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterStatus').value);
    });

    // ==========================================
    // FILTER: SURAT KELUAR
    // ==========================================
    function loadSuratKeluar(search, divisi, status) {
        var userId = document.querySelector('meta[name="user-id"]').content;
        
        fetch('{{ route("surat-keluar.index") }}?search=' + encodeURIComponent(search || '') + '&divisi=' + encodeURIComponent(divisi || '') + '&status=' + encodeURIComponent(status || ''), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var list = document.getElementById('suratKeluarList');
            if (!list || !data.success) return;
            list.innerHTML = '';
            
            var filteredData = data.suratKeluar.filter(function(item) {
                return String(item.user_id) === String(userId);
            });
            
            if (filteredData.length) {
                filteredData.forEach(function(item) {
                    var divisiNama = item.divisi && item.divisi.nama_divisi ? item.divisi.nama_divisi : 'N/A';
                    var preview = (item.perihal || '').substring(0, 100);
                    var statuses = { draft: 'bg-draft', dikirim: 'bg-dikirim', diterima: 'bg-diterima' };
                    var badge = '<span class="badge ' + (statuses[item.status] || 'bg-secondary') + '">' + item.status + '</span>';
                    
                    list.innerHTML += '<div class="list-group-item email-item" data-id="' + item.id + '"><div class="d-flex w-100 align-items-center"><div class="flex-grow-1"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1 email-subject">' + (item.penerima || 'N/A') + '</h6><small class="text-muted">' + (item.tanggal_kirim || '') + '</small></div><p class="mb-1 email-preview">' + preview + '</p><div class="email-meta mt-2"><span class="badge bg-light text-dark me-2">' + divisiNama + '</span>' + badge + '</div></div><div class="ms-3 email-actions"><div class="btn-group btn-group-sm"><button class="btn btn-outline-primary" onclick="lihatSuratKeluar(' + item.id + ')"><i class="fas fa-eye"></i></button><button class="btn btn-outline-secondary" onclick="editSuratKeluar(' + item.id + ')"><i class="fas fa-edit"></i></button><button class="btn btn-outline-danger" onclick="hapusSuratKeluar(' + item.id + ', event)"><i class="fas fa-trash"></i></button></div></div></div></div>';
                });
            } else {
                list.innerHTML = '<div class="list-group-item text-center p-5">Tidak ada data.</div>';
            }
        });
    }

    document.getElementById('btnSearchSuratKeluar').addEventListener('click', function() {
        loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterDivisiKeluar').value, document.getElementById('filterStatusKeluar').value);
    });
    document.getElementById('searchSuratKeluar').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') loadSuratKeluar(e.target.value, document.getElementById('filterDivisiKeluar').value, document.getElementById('filterStatusKeluar').value);
    });
    document.getElementById('filterDivisiKeluar').addEventListener('change', function() {
        loadSuratKeluar(document.getElementById('searchSuratKeluar').value, this.value, document.getElementById('filterStatusKeluar').value);
    });
    document.getElementById('filterStatusKeluar').addEventListener('change', function() {
        loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterDivisiKeluar').value, this.value);
    });

    // ==========================================
    // CHARTS
    // ==========================================
    try {
        var ctxDivision = document.getElementById('divisionChartUser');
        var divisionData = @json($divisionChartData ?? []);

        if (ctxDivision && divisionData && divisionData.length > 0) {
            new Chart(ctxDivision, {
                type: 'bar',
                data: {
                    labels: divisionData.map(function(d) { return d.name; }),
                    datasets: [
                        {
                            label: 'Surat Masuk',
                            data: divisionData.map(function(d) { return d.surat_masuk; }),
                            backgroundColor: 'rgba(52, 152, 219, 0.7)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Surat Keluar',
                            data: divisionData.map(function(d) { return d.surat_keluar; }),
                            backgroundColor: 'rgba(39, 174, 96, 0.7)',
                            borderColor: 'rgba(39, 174, 96, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        var ctxStatus = document.getElementById('statusChartUser');
        var statusData = @json($statusChartData ?? []);

        if (ctxStatus && statusData && Object.keys(statusData).length > 0) {
            var statusColors = {
                'baru': 'rgba(243, 156, 18, 0.7)',
                'diterima': 'rgba(39, 174, 96, 0.7)',
                'ditolak': 'rgba(231, 76, 60, 0.7)',
                'diproses': 'rgba(52, 152, 219, 0.7)',
                'selesai': 'rgba(155, 89, 182, 0.7)'
            };

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: Object.keys(statusData).map(function(k) {
                            return statusColors[k] || 'rgba(149, 165, 166, 0.7)';
                        })
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    } catch (chartError) {
        console.warn('Chart initialization skipped:', chartError.message);
    }

});
</script>