@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
{{-- ==================== DASHBOARD ==================== --}}
<section id="dashboard" class="dashboard-section active">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <h1 class="h2 mb-0">Dashboard Overview</h1>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card surat-masuk">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Surat Masuk</h6>
                            <h3>{{ $suratMasukCount ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-envelope fa-2x text-secondary"></i>
                    </div>
                    <span class="badge bg-secondary">Total</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card surat-keluar">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Surat Keluar</h6>
                            <h3>{{ $suratKeluarCount ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-paper-plane fa-2x text-success"></i>
                    </div>
                    <span class="badge bg-success">Total</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card belum-ditindak">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Belum Ditindak</h6>
                            <h3>{{ $belumDitindakCount ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                    </div>
                    <span class="badge bg-warning text-dark">Perlu Perhatian</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card ratarata-waktu">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Rata-rata Waktu</h6>
                            <h3>{{ $avgWaktuCount ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-hourglass-half fa-2x text-danger"></i>
                    </div>
                    <span class="badge bg-danger">Hari</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional KPI -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Format</h6>
                            <h3>{{ $arsip->whereIn('format_id', [1,2,3,4,5])->count() }}</h3>
                        </div>
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                    <div class="mt-2 d-flex gap-2">
                        <span class="badge bg-danger"><i class="fas fa-file-pdf"></i> {{ $arsip->where('format_id', 1)->count() }}</span>
                        <span class="badge bg-primary"><i class="fas fa-file-word"></i> {{ $arsip->where('format_id', 3)->count() }}</span>
                        <span class="badge bg-success"><i class="fas fa-file-excel"></i> {{ $arsip->where('format_id', 5)->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Arsip</h6>
                            <h3>{{ $arsip->count() }}</h3>
                        </div>
                        <i class="fas fa-archive fa-2x text-info"></i>
                    </div>
                    <span class="badge bg-info">File</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Pengguna</h6>
                            <h3>{{ $totalUsersCount ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <span class="badge bg-primary">Aktif</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Efisiensi Sistem</h6>
                            <h3>{{ $efisiensiCount ?? 0 }}%</h3>
                        </div>
                        <i class="fas fa-chart-line fa-2x text-success"></i>
                    </div>
                    <span class="badge bg-success">Kinerja</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="card-title mb-0">Distribusi Surat per Divisi</h5></div>
                <div class="card-body"><div class="chart-container"><canvas id="divisionChart"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="card-title mb-0">Status Surat Masuk</h5></div>
                <div class="card-body"><div class="chart-container"><canvas id="statusChart"></canvas></div></div>
            </div>
        </div>
    </div>

    <!-- Activities & Summary -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="card-title mb-0">Aktivitas Terbaru</h5></div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $activity)
                        <div class="list-group-item d-flex align-items-start">
                            <div class="me-3"><i class="fas {{ $activity->icon }} text-{{ $activity->color }}"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold d-flex align-items-center gap-2">
                                    {{ $activity->title }}
                                    <span class="badge bg-{{ $activity->color }} text-uppercase" style="font-size:0.6rem">{{ $activity->type }}</span>
                                </div>
                                <small class="text-muted">Oleh {{ $activity->user ?? 'Sistem' }} • {{ $activity->date->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-muted p-3">Tidak ada aktivitas terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="card-title mb-0">Ringkasan Sistem</h5></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6"><h4>{{ $totalDocuments ?? 0 }}</h4><small class="text-muted">Total Dokumen</small></div>
                        <div class="col-6"><h4>{{ $activeUsers ?? 0 }}</h4><small class="text-muted">Pengguna Aktif</small></div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6"><h4>{{ $processedToday ?? 0 }}</h4><small class="text-muted">Diproses Hari Ini</small></div>
                        <div class="col-6"><h4>{{ $pendingItems ?? 0 }}</h4><small class="text-muted">Menunggu Proses</small></div>
                    </div>
                </div>
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
            <select class="form-select filter-select" id="filterDivisi" style="width:160px">
                <option value="">Semua Divisi</option>
                @foreach($divisi as $d)
                <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                @endforeach
            </select>
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
                        <tr>
                            <th>#</th>
                            <th>Tanggal Terima</th>
                            <th>Pengirim</th>
                            <th>Instruksi Disposisi</th>
                            <th>Instruksi Tambahan</th>
                            <th>Divisi</th>
                            <th>Format</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="suratMasukTableBody">
                        @php $no = 1; @endphp
                        @foreach($suratMasuk as $item)
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
                                    <button class="btn btn-outline-primary" onclick="lihatDetailSuratMasuk({{ $item->id }})" title="Lihat"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-outline-secondary" onclick="editSuratMasuk({{ $item->id }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-danger" onclick="hapusSuratMasuk({{ $item->id }}, event)" title="Hapus"><i class="fas fa-trash"></i></button>
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

{{-- ==================== SURAT KELUAR ==================== --}}
<section id="surat-keluar" class="dashboard-section">
    @include('dashboard.sections.surat-keluar')
</section>

{{-- ==================== BEBAN KERJA ==================== --}}
<section id="beban-kerja" class="dashboard-section">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <h1 class="h2">Analisis Beban Kerja</h1>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card surat-masuk">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Surat Masuk</h6><h3>{{ $suratMasukCount ?? 0 }}</h3></div>
                        <i class="fas fa-envelope fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card surat-keluar">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Surat Keluar</h6><h3>{{ $suratKeluarCount ?? 0 }}</h3></div>
                        <i class="fas fa-paper-plane fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card belum-ditindak">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Belum Ditindak</h6><h3>{{ $belumDitindakCount ?? 0 }}</h3></div>
                        <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card ratarata-waktu">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted">Rata-rata Waktu</h6><h3>{{ $avgWaktuCount ?? 0 }} hari</h3></div>
                        <i class="fas fa-hourglass-half fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Grafik Beban Kerja per Divisi</h5></div>
                <div class="card-body"><div class="chart-container" style="height:320px"><canvas id="workloadAnalysisChart"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Status Beban Kerja</h5></div>
                <div class="card-body d-flex align-items-center justify-content-center"><div class="chart-container" style="height:320px"><canvas id="workloadStatusChart"></canvas></div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white"><h5 class="mb-0">Detail Beban Kerja per Divisi</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Divisi</th><th>Total Surat</th><th>Surat Masuk</th><th>Surat Keluar</th><th>Belum Ditindak</th><th>Rata-rata Waktu</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($workloadData as $item)
                        <tr>
                            <td><span class="divisi-tag divisi-{{ $item->divisi_code }}">{{ $item->divisi_name }}</span></td>
                            <td>{{ $item->total_surat }}</td>
                            <td>{{ $item->surat_masuk }}</td>
                            <td>{{ $item->surat_keluar }}</td>
                            <td>{{ $item->belum_ditindak }}</td>
                            <td>{{ $item->avg_waktu }} hari</td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ $item->status_name }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- ==================== ARSIP ==================== --}}
<section id="arsip" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <h1 class="h2">Arsip Digital</h1>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group" style="width:280px">
                <input type="text" class="form-control" id="searchArsip" placeholder="Cari perihal/keterangan..." style="height:38px" value="{{ request('searchArsip') }}">
                <button class="btn btn-outline-secondary" id="btnSearchArsip" style="width:38px"><i class="fas fa-search"></i></button>
            </div>
            <select class="form-select filter-select" id="filterTahunArsip" style="width:130px">
                <option value="">Semua Tahun</option>
                @foreach([2025,2024,2023] as $year)
                <option value="{{ $year }}" {{ request('filterTahunArsip') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <select class="form-select filter-select" id="filterJenisArsip" style="width:150px">
                <option value="">Semua Jenis</option>
                <option value="surat-masuk" {{ request('filterJenisArsip') == 'surat-masuk' ? 'selected' : '' }}>Surat Masuk</option>
                <option value="surat-keluar" {{ request('filterJenisArsip') == 'surat-keluar' ? 'selected' : '' }}>Surat Keluar</option>
            </select>
            <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#uploadArsipModal">
                <i class="fas fa-upload me-1"></i> Upload
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><i class="fas fa-folder fa-3x text-primary mb-3"></i><h5>Total Arsip</h5><h3>{{ $arsip->count() }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><i class="fas fa-file-pdf fa-3x text-danger mb-3"></i><h5>PDF</h5><h3>{{ $arsip->where('format_id', 1)->count() }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><i class="fas fa-file-word fa-3x text-info mb-3"></i><h5>Word</h5><h3>{{ $arsip->where('format_id', 3)->count() }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><i class="fas fa-file-excel fa-3x text-success mb-3"></i><h5>Excel</h5><h3>{{ $arsip->where('format_id', 5)->count() }}</h3></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>No</th><th>Nomor Surat</th><th>Tanggal</th><th>Perihal</th><th>Keterangan</th><th>Jenis</th><th>Format</th><th>Ukuran</th><th>Aksi</th></tr>
                    </thead>
                    <tbody id="arsipTableBody">
                        @foreach($arsip as $index => $item)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $item->nomor_surat }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->perihal }}</td>
                            <td class="text-truncate" style="max-width:150px" title="{{ $item->keterangan }}">{{ $item->keterangan ?: '-' }}</td>
                            <td><span class="badge bg-{{ $item->jenis_color ?? 'secondary' }}">{{ $item->jenis }}</span></td>
                            <td><i class="{{ getFileIconClass($item->format_id, 'fa-lg', $item->format ?? '') }}" title="{{ $item->format ?? 'File' }}"></i></td>
                            <td>{{ $item->ukuran }}</td>
                            <td>
                                @if($item->file_path)
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="btn btn-outline-primary {{ $item->format_id != 1 ? 'disabled' : '' }}"><i class="fas fa-eye"></i></a>
                                    <a href="{{ asset('storage/'.$item->file_path) }}" download class="btn btn-outline-secondary"><i class="fas fa-download"></i></a>
                                </div>
                                @else
                                <span class="text-muted">No file</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- ==================== LAPORAN ==================== --}}
<section id="laporan" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <h1 class="h2">Laporan & Statistik</h1>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#uploadLaporanModal"><i class="fas fa-upload me-1"></i> Upload Laporan</button>
            <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#inputLaporanModal"><i class="fas fa-plus me-1"></i> Input Manual</button>
            <button class="btn btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#exportModal"><i class="fas fa-download me-1"></i> Ekspor</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted">Total Surat Masuk</h6><h3>{{ $totalSuratMasuk ?? 0 }}</h3></div>
                    <i class="fas fa-envelope fa-2x text-primary"></i>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted">Total Surat Keluar</h6><h3>{{ $totalSuratKeluar ?? 0 }}</h3></div>
                    <i class="fas fa-paper-plane fa-2x text-success"></i>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted">Efisiensi Proses</h6><h3>{{ $efisiensiProses ?? 0 }}%</h3></div>
                    <i class="fas fa-chart-line fa-2x text-info"></i>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted">Total Laporan</h6><h3>{{ $totalLaporanCount ?? 0 }}</h3></div>
                    <i class="fas fa-file-alt fa-2x text-warning"></i>
                </div>
            </div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card"><div class="card-header bg-white"><h5 class="mb-0">Trend Surat Bulanan</h5></div><div class="card-body"><div class="chart-container"><canvas id="monthlyTrendChart"></canvas></div></div></div>
        </div>
        <div class="col-lg-6">
            <div class="card"><div class="card-header bg-white"><h5 class="mb-0">Distribusi per Divisi</h5></div><div class="card-body"><div class="chart-container"><canvas id="divisionDistributionChart"></canvas></div></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white"><h5 class="mb-0">Ringkasan Kinerja</h5></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-6"><h4>{{ $avgProcessingTime ?? 0 }}</h4><small class="text-muted">Waktu Proses Rata-rata</small></div>
                <div class="col-6"><h4>{{ $onTimePercentage ?? 0 }}%</h4><small class="text-muted">Tepat Waktu</small></div>
            </div>
            <hr>
            <div class="row text-center">
                <div class="col-6"><h4>{{ $mostActiveDivision ?? 'N/A' }}</h4><small class="text-muted">Divisi Teraktif</small></div>
                <div class="col-6"><h4>{{ $totalFilesProcessed ?? 0 }}</h4><small class="text-muted">File Diproses</small></div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== PENGGUNA ==================== --}}
<section id="pengguna" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <h1 class="h2">Manajemen Pengguna</h1>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#penggunaModal"><i class="fas fa-plus me-1"></i> Tambah</button>
            <button class="btn btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#importPenggunaModal"><i class="fas fa-file-import me-1"></i> Import CSV</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>#</th><th>Nama</th><th>Email</th><th>Divisi</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <th>{{ $index + 1 }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->divisi->nama_divisi ?? 'N/A' }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td><span class="badge bg-success">Aktif</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-edit-pengguna" data-user-id="{{ $user->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-danger btn-hapus-pengguna" data-user-id="{{ $user->id }}"><i class="fas fa-trash"></i></button>
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

{{-- ==================== DETAIL SURAT ==================== --}}
<section id="suratDetailSection" class="dashboard-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3">Detail Surat Masuk</h2>
        <button class="btn btn-outline-secondary" onclick="document.querySelector('[data-section=surat-masuk]').click()">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </button>
    </div>
    
    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Informasi Surat</h4>
                <span id="detailStatusBadge" class="badge"></span>
            </div>
        </div>
        <input type="hidden" id="currentSuratId" value="">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><td width="30%"><strong>Nomor Surat</strong></td><td width="5%">:</td><td id="detailNomorSurat" class="fw-bold"></td></tr>
                        <tr><td><strong>Tanggal Diterima</strong></td><td>:</td><td id="detailTanggal"></td></tr>
                        <tr><td><strong>Pengirim</strong></td><td>:</td><td id="detailPengirim"></td></tr>
                        <tr><td><strong>Perihal</strong></td><td>:</td><td id="detailPerihal"></td></tr>
                        <tr><td><strong>Divisi</strong></td><td>:</td><td id="detailDivisi"></td></tr>
                        <tr><td><strong>Status</strong></td><td>:</td><td id="detailStatus"></td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <div class="mb-3" id="detailFileIconContainer"><i class="fas fa-file fa-7x text-secondary"></i></div>
                            <h5>Dokumen Surat</h5>
                            <a href="#" id="detailFileLink" target="_blank" class="btn btn-primary mt-2"><i class="fas fa-eye me-2"></i>Lihat File</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
            <div class="mt-4 p-3 border rounded bg-light" id="updateStatusContainer">
                <label class="form-label fw-bold">Update Status</label>
                <div class="d-flex gap-2 mb-3">
                    <select class="form-select form-select-sm" id="newStatusSelect" style="width: auto;">
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateStatus()">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
                <textarea class="form-control form-control-sm" id="statusKeterangan" rows="2" placeholder="Masukkan keterangan/catatan tindakan..."></textarea>
            </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header bg-white"><h5 class="mb-0">Riwayat Proses</h5></div>
        <div class="card-body">
            <ul class="timeline">
                <li class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Surat Diterima</h6>
                        <p class="timeline-text">Surat telah diterima dan dicatat dalam sistem</p>
                        <small class="text-muted" id="detailTanggalRiwayat"></small>
                    </div>
                </li>
            </ul>
        </div>
    </div>
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
                <p>{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            <div class="modal-body">
                <form id="profileForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="fullName" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="tel" class="form-control" id="phone" value="{{ Auth::user()->phone }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" id="sid" value="{{ Auth::user()->sid }}">
                        </div>
                        @if(Auth::user()->role !== 'admin')
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" id="department">
                                @foreach($divisi as $d)
                                <option value="{{ $d->id }}" {{ Auth::user()->divisi_id == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" rows="3">{{ Auth::user()->bio }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" class="form-control" id="profilePicture" accept="image/*">
                    </div>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengaturan Sistem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header bg-white"><h5 class="mb-0">Pengaturan Umum</h5></div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Sistem</label>
                                        <input type="text" class="form-control" id="systemName" value="{{ config('app.name', 'Sistem Arsip Digital') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bahasa Default</label>
                                        <select class="form-select" id="defaultLanguage">
                                            <option value="id" {{ config('app.locale') == 'id' ? 'selected' : '' }}>Indonesia</option>
                                            <option value="en" {{ config('app.locale') == 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Timezone</label>
                                        <select class="form-select" id="timezone">
                                            <option value="Asia/Jakarta" {{ config('app.timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>WIB</option>
                                            <option value="Asia/Makassar" {{ config('app.timezone') == 'Asia/Makassar' ? 'selected' : '' }}>WITA</option>
                                            <option value="Asia/Jayapura" {{ config('app.timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>WIT</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ukuran File Maksimal (MB)</label>
                                        <input type="number" class="form-control" id="maxFileSize" value="{{ config('filesystems.max_file_size', 10) }}">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" id="saveGeneralSettings">Simpan</button>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-white"><h5 class="mb-0">Notifikasi</h5></div>
                            <div class="card-body">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="emailNewMail" checked><label class="form-check-label" for="emailNewMail">Surat masuk baru</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="emailOverdue" checked><label class="form-check-label" for="emailOverdue">Surat melewati batas waktu</label></div>
                                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="browserNewMail" checked><label class="form-check-label" for="browserNewMail">Notifikasi browser</label></div>
                                <button type="button" class="btn btn-primary" id="saveNotificationSettings">Simpan</button>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-white"><h5 class="mb-0">Keamanan</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Timeout Sesi (menit)</label>
                                    <input type="number" class="form-control" id="sessionTimeout" value="{{ config('session.lifetime') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kebijakan Password</label>
                                    <select class="form-select" id="passwordPolicy">
                                        <option value="medium">Sedang (8 karakter)</option>
                                        <option value="strong">Kuat (12 karakter)</option>
                                    </select>
                                </div>
                                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="twoFactorAuth"><label class="form-check-label" for="twoFactorAuth">Two-Factor Authentication</label></div>
                                <button type="button" class="btn btn-primary" id="saveSecuritySettings">Simpan</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white"><h5 class="mb-0">Info Sistem</h5></div>
                            <div class="card-body">
                                <div class="mb-2"><strong>Versi:</strong> {{ config('app.version', '2.1.0') }}</div>
                                <div class="mb-2"><strong>Database:</strong> MySQL</div>
                                <div class="mb-2"><strong>PHP:</strong> {{ phpversion() }}</div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header bg-white"><h5 class="mb-0">Backup</h5></div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-sm" id="createBackup">Buat Backup</button>
                                    <button type="button" class="btn btn-warning btn-sm" id="restoreBackup">Restore</button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-white"><h5 class="mb-0">Pemeliharaan</h5></div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-info btn-sm" id="clearCache">Bersihkan Cache</button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="optimizeDatabase">Optimalkan DB</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
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
                    <div class="mb-3">
                        <label class="form-label">Nomor Surat</label>
                        <input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat" value="{{ old('nomor_surat') }}" required>
                        @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
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
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal" value="{{ old('tanggal') }}" required>
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instruksi Disposisi</label>
                        <textarea class="form-control" name="instruksi_disposisi" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instruksi Tambahan</label>
                        <textarea class="form-control" name="instruksi_tambahan" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pengirim</label>
                        <input type="text" class="form-control @error('pengirim') is-invalid @enderror" name="pengirim" value="{{ old('pengirim') }}" required>
                        @error('pengirim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Surat (PDF, DOCX, XLSX - max 10MB)</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" accept=".pdf,.docx,.xlsx">
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <label class="form-label">Divisi</label>
                            <select class="form-select" name="divisi_id" required>
                                <option value="" disabled selected>Pilih Divisi</option>
                                @foreach($divisi as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach
                            </select>
                        </div>
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

{{-- ===== UPLOAD ARSIP ===== --}}
<div class="modal fade" id="uploadArsipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Upload Arsip Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="uploadArsipForm" action="{{ route('arsip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" name="nomor_surat" required></div>
                    <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" required></div>
                    <div class="mb-3"><label class="form-label">Perihal</label><textarea class="form-control" name="perihal" rows="3" required></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Dokumen</label>
                        <select class="form-select" name="jenis_dokumen_id" required>
                            <option value="">Pilih Jenis</option>
                            @php $jenisDokumenList = \App\Models\JenisDokumen::all(); @endphp
                            @foreach($jenisDokumenList as $jenis)<option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Keterangan</label><textarea class="form-control" name="keterangan" rows="2" maxlength="255"></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select class="form-select" name="format_file_id" required>
                            <option value="1">PDF</option><option value="2">DOCX</option><option value="3">XLSX</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">File (max 10MB)</label><input type="file" class="form-control" name="file" accept=".pdf,.docx,.xlsx" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== EXPORT ===== --}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Ekspor Laporan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Format</label>
                    <div class="export-option" onclick="this.querySelector('input').checked=true">
                        <div class="form-check"><input class="form-check-input" type="radio" name="exportFormat" value="pdf" checked><label class="form-check-label"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</label></div>
                    </div>
                    <div class="export-option" onclick="this.querySelector('input').checked=true">
                        <div class="form-check"><input class="form-check-input" type="radio" name="exportFormat" value="excel"><label class="form-check-label"><i class="fas fa-file-excel text-success me-2"></i>Excel</label></div>
                    </div>
                    <div class="export-option" onclick="this.querySelector('input').checked=true">
                        <div class="form-check"><input class="form-check-input" type="radio" name="exportFormat" value="csv"><label class="form-check-label"><i class="fas fa-file-csv text-primary me-2"></i>CSV</label></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Periode</label>
                    <select class="form-select" id="exportPeriod">
                        <option value="bulan">Bulan Ini</option>
                        <option value="triwulan">Triwulan Ini</option>
                        <option value="tahun">Tahun Ini</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div class="mb-3" id="exportDateRange" style="display:none">
                    <div class="row">
                        <div class="col-6"><input type="date" class="form-control" id="exportStartDate"></div>
                        <div class="col-6"><input type="date" class="form-control" id="exportEndDate"></div>
                    </div>
                </div>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="exportSuratMasuk" checked><label class="form-check-label" for="exportSuratMasuk">Surat Masuk</label></div>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="exportSuratKeluar" checked><label class="form-check-label" for="exportSuratKeluar">Surat Keluar</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" id="exportCharts"><label class="form-check-label" for="exportCharts">Grafik & Statistik</label></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmExport"><i class="fas fa-download me-1"></i> Ekspor</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== PENGGUNA FORM ===== --}}
<div class="modal fade" id="penggunaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="penggunaForm" action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="penggunaId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Username</label><input type="text" class="form-control" id="penggunaUsername" name="username" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="penggunaFullName" name="full_name" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" id="penggunaEmail" name="email" required></div>
                        <div class="col-md-6 mb-3" id="penggunaDeptContainer">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" id="penggunaDepartment" name="divisi_id">
                                <option value="">Pilih Divisi</option>
                                @foreach($divisi as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="penggunaRole" name="role" required>
                                <option value="user">User</option><option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control pe-5" id="penggunaPassword" name="password">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePasswordVisibility('penggunaPassword')" style="cursor:pointer"><i class="fas fa-eye" id="toggleIcon-penggunaPassword"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== EDIT PENGGUNA ===== --}}
<div class="modal fade" id="editPenggunaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editPenggunaForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editPenggunaId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Username</label><input type="text" class="form-control" id="editUsername" name="username" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="editFullName" name="full_name" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" id="editEmail" name="email" required></div>
                        <div class="col-md-6 mb-3" id="editDivisiContainer">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" id="editDivisi" name="divisi_id">
                                <option value="">Pilih Divisi</option>
                                @foreach($divisi as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="user">User</option><option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Password Baru</label><input type="password" class="form-control" id="editPassword" name="password" placeholder="Kosongkan jika tidak diubah"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== IMPORT PENGGUNA ===== --}}
<div class="modal fade" id="importPenggunaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Import Pengguna dari CSV</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="importPenggunaForm" action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File CSV</label>
                        <input type="file" class="form-control" id="importPenggunaFile" name="csv_file" accept=".csv" required>
                        <div class="form-text">Kolom: username, full_name, email, department, role, password (opsional)</div>
                    </div>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="skipDuplicates" name="skip_duplicates" checked><label class="form-check-label" for="skipDuplicates">Lewati duplikat</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== UPLOAD LAPORAN ===== --}}
<div class="modal fade" id="uploadLaporanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Upload Laporan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="uploadLaporanForm" action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Judul Laporan</label><input type="text" class="form-control" name="title" required maxlength="255"></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi</label>
                            <select class="form-select" name="divisi" required>
                                <option value="">Pilih Divisi</option>
                                <option value="umum">Umum & Kepegawaian</option>
                                <option value="keuangan">Keuangan</option>
                                <option value="perencanaan">Perencanaan</option>
                                <option value="hukum">Hukum & Kerjasama</option>
                                <option value="ti">Teknologi Informasi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">File (PDF, XLSX, CSV - max 10MB)</label>
                            <input type="file" class="form-control" name="file" accept=".pdf,.xlsx,.csv">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== INPUT LAPORAN MANUAL ===== --}}
<div class="modal fade" id="inputLaporanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Input Laporan Manual</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="inputLaporanForm" action="{{ route('laporan.storeManual') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Judul Laporan</label><input type="text" class="form-control" name="title" required maxlength="255"></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Divisi</label>
                        <select class="form-select" name="divisi" required>
                            <option value="">Pilih Divisi</option>
                            <option value="umum">Umum & Kepegawaian</option>
                            <option value="keuangan">Keuangan</option>
                            <option value="perencanaan">Perencanaan</option>
                            <option value="hukum">Hukum & Kerjasama</option>
                            <option value="ti">Teknologi Informasi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle department visibility based on role
    function toggleDeptVisibility(roleSelect, deptContainer) {
        if (!roleSelect || !deptContainer) return;
        deptContainer.style.display = roleSelect.value === 'admin' ? 'none' : 'block';
    }
    
    const penggunaRole = document.getElementById('penggunaRole');
    if (penggunaRole) penggunaRole.addEventListener('change', () => toggleDeptVisibility(penggunaRole, document.getElementById('penggunaDeptContainer')));
    
    const editRole = document.getElementById('editRole');
    if (editRole) editRole.addEventListener('change', () => toggleDeptVisibility(editRole, document.getElementById('editDivisiContainer')));

    // Export period toggle
    document.getElementById('exportPeriod')?.addEventListener('change', function() {
        document.getElementById('exportDateRange').style.display = this.value === 'custom' ? 'block' : 'none';
    });

    // ==========================================
    // 1. CHART DASHBOARD UTAMA (Distribusi & Status)
    // ==========================================
    const chartDivisionData = @json($divisionChartData);
    const chartStatusData = @json($statusChartData);

    const ctxDivision = document.getElementById('divisionChart');
    if (ctxDivision && chartDivisionData) {
        new Chart(ctxDivision, {
            type: 'bar',
            data: {
                labels: chartDivisionData.map(d => d.name),
                datasets: [
                    { label: 'Surat Masuk', data: chartDivisionData.map(d => d.surat_masuk), backgroundColor: 'rgba(52, 152, 219, 0.7)', borderColor: 'rgba(52, 152, 219, 1)', borderWidth: 1 },
                    { label: 'Surat Keluar', data: chartDivisionData.map(d => d.surat_keluar), backgroundColor: 'rgba(39, 174, 96, 0.7)', borderColor: 'rgba(39, 174, 96, 1)', borderWidth: 1 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    }

    const ctxStatus = document.getElementById('statusChart');
    if (ctxStatus && chartStatusData) {
        const statusColors = { 'baru': 'rgba(243, 156, 18, 0.7)', 'diterima': 'rgba(39, 174, 96, 0.7)', 'ditolak': 'rgba(231, 76, 60, 0.7)', 'diproses': 'rgba(52, 152, 219, 0.7)', 'selesai': 'rgba(155, 89, 182, 0.7)' };
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: Object.keys(chartStatusData),
                datasets: [{ data: Object.values(chartStatusData), backgroundColor: Object.keys(chartStatusData).map(k => statusColors[k] || 'rgba(149, 165, 166, 0.7)') }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // ==========================================
    // 2. CHART LAPORAN & BEBAN KERJA
    // ==========================================
    const workloadData = @json($workloadData);
    const workloadStatusCounts = @json($workloadStatusCounts);
    const reportData = { monthlyTrend: @json($monthlyTrend), divisionDistribution: @json($divisionDistribution) };

    if (document.getElementById('workloadAnalysisChart')) {
        new Chart(document.getElementById('workloadAnalysisChart'), {
            type: 'bar', data: { labels: workloadData.map(d => d.divisi_name), datasets: [
                { label: 'Surat Masuk', data: workloadData.map(d => d.surat_masuk), backgroundColor: 'rgba(52,152,219,0.8)' },
                { label: 'Surat Keluar', data: workloadData.map(d => d.surat_keluar), backgroundColor: 'rgba(39,174,96,0.8)' },
                { label: 'Belum Ditindak', data: workloadData.map(d => d.belum_ditindak), backgroundColor: 'rgba(243,156,18,0.8)' }
            ]}, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }

    if (document.getElementById('workloadStatusChart')) {
        new Chart(document.getElementById('workloadStatusChart'), {
            type: 'doughnut', data: { labels: Object.keys(workloadStatusCounts), datasets: [{ data: Object.values(workloadStatusCounts), backgroundColor: ['#27ae60','#f39c12','#e74c3c'], borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if (document.getElementById('monthlyTrendChart')) {
        new Chart(document.getElementById('monthlyTrendChart'), {
            type: 'line', data: { labels: reportData.monthlyTrend.labels, datasets: [
                { label: 'Surat Masuk', data: reportData.monthlyTrend.surat_masuk, borderColor: '#3498db', backgroundColor: 'rgba(52,152,219,0.1)', fill: true, tension: 0.3 },
                { label: 'Surat Keluar', data: reportData.monthlyTrend.surat_keluar, borderColor: '#27ae60', backgroundColor: 'rgba(39,174,96,0.1)', fill: true, tension: 0.3 }
            ]}, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    }

    if (document.getElementById('divisionDistributionChart')) {
        new Chart(document.getElementById('divisionDistributionChart'), {
            type: 'pie', data: { labels: reportData.divisionDistribution.map(d => d.name), datasets: [{ data: reportData.divisionDistribution.map(d => d.total), backgroundColor: ['#3498db','#27ae60','#f39c12','#e74c3c','#9b59b6','#1abc9c'] }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // ==========================================
    // 3. FILTER FUNCTIONS
    // ==========================================
    function loadSuratMasuk(search, divisi, status) {
        fetch(`{{ route('surat-masuk.index') }}?search=${encodeURIComponent(search||'')}&divisi=${encodeURIComponent(divisi||'')}&status=${encodeURIComponent(status||'')}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json()).then(data => {
            const tbody = document.getElementById('suratMasukTableBody');
            if (!tbody || !data.success) return;
            tbody.innerHTML = '';
            if (data.suratMasuk.length) {
                let no = 1;
                data.suratMasuk.forEach(item => {
                    const tanggal = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';
                    const divisiNama = item.divisi?.nama_divisi || item.nama_divisi || 'N/A';
                    const icon = getFileIconClass(item.format_file_id, 'fa-lg', item.format_file?.nama_format || '');
                    const statuses = { baru: 'warning', diterima: 'success', ditolak: 'danger', diproses: 'info', selesai: 'primary' };
                    tbody.innerHTML += `<tr data-id="${item.id}"><th>${no++}</th><td>${tanggal}</td><td>${item.pengirim||'N/A'}</td><td class="text-truncate" style="max-width:150px" title="${item.instruksi_disposisi||''}">${item.instruksi_disposisi||'-'}</td><td class="text-truncate" style="max-width:150px" title="${item.instruksi_tambahan||''}">${item.instruksi_tambahan||'-'}</td><td><span class="divisi-tag divisi-${item.divisi_id||0}">${divisiNama}</span></td><td><i class="${icon}"></i></td><td><span class="badge bg-${statuses[item.status]||'secondary'}">${item.status}</span></td><td><div class="btn-group btn-group-sm"><button class="btn btn-outline-primary" onclick="lihatDetailSuratMasuk(${item.id})"><i class="fas fa-eye"></i></button><button class="btn btn-outline-secondary" onclick="editSuratMasuk(${item.id})"><i class="fas fa-edit"></i></button><button class="btn btn-outline-danger" onclick="hapusSuratMasuk(${item.id},event)"><i class="fas fa-trash"></i></button></div></td></tr>`;
                });
            } else { tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>'; }
        }).catch(() => { document.getElementById('suratMasukTableBody').innerHTML = '<tr><td colspan="9" class="text-center text-danger">Gagal memuat data.</td></tr>'; });
    }

    document.getElementById('searchBtnMasuk')?.addEventListener('click', () => loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterDivisi').value, document.getElementById('filterStatus').value));
    document.getElementById('searchSuratMasuk')?.addEventListener('keyup', e => { if (e.key === 'Enter') loadSuratMasuk(e.target.value, document.getElementById('filterDivisi').value, document.getElementById('filterStatus').value); });
    document.getElementById('filterDivisi')?.addEventListener('change', () => loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterDivisi').value, document.getElementById('filterStatus').value));
    document.getElementById('filterStatus')?.addEventListener('change', () => loadSuratMasuk(document.getElementById('searchSuratMasuk').value, document.getElementById('filterDivisi').value, document.getElementById('filterStatus').value));

    // Filter Surat Keluar & Arsip (Sama seperti sebelumnya, pastikan ada di sini)
    function loadSuratKeluar(search, divisi, status) {
        fetch(`{{ route('surat-keluar.index') }}?search=${encodeURIComponent(search||'')}&divisi=${encodeURIComponent(divisi||'')}&status=${encodeURIComponent(status||'')}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json()).then(data => {
            const list = document.getElementById('suratKeluarList'); if (!list || !data.success) return; list.innerHTML = '';
            if (data.suratKeluar.length) {
                data.suratKeluar.forEach(item => {
                    const badge = `<span class="badge bg-${item.status === 'draft' ? 'draft' : (item.status === 'dikirim' ? 'dikirim' : 'diterima')}">${item.status}</span>`;
                    list.innerHTML += `<div class="list-group-item email-item"><div class="d-flex w-100 align-items-center"><div class="flex-grow-1"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1 email-subject">${item.penerima||'N/A'}</h6><small class="text-muted">${item.tanggal_kirim||''}</small></div><p class="mb-1 email-preview">${(item.perihal||'').substring(0,100)}</p><div class="email-meta mt-2"><span class="badge bg-light text-dark me-2">${item.divisi?.nama_divisi||'N/A'}</span>${badge}</div></div><div class="ms-3"><div class="btn-group btn-group-sm"><a href="/surat-keluar/${item.id}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a><button class="btn btn-outline-secondary" onclick="editSuratKeluar(${item.id})"><i class="fas fa-edit"></i></button><button class="btn btn-outline-danger" onclick="hapusSuratKeluar(${item.id},event)"><i class="fas fa-trash"></i></button></div></div></div></div>`;
                });
            } else { list.innerHTML = '<div class="list-group-item text-center p-5">Tidak ada data.</div>'; }
        });
    }
    document.getElementById('searchSuratKeluar')?.addEventListener('keyup', () => loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterDivisiKeluar')?.value, document.getElementById('filterStatusKeluar')?.value));
    document.getElementById('filterDivisiKeluar')?.addEventListener('change', () => loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterDivisiKeluar').value, document.getElementById('filterStatusKeluar')?.value));
    document.getElementById('filterStatusKeluar')?.addEventListener('change', () => loadSuratKeluar(document.getElementById('searchSuratKeluar').value, document.getElementById('filterDivisiKeluar')?.value, document.getElementById('filterStatusKeluar')?.value));

    function loadArsip(search, tahun, jenis) {
        fetch(`{{ route('arsip.index') }}?searchArsip=${encodeURIComponent(search||'')}&filterTahunArsip=${encodeURIComponent(tahun||'')}&filterJenisArsip=${encodeURIComponent(jenis||'')}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json()).then(data => {
            const tbody = document.getElementById('arsipTableBody'); if (!tbody) return; tbody.innerHTML = '';
            const items = data.arsip?.data || data.arsip || data.data || data;
            if (Array.isArray(items) && items.length) {
                let no = 1;
                items.forEach(item => {
                    const isPdf = (item.format_id == 1) || ((item.format||'').toLowerCase().includes('pdf'));
                    const fileActions = item.file_path ? `<div class="btn-group btn-group-sm"><a href="{{ asset('storage') }}/${item.file_path}" target="_blank" class="btn btn-outline-primary ${!isPdf?'disabled':''}"><i class="fas fa-eye"></i></a><a href="{{ asset('storage') }}/${item.file_path}" download class="btn btn-outline-secondary"><i class="fas fa-download"></i></a></div>` : '<span class="text-muted">No file</span>';
                    tbody.innerHTML += `<tr><th>${no++}</th><td>${item.nomor_surat||'-'}</td><td>${item.tanggal||'-'}</td><td>${item.perihal||'-'}</td><td class="text-truncate" style="max-width:150px">${item.keterangan||'-'}</td><td><span class="badge bg-${item.jenis_color||'secondary'}">${item.jenis||'-'}</span></td><td><i class="${getFileIconClass(item.format_id, 'fa-lg', item.format||'')}"></i></td><td>${item.ukuran||'-'}</td><td>${fileActions}</td></tr>`;
                });
            } else { tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>'; }
        });
    }
    document.getElementById('btnSearchArsip')?.addEventListener('click', () => loadArsip(document.getElementById('searchArsip').value, document.getElementById('filterTahunArsip').value, document.getElementById('filterJenisArsip').value));
    document.getElementById('searchArsip')?.addEventListener('keyup', e => { if (e.key === 'Enter') loadArsip(e.target.value, document.getElementById('filterTahunArsip').value, document.getElementById('filterJenisArsip').value); });
    document.getElementById('filterTahunArsip')?.addEventListener('change', () => loadArsip(document.getElementById('searchArsip').value, document.getElementById('filterTahunArsip').value, document.getElementById('filterJenisArsip').value));
    document.getElementById('filterJenisArsip')?.addEventListener('change', () => loadArsip(document.getElementById('searchArsip').value, document.getElementById('filterTahunArsip').value, document.getElementById('filterJenisArsip').value));
});

// ==========================================
// FUNGSI GLOBAL
// ==========================================

window.updateStatus = function() {
    const suratId = document.getElementById('currentSuratId')?.value;
    const status = document.getElementById('newStatusSelect')?.value;
    const keterangan = document.getElementById('statusKeterangan')?.value;

    if (!suratId || !status) return;
    if (!keterangan.trim()) {
        alert('Keterangan wajib diisi untuk riwayat proses.');
        return;
    }

    fetch(`/surat-masuk/${suratId}/update-status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'), 'Accept': 'application/json' },
        body: JSON.stringify({ id: suratId, status: status, keterangan: keterangan })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Status berhasil diperbarui');
            // Reset form setelah berhasil
            document.getElementById('statusKeterangan').value = '';
            // Refresh detail untuk muat riwayat baru
            setTimeout(() => lihatDetailSuratMasuk(suratId), 1000);
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
        }
    }).catch(error => { console.error('Error:', error); alert('Gagal menyimpan status.'); });
};

// Fungsi Lihat Detail Surat Masuk (Sekarang termasuk Riwayat Proses)
window.lihatDetailSuratMasuk = function(id) {
    document.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
    const detailSection = document.getElementById('suratDetailSection');
    if (detailSection) {
        detailSection.classList.add('active');
        document.getElementById('currentSuratId').value = id;
    }
    
    fetch(`/api/surat-masuk/${id}`, { headers: { 'Accept': 'application/json' } })
    .then(response => response.json())
    .then(data => {
        if (!data || !data.success || !data.surat) { alert('Gagal mengambil data surat'); return; }
        
        const surat = data.surat;
        const setText = (elId, value) => { const el = document.getElementById(elId); if (el) el.textContent = value || '-'; };
        
        setText('detailNomorSurat', surat.nomor_surat);
        setText('detailTanggal', surat.tanggal);
        setText('detailPengirim', surat.pengirim);
        setText('detailPerihal', surat.perihal);
        setText('detailDivisi', surat.divisi_name);
        setText('detailStatus', surat.status_name);
        
        const badgeEl = document.getElementById('detailStatusBadge');
        if (badgeEl) {
            const statusColors = { 'baru': 'warning', 'diterima': 'success', 'ditolak': 'danger', 'diproses': 'info', 'selesai': 'primary' };
            badgeEl.className = `badge bg-${statusColors[surat.status] || 'secondary'}`;
            badgeEl.textContent = surat.status_name;
        }
        
        const fileIcon = document.getElementById('detailFileIconContainer');
        if (fileIcon) {
            let iconClass = 'fas fa-file fa-7x text-secondary';
            if (surat.format_file_id == 1) iconClass = 'fas fa-file-pdf fa-7x text-danger';
            else if (surat.format_file_id == 2) iconClass = 'fas fa-file-word fa-7x text-primary';
            else if (surat.format_file_id == 3) iconClass = 'fas fa-file-excel fa-7x text-success';
            fileIcon.innerHTML = `<i class="${iconClass}"></i>`;
        }
        
        const fileLink = document.getElementById('detailFileLink');
        if (fileLink) {
            if (surat.file_path) { fileLink.href = `${window.location.origin}/storage/${surat.file_path}`; fileLink.style.display = 'inline-block'; fileLink.target = '_blank'; }
            else { fileLink.style.display = 'none'; }
        }

        // TAMBAHKAN: Render Riwayat Proses secara Dinamis
        const timelineContainer = detailSection.querySelector('.timeline');
        if (timelineContainer && data.riwayat) {
            timelineContainer.innerHTML = ''; // Kosongkan riwayat lama (hardcoded)
            
        if (data.riwayat.length > 0) {
            const markerColors = { 'Baru': '#f39c12', 'Diterima': '#27ae60', 'Ditolak': '#e74c3c', 'Diproses': '#3498db', 'Selesai': '#9b59b6' };
            data.riwayat.forEach(riw => {
                timelineContainer.innerHTML += `
                    <li class="timeline-item">
                        <div class="timeline-marker" style="background-color: ${markerColors[riw.status_name] || '#6c757d'}"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">${riw.status_name}</h6>
                            <p class="timeline-text">${riw.keterangan || '-'}</p>
                            <small class="text-muted d-block">Oleh: ${riw.user_name} • ${riw.tanggal}</small>
                        </div>
                    </li>`;
            });
        } else {
                timelineContainer.innerHTML = '<li class="timeline-item"><div class="timeline-marker"></div><div class="timeline-content"><p class="text-muted">Belum ada riwayat proses.</p></div></li>';
            }
        }
    }).catch(error => { console.error('Error:', error); alert('Terjadi kesalahan saat mengambil data surat'); });
};
</script>
@endpush