@extends('layouts.dashboard')

@section('title', 'Dashboard User')

@section('content')
{{-- ==================== DASHBOARD ==================== --}}
<section id="dashboard" class="dashboard-section active">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
                            <td><i class="{{ getFileIconClass($item->format_file_id, 'fa-lg', $item->formatFile->nama_format ?? '') }}" title="{{ $item->formatFile->nama_format ?? 'File' }}"></i></td>
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

{{-- ===== SETTINGS MODAL (Simplified for User) ===== --}}
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

{{-- ===== SURAT KELUAR BARU ===== --}}
<div class="modal fade" id="modalSuratKeluarBaru" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Buat Surat Keluar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formSuratKeluarBaru" action="{{ route('surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" name="nomor_surat" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Kirim</label><input type="date" class="form-control" name="tanggal_kirim" required></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="draft">Draft</option>
                                <option value="dikirim" selected>Dikirim</option>
                                <option value="diterima">Diterima</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Kepada</label><input type="text" class="form-control" name="penerima" placeholder="Nama instansi/penerima" required></div>
                    <div class="mb-3"><label class="form-label">Perihal</label><textarea class="form-control" name="perihal" rows="4" required></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" multiple>
                        <div class="form-text">PDF, DOCX, XLSX - max 10MB per file</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter: Surat Masuk (User hanya bisa lihat miliknya/divisinya)
    function loadSuratMasuk(search, status) {
        const userId = document.querySelector('meta[name="user-id"]').content;
        const userDivisi = document.querySelector('meta[name="user-divisi-id"]').content;
        
        fetch(`{{ route('surat-masuk.index') }}?search=${encodeURIComponent(search||'')}&status=${encodeURIComponent(status||'')}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('suratMasukTableBody');
            if (!tbody || !data.success) return;
            tbody.innerHTML = '';
            
            let filteredData = data.suratMasuk.filter(item => 
                String(item.created_by) === String(userId) || String(item.divisi_id) === String(userDivisi)
            );
            
            if (filteredData.length) {
                let no = 1;
                filteredData.forEach(item => {
                    const tanggal = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';
                    const divisiNama = item.divisi?.nama_divisi || item.nama_divisi || 'N/A';
                    const icon = getFileIconClass(item.format_file_id, 'fa-lg', item.format_file?.nama_format || '');
                    const statuses = { baru: 'warning', diterima: 'success', ditolak: 'danger', diproses: 'info', selesai: 'primary' };
                    const badge = `<span class="badge bg-${statuses[item.status] || 'secondary'}">${item.status}</span>`;
                    const canEdit = String(item.created_by) === String(userId);
                    
                    tbody.innerHTML += `<tr data-id="${item.id}"><th>${no++}</th><td>${tanggal}</td><td>${item.pengirim||'N/A'}</td><td class="text-truncate" style="max-width:150px" title="${item.instruksi_disposisi||''}">${item.instruksi_disposisi||'-'}</td><td class="text-truncate" style="max-width:150px" title="${item.instruksi_tambahan||''}">${item.instruksi_tambahan||'-'}</td><td><span class="divisi-tag divisi-${item.divisi_id||0}">${divisiNama}</span></td><td><i class="${icon}"></i></td><td>${badge}</td><td><div class="btn-group btn-group-sm"><a href="/surat-masuk/${item.id}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>${canEdit ? `<button class="btn btn-outline-secondary" onclick="editSuratMasuk(${item.id})"><i class="fas fa-edit"></i></button>` : ''}</div></td></tr>`;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>';
            }
        }).catch(() => { document.getElementById('suratMasukTableBody').innerHTML = '<tr><td colspan="9" class="text-center text-danger">Gagal memuat data.</td></tr>'; });
    }

    document.getElementById('searchBtnMasuk')?.addEventListener('click', () => loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterStatus').value));
    document.getElementById('searchSuratMasuk')?.addEventListener('keyup', e => { if (e.key === 'Enter') loadSuratMasuk(e.target.value, document.getElementById('filterStatus').value); });
    document.getElementById('filterStatus')?.addEventListener('change', () => loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterStatus').value));

    // Filter: Surat Keluar (User hanya bisa lihat miliknya)
    function loadSuratKeluar(search, status) {
        const userId = document.querySelector('meta[name="user-id"]').content;
        
        fetch(`{{ route('surat-keluar.index') }}?search=${encodeURIComponent(search||'')}&status=${encodeURIComponent(status||'')}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('suratKeluarList');
            if (!list || !data.success) return;
            list.innerHTML = '';
            
            let filteredData = data.suratKeluar.filter(item => String(item.user_id) === String(userId));
            
            if (filteredData.length) {
                filteredData.forEach(item => {
                    const divisiNama = item.divisi?.nama_divisi || 'N/A';
                    const preview = (item.perihal || '').substring(0, 100);
                    const statuses = { draft: 'bg-draft', dikirim: 'bg-dikirim', diterima: 'bg-diterima' };
                    const badge = `<span class="badge ${statuses[item.status] || 'bg-secondary'}">${item.status}</span>`;
                    
                    list.innerHTML += `<div class="list-group-item email-item" data-id="${item.id}"><div class="d-flex w-100 align-items-center"><div class="flex-grow-1"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1 email-subject">${item.penerima||'N/A'}</h6><small class="text-muted">${item.tanggal_kirim||''}</small></div><p class="mb-1 email-preview">${preview}</p><div class="email-meta mt-2"><span class="badge bg-light text-dark me-2">${divisiNama}</span>${badge}</div></div><div class="ms-3"><div class="btn-group btn-group-sm"><a href="/surat-keluar/${item.id}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a><button class="btn btn-outline-secondary" onclick="editSuratKeluar(${item.id})"><i class="fas fa-edit"></i></button><button class="btn btn-outline-danger" onclick="hapusSuratKeluar(${item.id},event)"><i class="fas fa-trash"></i></button></div></div></div></div>`;
                });
            } else {
                list.innerHTML = '<div class="list-group-item text-center p-5">Tidak ada data.</div>';
            }
        });
    }

    document.getElementById('searchSuratKeluar')?.addEventListener('keyup', () => loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterStatusKeluar')?.value));
    document.getElementById('filterStatusKeluar')?.addEventListener('change', () => loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterStatusKeluar').value));
});
@endpush