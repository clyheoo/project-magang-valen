<!-- Dashboard Section -->
<section id="dashboard" class="dashboard-section active">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard Overview</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-filter-dashboard active" data-filter="today">Hari Ini</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-filter-dashboard" data-filter="week">Minggu Ini</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-filter-dashboard" data-filter="month">Bulan Ini</button>
            </div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-1"></i> Export Dashboard
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card surat-masuk shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Surat Masuk</h6>
<h3 class="card-text" id="suratMasukCount">{{ $suratMasukCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x text-secondary"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-secondary">Total</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card surat-keluar shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Surat Keluar</h6>
<h3 class="card-text" id="suratKeluarCount">{{ $suratKeluarCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-paper-plane fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success">Total</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card belum-ditindak shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Belum Ditindak</h6>
<h3 class="card-text" id="belumDitindakCount">{{ $belumDitindakCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark">Perlu Perhatian</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card ratarata-waktu shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Rata-rata Waktu</h6>
<h3 class="card-text" id="avgWaktuCount">{{ $avgWaktuCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-danger">Hari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted">Total Laporan</h6>
<h3 class="card-text" id="totalLaporanCount">{{ $totalLaporanCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-danger">Dokumen</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted">Total Arsip</h6>
<h3 class="card-text" id="totalArsipCount">{{ $totalArsipCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-archive fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-info">File</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted">Total Pengguna</h6>
<h3 class="card-text" id="totalUsersCount">{{ $totalUsersCount ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-primary">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted">Efisiensi Sistem</h6>
<h3 class="card-text" id="efisiensiCount">{{ $efisiensiCount ?? 0 }}%</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success">Kinerja</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Distribusi Surat per Divisi</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="divisionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Status Surat Masuk</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush" id="recentActivities">
                        <!-- Activities will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Ringkasan Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
<h4 id="totalDocuments">{{ $totalDocuments ?? 0 }}</h4>
                            <small class="text-muted">Total Dokumen</small>
                        </div>
                        <div class="col-6">
<h4 id="activeUsers">{{ $activeUsers ?? 0 }}</h4>
                            <small class="text-muted">Pengguna Aktif</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
<h4 id="processedToday">{{ $processedToday ?? 0 }}</h4>
                            <small class="text-muted">Sedang Diproses</small>
                        </div>
                        <div class="col-6">
<h4 id="pendingItems">{{ $pendingItems ?? 0 }}</h4>
                            <small class="text-muted">Menunggu Proses</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
